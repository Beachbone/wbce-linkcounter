<?php
/**
 * Link Counter - Tool Entry Point
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.0.0
 */

if(!defined('WB_PATH')) exit("Cannot access this file directly ".__FILE__);

// Get action parameter
$action = isset($_GET['action']) ? $_GET['action'] : 'overview';

// Route to appropriate page based on action
switch ($action) {
    case 'add':
    case 'edit':
        // Show add/edit form
        require_once(dirname(__FILE__) . '/add.php');
        break;

    case 'export':
        // Export CSV
        require_once(dirname(__FILE__) . '/export.php');
        break;

    case 'overview':
    default:
        // Show overview page
        require_once(dirname(__FILE__) . '/overview.php');
        break;
}

?>
