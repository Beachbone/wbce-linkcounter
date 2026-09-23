<?php
/**
 * Link Counter - Settings (Crawler Protection, Exit Notice)
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.3.0
 */

if(!defined('WB_PATH')) exit("Cannot access this file directly ".__FILE__);

require_once(WB_PATH . '/framework/class.admin.php');

if (!isset($admin)) {
    $admin = new admin('Admintools', 'admintools', false);
}

// Load language file
$lang = (file_exists(WB_PATH . '/modules/linkcounter/languages/' . LANGUAGE . '.php'))
    ? LANGUAGE
    : 'EN';
require_once(WB_PATH . '/modules/linkcounter/languages/' . $lang . '.php');

global $database;
$settings_table = TABLE_PREFIX . 'mod_linkcounter_settings';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {

    // Check CSRF token
    if (!$admin->checkFTAN()) {
        $_SESSION['linkcounter_error'] = $MOD_LINKCOUNTER['ERROR_SECURITY'];
    } else {
        // Get POST data
        $crawler_protection_enabled = isset($_POST['crawler_protection_enabled']) ? '1' : '0';
        $crawler_min_delay = isset($_POST['crawler_min_delay']) && is_numeric($_POST['crawler_min_delay'])
            ? (int)$_POST['crawler_min_delay']
            : 500;
        $crawler_action = isset($_POST['crawler_action']) && in_array($_POST['crawler_action'], array('skip_count', 'block'))
            ? $_POST['crawler_action']
            : 'skip_count';

        // Validate minimum delay (must be between 100 and 10000 ms)
        if ($crawler_min_delay < 100) {
            $crawler_min_delay = 100;
        }
        if ($crawler_min_delay > 10000) {
            $crawler_min_delay = 10000;
        }

        // Update settings in database
        $database->query("UPDATE `$settings_table` SET `setting_value` = '$crawler_protection_enabled'
                          WHERE `setting_key` = 'crawler_protection_enabled'");
        $database->query("UPDATE `$settings_table` SET `setting_value` = '$crawler_min_delay'
                          WHERE `setting_key` = 'crawler_min_delay'");
        $database->query("UPDATE `$settings_table` SET `setting_value` = '$crawler_action'
                          WHERE `setting_key` = 'crawler_action'");

        $exit_notice_text = isset($_POST['exit_notice_text']) ? trim((string)$_POST['exit_notice_text']) : '';
        $exit_notice_text = str_replace(array("\r\n", "\r"), "\n", $exit_notice_text);
        if (mb_strlen($exit_notice_text) > 255) {
            $exit_notice_text = mb_substr($exit_notice_text, 0, 255);
        }
        $escaped_exit_text = $database->escapeString($exit_notice_text);
        $database->query("INSERT INTO `$settings_table` (`setting_key`, `setting_value`)
                          VALUES ('exit_notice_text', '$escaped_exit_text')
                          ON DUPLICATE KEY UPDATE `setting_value` = '$escaped_exit_text'");

        $_SESSION['linkcounter_success'] = $MOD_LINKCOUNTER['SUCCESS_SETTINGS_SAVED'];

        // Redirect to prevent form resubmission
        header('Location: ' . ADMIN_URL . '/admintools/tool.php?tool=linkcounter&action=settings');
        exit();
    }
}

// Load current settings
$settings_result = $database->query("SELECT `setting_key`, `setting_value` FROM `$settings_table`");
$settings = array();
if ($settings_result && $settings_result->numRows() > 0) {
    while ($row = $settings_result->fetchRow(MYSQLI_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

$crawler_protection_enabled = isset($settings['crawler_protection_enabled']) ? $settings['crawler_protection_enabled'] : '0';
$crawler_min_delay = isset($settings['crawler_min_delay']) ? (int)$settings['crawler_min_delay'] : 500;
$crawler_action = isset($settings['crawler_action']) ? $settings['crawler_action'] : 'skip_count';
$exit_notice_text = isset($settings['exit_notice_text']) ? $settings['exit_notice_text'] : '';

// Include CSS
echo '<link rel="stylesheet" href="' . WB_URL . '/modules/linkcounter/css/backend.css">';

?>

<div class="linkcounter-admin">
    <h2><?php echo $MOD_LINKCOUNTER['HEADING_SETTINGS']; ?></h2>

    <?php
    // Display success messages
    if (isset($_SESSION['linkcounter_success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['linkcounter_success']) . '</div>';
        unset($_SESSION['linkcounter_success']);
    }

    // Display error messages
    if (isset($_SESSION['linkcounter_error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['linkcounter_error']) . '</div>';
        unset($_SESSION['linkcounter_error']);
    }
    ?>

    <!-- Navigation -->
    <div class="action-bar">
        <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter" class="btn btn-secondary">
            <?php echo $MOD_LINKCOUNTER['BTN_BACK']; ?>
        </a>
    </div>

    <!-- Settings Form -->
    <form method="post" action="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&action=settings" class="linkcounter-form">
        <?php echo $admin->getFTAN(); ?>

        <div class="settings-section">
            <h3><?php echo $MOD_LINKCOUNTER['SETTINGS_CRAWLER_PROTECTION']; ?></h3>
            <p class="help-text"><?php echo $MOD_LINKCOUNTER['SETTINGS_CRAWLER_DESCRIPTION']; ?></p>

            <!-- Enable/Disable Crawler Protection -->
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox"
                           name="crawler_protection_enabled"
                           value="1"
                           <?php echo $crawler_protection_enabled == '1' ? 'checked' : ''; ?>>
                    <strong><?php echo $MOD_LINKCOUNTER['SETTINGS_ENABLE_PROTECTION']; ?></strong>
                </label>
                <p class="help-text"><?php echo $MOD_LINKCOUNTER['SETTINGS_ENABLE_PROTECTION_HELP']; ?></p>
            </div>

            <!-- Minimum Delay -->
            <div class="form-group">
                <label for="crawler_min_delay">
                    <strong><?php echo $MOD_LINKCOUNTER['SETTINGS_MIN_DELAY']; ?></strong>
                </label>
                <input type="number"
                       id="crawler_min_delay"
                       name="crawler_min_delay"
                       value="<?php echo (int)$crawler_min_delay; ?>"
                       min="100"
                       max="10000"
                       step="100"
                       class="form-control"
                       style="max-width: 200px;">
                <p class="help-text"><?php echo $MOD_LINKCOUNTER['SETTINGS_MIN_DELAY_HELP']; ?></p>
            </div>

            <!-- Crawler Action -->
            <div class="form-group">
                <label for="crawler_action">
                    <strong><?php echo $MOD_LINKCOUNTER['SETTINGS_CRAWLER_ACTION']; ?></strong>
                </label>
                <select id="crawler_action" name="crawler_action" class="form-control" style="max-width: 400px;">
                    <option value="skip_count" <?php echo $crawler_action == 'skip_count' ? 'selected' : ''; ?>>
                        <?php echo $MOD_LINKCOUNTER['SETTINGS_ACTION_SKIP_COUNT']; ?>
                    </option>
                    <option value="block" <?php echo $crawler_action == 'block' ? 'selected' : ''; ?>>
                        <?php echo $MOD_LINKCOUNTER['SETTINGS_ACTION_BLOCK']; ?>
                    </option>
                </select>
                <p class="help-text"><?php echo $MOD_LINKCOUNTER['SETTINGS_CRAWLER_ACTION_HELP']; ?></p>
            </div>

            <!-- Info Box -->
            <div class="info-box" style="margin-top: 20px; padding: 15px; background: #e7f3ff; border-left: 4px solid #2196F3; border-radius: 4px;">
                <h4 style="margin-top: 0;"><?php echo $MOD_LINKCOUNTER['SETTINGS_HOW_IT_WORKS']; ?></h4>
                <ul style="margin-bottom: 0;">
                    <li><?php echo $MOD_LINKCOUNTER['SETTINGS_HOW_POINT_1']; ?></li>
                    <li><?php echo $MOD_LINKCOUNTER['SETTINGS_HOW_POINT_2']; ?></li>
                    <li><?php echo $MOD_LINKCOUNTER['SETTINGS_HOW_POINT_3']; ?></li>
                    <li><?php echo $MOD_LINKCOUNTER['SETTINGS_HOW_POINT_4']; ?></li>
                </ul>
            </div>

        </div>

        <div class="settings-section" style="margin-top: 30px;">
            <h3><?php echo $MOD_LINKCOUNTER['SETTINGS_EXIT_NOTICE']; ?></h3>
            <p class="help-text"><?php echo $MOD_LINKCOUNTER['SETTINGS_EXIT_NOTICE_DESCRIPTION']; ?></p>

            <div class="form-group">
                <label for="exit_notice_text">
                    <strong><?php echo $MOD_LINKCOUNTER['SETTINGS_EXIT_NOTICE_TEXT']; ?></strong>
                </label>
                <textarea id="exit_notice_text"
                          name="exit_notice_text"
                          rows="3"
                          maxlength="255"
                          class="form-control"
                          placeholder="<?php echo htmlspecialchars($MOD_LINKCOUNTER['EXIT_NOTICE_DEFAULT_TEXT']); ?>"><?php echo htmlspecialchars($exit_notice_text); ?></textarea>
                <p class="help-text"><?php echo $MOD_LINKCOUNTER['SETTINGS_EXIT_NOTICE_TEXT_HELP']; ?></p>
            </div>
        </div>

        <!-- Save Button -->
        <div class="form-actions" style="margin-top: 30px;">
            <button type="submit" name="save_settings" class="btn btn-primary">
                <?php echo $MOD_LINKCOUNTER['BTN_SAVE']; ?>
            </button>
            <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter" class="btn btn-secondary">
                <?php echo $MOD_LINKCOUNTER['BTN_CANCEL']; ?>
            </a>
        </div>
    </form>
</div>
