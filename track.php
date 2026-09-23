<?php
/**
 * Link Counter - Track & Redirect
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.3.0
 */

if (!defined('WB_PATH')) {
    require('../../config.php');
}

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';
$settings_table = TABLE_PREFIX . 'mod_linkcounter_settings';

/**
 * Get safe redirect URL for blocked crawlers
 * Returns HTTP_REFERER if it's from the same domain, otherwise homepage
 */
function getSafeRedirectUrl() {
    if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
        $referer_host = parse_url($referer, PHP_URL_HOST);
        $current_host = parse_url(WB_URL, PHP_URL_HOST);

        // Only redirect to referer if it's from the same domain (security check)
        if ($referer_host === $current_host) {
            return $referer;
        }
    }
    // Fallback to homepage
    return WB_URL;
}

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

// Load module settings
$settings_result = $database->query("SELECT `setting_key`, `setting_value` FROM `$settings_table`");
$settings = array();
if ($settings_result && $settings_result->numRows() > 0) {
    while ($row = $settings_result->fetchRow(MYSQLI_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// === CRAWLER PROTECTION (OPTIONAL) ===
$skip_counter = false;

$crawler_protection_enabled = isset($settings['crawler_protection_enabled']) && $settings['crawler_protection_enabled'] == '1';
$min_delay = isset($settings['crawler_min_delay']) ? (int)$settings['crawler_min_delay'] : 500;
$crawler_action = isset($settings['crawler_action']) ? $settings['crawler_action'] : 'skip_count';

// Check if timestamps are provided and protection is enabled
if ($crawler_protection_enabled && isset($_GET['_t']) && !empty($_GET['_t'])) {

    // Decode timestamp parameter
    $encoded = $_GET['_t'];

    try {
        // Base64 decode
        $decoded = base64_decode($encoded, true);

        if ($decoded !== false) {
            // XOR decode (reverse the XOR encoding from frontend)
            $xor_key = 42;
            $xor_decoded = '';
            for ($i = 0; $i < strlen($decoded); $i++) {
                $xor_decoded .= chr(ord($decoded[$i]) ^ $xor_key);
            }

            // Split timestamps
            if (strpos($xor_decoded, '|') !== false) {
                list($pageLoadTime, $clickTime) = explode('|', $xor_decoded, 2);

                // Validate: Are these valid numbers?
                if (is_numeric($pageLoadTime) && is_numeric($clickTime)) {
                    $timeDiff = (int)$clickTime - (int)$pageLoadTime;

                    // Check if click was too fast (crawler behavior)
                    if ($timeDiff < $min_delay) {
                        // Crawler detected
                        error_log("LinkCounter: Potential crawler detected - ID $id, Time: {$timeDiff}ms");

                        if ($crawler_action == 'block') {
                            // Block and redirect to referring page
                            header('Location: ' . getSafeRedirectUrl());
                            exit();
                        } else {
                            // Skip counter increment but continue with redirect
                            $skip_counter = true;
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Invalid timestamp format - treat as normal click
        error_log("LinkCounter: Error decoding timestamp for ID $id: " . $e->getMessage());
    }
} elseif ($crawler_protection_enabled && !isset($_GET['_t'])) {
    // Protection enabled but no timestamp provided
    // This could be a direct call or crawler without JavaScript
    error_log("LinkCounter: No timestamp provided for ID $id (possible crawler or direct access)");

    if ($crawler_action == 'block') {
        // Block access without timestamp and redirect to referring page
        header('Location: ' . getSafeRedirectUrl());
        exit();
    } else {
        // Skip counting but allow redirect
        $skip_counter = true;
    }
}
// === END CRAWLER PROTECTION ===

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

// === EXIT NOTICE (OPTIONAL) ===
// Shown only for URLs on a foreign host. Counting happens on the second
// request (go=1), so visitors who turn back are not counted.
$target_host = '';
$is_external = false;
if ($download['link_type'] == 'url') {
    $target_host  = strtolower((string)parse_url($target_url, PHP_URL_HOST));
    $current_host = strtolower((string)parse_url(WB_URL, PHP_URL_HOST));
    $is_external  = ($target_host !== '' && $target_host !== $current_host);
}

if (!empty($download['exit_notice']) && $is_external && !isset($_GET['go'])) {
    $lang = (file_exists(WB_PATH . '/modules/linkcounter/languages/' . LANGUAGE . '.php'))
        ? LANGUAGE
        : 'EN';
    require_once(WB_PATH . '/modules/linkcounter/languages/' . $lang . '.php');

    // Carry the crawler timestamp over so the second request is judged the same way
    $continue_url = WB_URL . '/modules/linkcounter/track.php?id=' . $id . '&go=1';
    if (isset($_GET['_t']) && is_string($_GET['_t']) && $_GET['_t'] !== '') {
        $continue_url .= '&_t=' . rawurlencode($_GET['_t']);
    }
    $back_url = getSafeRedirectUrl();

    $notice_text = isset($settings['exit_notice_text']) ? trim($settings['exit_notice_text']) : '';
    if ($notice_text === '') {
        $notice_text = $MOD_LINKCOUNTER['EXIT_NOTICE_DEFAULT_TEXT'];
    }

    require(dirname(__FILE__) . '/exit_notice.php');
    exit();
}
// === END EXIT NOTICE ===

// Increment counter (only if not skipped by crawler protection)
if (!$skip_counter) {
    $update_sql = "UPDATE `$table` SET `counter` = `counter` + 1 WHERE `id` = $id";
    $database->query($update_sql);
}

// Redirect to target URL
header('Location: ' . $target_url, true, 302);
exit();

?>
