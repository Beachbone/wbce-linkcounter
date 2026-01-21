<?php
/**
 * Link Counter - Uninstallation
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.0.0
 */

if(!defined('WB_PATH')) exit("Cannot access this file directly ".__FILE__);

global $database;

// Drop module table
$table = TABLE_PREFIX . 'mod_linkcounter';
$database->query("DROP TABLE IF EXISTS `$table`");

// Remove Droplets
$droplet_table = TABLE_PREFIX . 'mod_droplets';

// Check if droplets table exists
$check_droplets = $database->query("SHOW TABLES LIKE '$droplet_table'");

if ($check_droplets->numRows() > 0) {
    // Remove droplets
    $droplet_names = array('LinkCounter', 'LinkCounterStats');

    foreach ($droplet_names as $name) {
        $escaped_name = $database->escapeString($name);
        $database->query("DELETE FROM `$droplet_table` WHERE `name` = '$escaped_name'");
    }
}

?>
