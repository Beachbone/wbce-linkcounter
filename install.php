<?php
/**
 * Link Counter - Installation
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.1.0
 */

if(!defined('WB_PATH')) exit("Cannot access this file directly ".__FILE__);

global $database;

// Create module table
$table = TABLE_PREFIX . 'mod_linkcounter';

// Check if table already exists
$check_table = $database->query("SHOW TABLES LIKE '$table'");
if ($check_table->numRows() == 0) {
    $sql = "CREATE TABLE IF NOT EXISTS `$table` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL DEFAULT '',
        `link_type` ENUM('url', 'page') NOT NULL DEFAULT 'url',
        `url` VARCHAR(500) NOT NULL DEFAULT '',
        `page_id` INT(11) DEFAULT NULL,
        `description` TEXT,
        `counter` INT(11) NOT NULL DEFAULT 0,
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `active` TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`),
        INDEX `idx_active` (`active`),
        INDEX `idx_counter` (`counter`),
        INDEX `idx_link_type` (`link_type`),
        INDEX `idx_page_id` (`page_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    $database->query($sql);
}

// Create settings table for crawler protection
$settings_table = TABLE_PREFIX . 'mod_linkcounter_settings';
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

// Install Droplets
$droplet_table = TABLE_PREFIX . 'mod_droplets';

// Check if droplets table exists (droplet module must be installed)
$check_droplets = $database->query("SHOW TABLES LIKE '$droplet_table'");

if ($check_droplets->numRows() > 0) {

    // Droplet 1: [[LinkCounter]]
    $droplet_name = 'LinkCounter';
    $escaped_droplet_name = $database->escapeString($droplet_name);
    $check_droplet = $database->query("SELECT * FROM `$droplet_table` WHERE `name` = '$escaped_droplet_name'");

    if ($check_droplet->numRows() == 0) {
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

// Load frontend JavaScript once (for crawler protection)
static $js_loaded = false;
$js_output = '';
if (!$js_loaded) {
    $js_output = '<script src="' . WB_URL . '/modules/linkcounter/js/frontend.js"></script>';
    $js_loaded = true;
}

// Return link with data-attribute for crawler protection (URL is already safe as ID is cast to int)
return $js_output . '<a href="' . htmlspecialchars($track_url) . '" title="' . $link_text . '" data-linkcounter-id="' . (int)$id . '" class="linkcounter-link">' . $link_text . '</a>';
EOD;

        $description = 'Generates a tracked link. Parameters: id (required)';
        $escaped_code = $database->escapeString($droplet_code);
        $escaped_description = $database->escapeString($description);
        $escaped_comments = $database->escapeString('[[linkcounter?id=1]]');

        $database->query(
            "INSERT INTO `$droplet_table` (`name`, `code`, `description`, `active`, `comments`)
             VALUES ('$escaped_droplet_name', '$escaped_code', '$escaped_description', 1, '$escaped_comments')"
        );
    }

    // Droplet 2: [[LinkCounterStats]]
    $droplet_name = 'LinkCounterStats';
    $escaped_droplet_name = $database->escapeString($droplet_name);
    $check_droplet = $database->query("SELECT * FROM `$droplet_table` WHERE `name` = '$escaped_droplet_name'");

    if ($check_droplet->numRows() == 0) {
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
    $output .= '<td><a href="' . htmlspecialchars($track_url) . '" class="btn btn-sm btn-primary linkcounter-link" data-linkcounter-id="' . (int)$row['id'] . '">Link</a></td>';
    $output .= '</tr>';
}

$output .= '</tbody>';
$output .= '</table>';
$output .= '</div>';

return $output;
EOD;

        $description = 'Displays link statistics table. Parameters: limit (optional, default: 10)';
        $escaped_code = $database->escapeString($droplet_code);
        $escaped_description = $database->escapeString($description);
        $escaped_comments = $database->escapeString('[[linkcounterstats?limit=10]]');

        $database->query(
            "INSERT INTO `$droplet_table` (`name`, `code`, `description`, `active`, `comments`)
             VALUES ('$escaped_droplet_name', '$escaped_code', '$escaped_description', 1, '$escaped_comments')"
        );
    }
}

?>
