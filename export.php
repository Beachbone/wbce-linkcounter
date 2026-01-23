<?php
/**
 * Link Counter - CSV Export
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.1.0
 */

// Start output buffering to prevent any HTML output
ob_start();

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
    ob_end_clean(); // Clear buffer before error
    $admin->print_error($MOD_LINKCOUNTER['ERROR_SECURITY']);
    exit();
}

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Get all downloads
$result = $database->query("SELECT * FROM `$table` ORDER BY `id` ASC");

// Clear any buffered output before sending CSV headers
ob_end_clean();

// Set headers for CSV download
$filename = $MOD_LINKCOUNTER['EXPORT_FILENAME'] . '-' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// CSV headers
$headers = array(
    'ID',
    $MOD_LINKCOUNTER['TH_TITLE'],
    $MOD_LINKCOUNTER['LABEL_LINK_TYPE'],
    $MOD_LINKCOUNTER['TH_URL'],
    $MOD_LINKCOUNTER['TH_DESCRIPTION'],
    $MOD_LINKCOUNTER['TH_COUNTER'],
    $MOD_LINKCOUNTER['TH_STATUS'],
    $MOD_LINKCOUNTER['TH_CREATED'],
    'Modified'
);

fputcsv($output, $headers, ';', '"', '');

// Add data rows
while ($row = $result->fetchRow(MYSQLI_ASSOC)) {
    // Determine link type and URL display
    $link_type_display = '';
    $url_display = '';

    if ($row['link_type'] == 'page' && !empty($row['page_id'])) {
        $link_type_display = $MOD_LINKCOUNTER['LINK_TYPE_PAGE'];
        $url_display = $MOD_LINKCOUNTER['LINK_TYPE_PAGE'] . ' (ID: ' . $row['page_id'] . ')';
    } else {
        $link_type_display = $MOD_LINKCOUNTER['LINK_TYPE_URL'];
        $url_display = $row['url'];
    }

    $csv_row = array(
        $row['id'],
        $row['title'],
        $link_type_display,
        $url_display,
        $row['description'],
        $row['counter'],
        $row['active'] ? $MOD_LINKCOUNTER['STATUS_ACTIVE'] : $MOD_LINKCOUNTER['STATUS_INACTIVE'],
        $row['created'],
        $row['modified']
    );

    fputcsv($output, $csv_row, ';', '"', '');
}

fclose($output);
exit();

?>
