<?php
/**
 * Link Counter - Reset Counter
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.1.0
 */

if (!defined('WB_PATH')) {
    require('../../config.php');
}

require_once(WB_PATH . '/framework/class.admin.php');

$admin = new admin('Admintools', 'admintools', false);

// Load language file
$lang = (file_exists(WB_PATH . '/modules/linkcounter/languages/' . LANGUAGE . '.php'))
    ? LANGUAGE
    : 'EN';
require_once(WB_PATH . '/modules/linkcounter/languages/' . $lang . '.php');

// Check CSRF token
if (!$admin->checkFTAN()) {
    $_SESSION['linkcounter_error'] = $MOD_LINKCOUNTER['ERROR_SECURITY'];
    header('Location: ' . ADMIN_URL . '/admintools/tool.php?tool=linkcounter');
    exit();
}

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Get and validate ID from POST
$id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    $_SESSION['linkcounter_error'] = $MOD_LINKCOUNTER['ERROR_NOT_FOUND'];
    header('Location: ' . ADMIN_URL . '/admintools/tool.php?tool=linkcounter');
    exit();
}

// Check if download exists
$result = $database->query("SELECT * FROM `$table` WHERE `id` = $id");

if ($result->numRows() == 0) {
    $_SESSION['linkcounter_error'] = $MOD_LINKCOUNTER['ERROR_NOT_FOUND'];
    header('Location: ' . ADMIN_URL . '/admintools/tool.php?tool=linkcounter');
    exit();
}

// Reset counter
$reset_result = $database->query("UPDATE `$table` SET `counter` = 0 WHERE `id` = $id");

if ($reset_result) {
    $_SESSION['linkcounter_success'] = $MOD_LINKCOUNTER['SUCCESS_COUNTER_RESET'];
} else {
    $_SESSION['linkcounter_error'] = 'Failed to reset counter.';
}

// Redirect back to edit page
header('Location: ' . ADMIN_URL . '/admintools/tool.php?tool=linkcounter&action=edit&id=' . $id);
exit();

?>
