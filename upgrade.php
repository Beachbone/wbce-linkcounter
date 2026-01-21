<?php
/**
 * Link Counter - Upgrade
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.0.0
 */

if(!defined('WB_PATH')) exit("Cannot access this file directly ".__FILE__);

global $database;

$table = TABLE_PREFIX . 'mod_linkcounter';

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

?>
