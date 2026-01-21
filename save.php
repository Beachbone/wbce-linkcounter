<?php
/**
 * Link Counter - Save Handler
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.0.0
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
    $admin->print_error($MOD_LINKCOUNTER['ERROR_SECURITY']);
    exit();
}

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Get and validate POST data
$id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$link_type = isset($_POST['link_type']) && in_array($_POST['link_type'], array('url', 'page')) ? $_POST['link_type'] : 'url';
$url = isset($_POST['url']) ? trim($_POST['url']) : '';
$page_id = isset($_POST['page_id']) && is_numeric($_POST['page_id']) ? (int)$_POST['page_id'] : null;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$active = isset($_POST['active']) && $_POST['active'] == 1 ? 1 : 0;

// Validation
$errors = array();

// Validate title
if (empty($title)) {
    $errors[] = $MOD_LINKCOUNTER['ERROR_TITLE_EMPTY'];
}

// Validate based on link type
if ($link_type == 'url') {
    // Validate URL
    if (empty($url)) {
        $errors[] = $MOD_LINKCOUNTER['ERROR_URL_EMPTY'];
    } else {
        // Security: Check for dangerous URL schemes
        $dangerous_schemes = array('javascript:', 'data:', 'file:', 'vbscript:', 'about:');
        $url_lower = strtolower(trim($url));

        foreach ($dangerous_schemes as $scheme) {
            if (strpos($url_lower, $scheme) === 0) {
                $errors[] = 'URL contains a dangerous protocol. Only http://, https:// or relative URLs are allowed.';
                break;
            }
        }

        // Only validate with filter_var if no dangerous scheme was found
        if (empty($errors)) {
            // Check if it's a valid URL or a relative URL
            $is_absolute = (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0);
            $is_relative = (strpos($url, '/') === 0);

            if (!$is_relative && !$is_absolute) {
                // Not relative or absolute - normalize by adding http://
                // This handles cases like "www.google.com" or "google.com"
                $url = 'http://' . $url;
            }

            // Now validate the (potentially normalized) URL
            if (!$is_relative) {
                if (!filter_var($url, FILTER_VALIDATE_URL)) {
                    $errors[] = $MOD_LINKCOUNTER['ERROR_URL_INVALID'];
                } else {
                    // Additional check: ensure it's http or https
                    $parsed = parse_url($url);
                    if (!isset($parsed['scheme']) || !in_array($parsed['scheme'], array('http', 'https'))) {
                        $errors[] = 'Only http:// and https:// URLs are allowed.';
                    }
                }
            }
        }
    }
    // Clear page_id when using URL
    $page_id = null;
} else {
    // Validate page_id
    if (empty($page_id)) {
        $errors[] = 'Please select a page.';
    }
    // Clear url when using page
    $url = '';
}

// If validation errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['linkcounter_errors'] = $errors;
    $redirect_url = WB_URL . '/modules/linkcounter/add.php';
    if ($id > 0) {
        $redirect_url .= '?id=' . $id;
    }
    header('Location: ' . $redirect_url);
    exit();
}

// Save to database
if ($id > 0) {
    // Update existing link
    // Escape values manually for WBCE compatibility
    $escaped_title = $database->escapeString($title);
    $escaped_link_type = $database->escapeString($link_type);
    $escaped_url = $database->escapeString($url);
    $escaped_page_id = ($page_id === null) ? 'NULL' : (int)$page_id;
    $escaped_description = $database->escapeString($description);
    $escaped_active = (int)$active;
    $escaped_id = (int)$id;

    $sql = "UPDATE `$table` SET
            `title` = '$escaped_title',
            `link_type` = '$escaped_link_type',
            `url` = '$escaped_url',
            `page_id` = $escaped_page_id,
            `description` = '$escaped_description',
            `active` = $escaped_active
            WHERE `id` = $escaped_id";

    $result = $database->query($sql);

    if ($result) {
        $_SESSION['linkcounter_success'] = $MOD_LINKCOUNTER['SUCCESS_SAVED'];
    } else {
        $_SESSION['linkcounter_error'] = $MOD_LINKCOUNTER['ERROR_SAVE_FAILED'];
    }
} else {
    // Insert new link
    // Escape values manually for WBCE compatibility
    $escaped_title = $database->escapeString($title);
    $escaped_link_type = $database->escapeString($link_type);
    $escaped_url = $database->escapeString($url);
    $escaped_page_id = ($page_id === null) ? 'NULL' : (int)$page_id;
    $escaped_description = $database->escapeString($description);
    $escaped_active = (int)$active;

    $sql = "INSERT INTO `$table` (`title`, `link_type`, `url`, `page_id`, `description`, `active`)
            VALUES ('$escaped_title', '$escaped_link_type', '$escaped_url', $escaped_page_id, '$escaped_description', $escaped_active)";

    $result = $database->query($sql);

    if ($result) {
        $_SESSION['linkcounter_success'] = $MOD_LINKCOUNTER['SUCCESS_SAVED'];
    } else {
        $_SESSION['linkcounter_error'] = $MOD_LINKCOUNTER['ERROR_SAVE_FAILED'];
    }
}

// Redirect back to overview
header('Location: ' . ADMIN_URL . '/admintools/tool.php?tool=linkcounter');
exit();

?>
