<?php
/**
 * Link Counter - Delete Handler
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

// Check if link exists
$result = $database->query("SELECT * FROM `$table` WHERE `id` = $id");

if ($result->numRows() == 0) {
    $_SESSION['linkcounter_error'] = $MOD_LINKCOUNTER['ERROR_NOT_FOUND'];
    header('Location: ' . ADMIN_URL . '/admintools/tool.php?tool=linkcounter');
    exit();
}

// Delete link
$delete_result = $database->query("DELETE FROM `$table` WHERE `id` = $id");

if ($delete_result) {
    $_SESSION['linkcounter_success'] = $MOD_LINKCOUNTER['SUCCESS_DELETED'];
} else {
    $_SESSION['linkcounter_error'] = $MOD_LINKCOUNTER['ERROR_DELETE_FAILED'];
}

// Redirect back to overview
header('Location: ' . ADMIN_URL . '/admintools/tool.php?tool=linkcounter');
exit();

?>
