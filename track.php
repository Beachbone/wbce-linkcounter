<?php
/**
 * Link Counter - Track & Redirect
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.0.0
 */

if (!defined('WB_PATH')) {
    require('../../config.php');
}

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Get and validate ID parameter
$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

// Redirect to homepage if ID is invalid
if ($id <= 0) {
    header('Location: ' . WB_URL);
    exit();
}

// Get link from database
$sql = "SELECT * FROM `$table` WHERE `id` = $id AND `active` = 1";
$result = $database->query($sql);

// Redirect to homepage if link not found or inactive
if ($result->numRows() == 0) {
    header('Location: ' . WB_URL);
    exit();
}

$download = $result->fetchRow(MYSQLI_ASSOC);

// Increment counter
$update_sql = "UPDATE `$table` SET `counter` = `counter` + 1 WHERE `id` = $id";
$database->query($update_sql);

// Determine target URL based on link type
if ($download['link_type'] == 'page' && !empty($download['page_id'])) {
    // Internal page - get page link from WBCE
    $pages_table = TABLE_PREFIX . 'pages';
    $page_id = (int)$download['page_id'];
    $page_sql = "SELECT `link` FROM `$pages_table` WHERE `page_id` = $page_id AND `visibility` != 'deleted'";
    $page_result = $database->query($page_sql);

    if ($page_result->numRows() > 0) {
        $page = $page_result->fetchRow(MYSQLI_ASSOC);
        $target_url = WB_URL . PAGES_DIRECTORY . $page['link'] . PAGE_EXTENSION;
    } else {
        // Page not found, redirect to homepage
        header('Location: ' . WB_URL);
        exit();
    }
} else {
    // External URL
    $target_url = $download['url'];

    // Security: Validate URL to prevent open redirect attacks
    // Check for dangerous URL schemes
    $dangerous_schemes = array('javascript:', 'data:', 'file:', 'vbscript:', 'about:');
    $url_lower = strtolower(trim($target_url));

    foreach ($dangerous_schemes as $scheme) {
        if (strpos($url_lower, $scheme) === 0) {
            // Dangerous URL detected, redirect to homepage
            error_log("LinkCounter: Blocked dangerous URL scheme in link ID $id: $target_url");
            header('Location: ' . WB_URL);
            exit();
        }
    }

    // Normalize URL format
    if (strpos($target_url, 'http://') === 0 || strpos($target_url, 'https://') === 0) {
        // Already a valid absolute URL - use as is
        // URL already validated above for dangerous schemes
    } elseif (strpos($target_url, '/') === 0) {
        // Relative URL starting with / - prepend WB_URL
        $target_url = WB_URL . $target_url;
    } else {
        // URL without protocol (e.g., www.google.com, google.com)
        // This handles legacy entries or edge cases
        // Add http:// prefix to make it a valid external URL
        $target_url = 'http://' . $target_url;

        // Re-validate the normalized URL
        if (!filter_var($target_url, FILTER_VALIDATE_URL)) {
            error_log("LinkCounter: Invalid URL format after normalization in link ID $id: $target_url");
            header('Location: ' . WB_URL);
            exit();
        }
    }

    // Final validation: ensure URL starts with http:// or https://
    if (strpos($target_url, 'http://') !== 0 && strpos($target_url, 'https://') !== 0) {
        error_log("LinkCounter: Invalid URL format in link ID $id: $target_url");
        header('Location: ' . WB_URL);
        exit();
    }
}

// Redirect to target URL
header('Location: ' . $target_url, true, 302);
exit();

?>
