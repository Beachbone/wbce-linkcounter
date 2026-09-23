<?php
/**
 * Link Counter - Upgrade
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.3.0
 */

if(!defined('WB_PATH')) exit("Cannot access this file directly ".__FILE__);

global $database;

$table = TABLE_PREFIX . 'mod_linkcounter';
$settings_table = TABLE_PREFIX . 'mod_linkcounter_settings';

// Get current installed version from database
$addon_table = TABLE_PREFIX . 'addons';
$version_query = $database->query("SELECT `version` FROM `$addon_table` WHERE `directory` = 'linkcounter'");
if ($version_query && $version_query->numRows() > 0) {
    $version_row = $version_query->fetchRow(MYSQLI_ASSOC);
    $current_version = $version_row['version'];
} else {
    $current_version = '0.9.9';
}

// Version 0.9.8 is assumed as base version
// Only upgrades from 0.9.8+ are handled here

// ========================================
// UPGRADE LOGIC BY VERSION
// ========================================

// Ensure table structure is correct (for any version)
$check_table = $database->query("SHOW TABLES LIKE '$table'");
if ($check_table->numRows() == 0) {
    // Table doesn't exist, run install
    require_once(WB_PATH . '/modules/linkcounter/install.php');
} else {
    // Ensure indexes exist (for versions before 0.9.13)
    if (version_compare($current_version, '0.9.13', '<')) {
        $indexes_to_check = array('idx_active', 'idx_counter', 'idx_link_type', 'idx_page_id');

        foreach ($indexes_to_check as $index_name) {
            // Check if index exists
            $check_index = $database->query("SHOW INDEX FROM `$table` WHERE Key_name = '$index_name'");
            if ($check_index->numRows() == 0) {
                // Create index based on name
                switch ($index_name) {
                    case 'idx_active':
                        $database->query("CREATE INDEX `idx_active` ON `$table` (`active`)");
                        break;
                    case 'idx_counter':
                        $database->query("CREATE INDEX `idx_counter` ON `$table` (`counter`)");
                        break;
                    case 'idx_link_type':
                        $database->query("CREATE INDEX `idx_link_type` ON `$table` (`link_type`)");
                        break;
                    case 'idx_page_id':
                        $database->query("CREATE INDEX `idx_page_id` ON `$table` (`page_id`)");
                        break;
                }
            }
        }
    }
}

// ========================================
// UPGRADE TO VERSION 1.2.0
// ========================================
// Add open_target column if missing (default '_self' = same window)
$col_check = $database->query("SHOW COLUMNS FROM `$table` LIKE 'open_target'");
if ($col_check->numRows() == 0) {
    $database->query("ALTER TABLE `$table` ADD COLUMN `open_target` ENUM('_self','_blank') NOT NULL DEFAULT '_self' AFTER `active`");
}

// ========================================
// UPGRADE TO VERSION 1.1.0
// ========================================
// Features added in version 1.1.0:
// - Crawler protection with time-based detection
// - Settings table for configuration
// - Updated droplets with data-attributes and frontend.js
// - Referer-based redirect on blocked crawlers

// Create settings table for crawler protection (version 1.1.0+)
$check_settings = $database->query("SHOW TABLES LIKE '$settings_table'");
if ($check_settings->numRows() == 0) {
    $sql = "CREATE TABLE IF NOT EXISTS `$settings_table` (
        `setting_key` VARCHAR(50) NOT NULL,
        `setting_value` VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $database->query($sql);

    // Insert default values for crawler protection
    $database->query("INSERT INTO `$settings_table` (`setting_key`, `setting_value`)
                      VALUES ('crawler_protection_enabled', '0')");
    $database->query("INSERT INTO `$settings_table` (`setting_key`, `setting_value`)
                      VALUES ('crawler_min_delay', '500')");
    $database->query("INSERT INTO `$settings_table` (`setting_key`, `setting_value`)
                      VALUES ('crawler_action', 'skip_count')");
}

// ========================================
// UPGRADE TO VERSION 1.3.0
// ========================================
// Exit notice: per-link toggle + global notice text
$col_check = $database->query("SHOW COLUMNS FROM `$table` LIKE 'exit_notice'");
if ($col_check->numRows() == 0) {
    $database->query("ALTER TABLE `$table` ADD COLUMN `exit_notice` TINYINT(1) NOT NULL DEFAULT 0 AFTER `open_target`");
}
$database->query("INSERT IGNORE INTO `$settings_table` (`setting_key`, `setting_value`)
                  VALUES ('exit_notice_text', '')");

// Update droplet codes to include data-attributes for crawler protection (version 1.1.0+)
$droplet_table = TABLE_PREFIX . 'mod_droplets';
$check_droplets = $database->query("SHOW TABLES LIKE '$droplet_table'");

if ($check_droplets->numRows() > 0) {
    // Update LinkCounter droplet
    $droplet_name = 'LinkCounter';
    $escaped_droplet_name = $database->escapeString($droplet_name);
    $check_droplet = $database->query("SELECT * FROM `$droplet_table` WHERE `name` = '$escaped_droplet_name'");

    if ($check_droplet->numRows() > 0) {
        $droplet = $check_droplet->fetchRow(MYSQLI_ASSOC);
        // Update droplet if open_target support is missing
        if (strpos($droplet['code'], 'open_target') === false) {
            $droplet_code = <<<'EOD'
/**
 * Droplet: LinkCounter
 *
 * Generates a tracked link using the title from database
 *
 * Parameters:
 * - id: Link ID (required)
 *
 * Example: [[linkcounter?id=1]]
 */

if (!isset($id) || empty($id)) {
    return '<span class="error">Link ID is required</span>';
}

// Validate ID
$id = (int)$id;
if ($id <= 0) {
    return '<span class="error">Invalid Link ID</span>';
}

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Get link from database
$sql = "SELECT * FROM `$table` WHERE `id` = $id AND `active` = 1";
$result = $database->query($sql);

if ($result->numRows() == 0) {
    return '<span class="error">Link not found or inactive</span>';
}

$download = $result->fetchRow(MYSQLI_ASSOC);

// Use title from database as link text
$link_text = htmlspecialchars($download['title']);

// Generate track URL
$track_url = WB_URL . '/modules/linkcounter/track.php?id=' . (int)$id;

$open_target = isset($download['open_target']) ? $download['open_target'] : '_self';
$target_attr = ($open_target === '_blank') ? ' target="_blank" rel="noopener noreferrer"' : '';

// Load frontend JavaScript once (for crawler protection)
static $js_loaded = false;
$js_output = '';
if (!$js_loaded) {
    $js_output = '<script src="' . WB_URL . '/modules/linkcounter/js/frontend.js"></script>';
    $js_loaded = true;
}

// Return link with data-attribute for crawler protection (URL is already safe as ID is cast to int)
return $js_output . '<a href="' . htmlspecialchars($track_url) . '"' . $target_attr . ' title="' . $link_text . '" data-linkcounter-id="' . (int)$id . '" class="linkcounter-link">' . $link_text . '</a>';
EOD;

            $escaped_code = $database->escapeString($droplet_code);
            $database->query("UPDATE `$droplet_table` SET `code` = '$escaped_code' WHERE `name` = '$escaped_droplet_name'");
        }
    }

    // Update LinkCounterStats droplet
    $droplet_name = 'LinkCounterStats';
    $escaped_droplet_name = $database->escapeString($droplet_name);
    $check_droplet = $database->query("SELECT * FROM `$droplet_table` WHERE `name` = '$escaped_droplet_name'");

    if ($check_droplet->numRows() > 0) {
        $droplet = $check_droplet->fetchRow(MYSQLI_ASSOC);
        // Update droplet if open_target support is missing
        if (strpos($droplet['code'], 'open_target') === false) {
            $droplet_code = <<<'EOD'
/**
 * Droplet: LinkCounterStats
 *
 * Displays link statistics as a table
 *
 * Parameters:
 * - limit: Maximum number of links to show (optional, default: 10)
 *
 * Example: [[linkcounterstats?limit=10]]
 */

$limit = isset($limit) && is_numeric($limit) ? (int)$limit : 10;

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Load frontend JavaScript once (for crawler protection)
static $js_loaded = false;
$js_output = '';
if (!$js_loaded) {
    $js_output = '<script src="' . WB_URL . '/modules/linkcounter/js/frontend.js"></script>';
    $js_loaded = true;
}

// Get top downloads
$sql = "SELECT * FROM `$table` WHERE `active` = 1 ORDER BY `counter` DESC LIMIT $limit";
$result = $database->query($sql);

if ($result->numRows() == 0) {
    return $js_output . '<p class="info">No links available yet.</p>';
}

// Build table
$output = $js_output;
$output .= '<div class="linkcounter-stats">';
$output .= '<table class="table table-striped">';
$output .= '<thead>';
$output .= '<tr>';
$output .= '<th>Title</th>';
$output .= '<th>Description</th>';
$output .= '<th>Clicks</th>';
$output .= '<th>Link</th>';
$output .= '</tr>';
$output .= '</thead>';
$output .= '<tbody>';

while ($row = $result->fetchRow(MYSQLI_ASSOC)) {
    $output .= '<tr>';
    $output .= '<td>' . htmlspecialchars($row['title']) . '</td>';
    $output .= '<td>' . htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : '') . '</td>';
    $output .= '<td><strong>' . number_format($row['counter'], 0, ',', '.') . '</strong></td>';
    $track_url = WB_URL . '/modules/linkcounter/track.php?id=' . (int)$row['id'];
    $open_target = isset($row['open_target']) ? $row['open_target'] : '_self';
    $target_attr = ($open_target === '_blank') ? ' target="_blank" rel="noopener noreferrer"' : '';
    $output .= '<td><a href="' . htmlspecialchars($track_url) . '"' . $target_attr . ' class="btn btn-sm btn-primary linkcounter-link" data-linkcounter-id="' . (int)$row['id'] . '">Link</a></td>';
    $output .= '</tr>';
}

$output .= '</tbody>';
$output .= '</table>';
$output .= '</div>';

return $output;
EOD;

            $escaped_code = $database->escapeString($droplet_code);
            $database->query("UPDATE `$droplet_table` SET `code` = '$escaped_code' WHERE `name` = '$escaped_droplet_name'");
        }
    }
}

?>
