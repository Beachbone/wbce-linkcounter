<?php
/**
 * Link Counter - Add/Edit Form
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.0.0
 */

if(!defined('WB_PATH')) exit("Cannot access this file directly ".__FILE__);

require_once(WB_PATH . '/framework/class.admin.php');

if (!isset($admin)) {
    $admin = new admin('Admintools', 'admintools', false);
}
$lang = (file_exists(WB_PATH . '/modules/linkcounter/languages/' . LANGUAGE . '.php'))
    ? LANGUAGE
    : 'EN';
require_once(WB_PATH . '/modules/linkcounter/languages/' . $lang . '.php');

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Check if editing existing download
$edit_mode = false;
$download = array(
    'id' => 0,
    'title' => '',
    'link_type' => 'url',
    'url' => '',
    'page_id' => null,
    'description' => '',
    'active' => 1,
    'counter' => 0
);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $database->query("SELECT * FROM `$table` WHERE `id` = $id");

    if ($result->numRows() > 0) {
        $download = $result->fetchRow(MYSQLI_ASSOC);
        $edit_mode = true;
    }
}

// Get all pages from WBCE for dropdown
$pages_table = TABLE_PREFIX . 'pages';
$pages_query = "SELECT `page_id`, `menu_title`, `page_title`, `level`, `parent`
                FROM `$pages_table`
                WHERE `visibility` != 'deleted'
                ORDER BY `position` ASC";
$pages_result = $database->query($pages_query);

// Build hierarchical page list
$pages = array();
while ($page = $pages_result->fetchRow(MYSQLI_ASSOC)) {
    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $page['level']);
    $pages[] = array(
        'page_id' => $page['page_id'],
        'title' => $indent . ($page['menu_title'] ? $page['menu_title'] : $page['page_title']),
        'level' => $page['level']
    );
}

// Include CSS and JavaScript
echo '<link rel="stylesheet" href="' . WB_URL . '/modules/linkcounter/css/backend.css">';
echo '<script src="' . WB_URL . '/modules/linkcounter/js/backend.js"></script>';

?>

<div class="linkcounter-admin">
    <h2>
        <?php echo $edit_mode ? $MOD_LINKCOUNTER['HEADING_EDIT'] : $MOD_LINKCOUNTER['HEADING_ADD']; ?>
    </h2>

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

    // Display validation errors
    if (isset($_SESSION['linkcounter_errors']) && is_array($_SESSION['linkcounter_errors'])) {
        echo '<div class="alert alert-danger"><ul>';
        foreach ($_SESSION['linkcounter_errors'] as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul></div>';
        unset($_SESSION['linkcounter_errors']);
    }
    ?>

    <form action="<?php echo WB_URL; ?>/modules/linkcounter/save.php" method="post" class="linkcounter-form">
        <?php echo $admin->getFTAN(); ?>

        <input type="hidden" name="id" value="<?php echo (int)$download['id']; ?>">

        <!-- Title Field -->
        <div class="form-group">
            <label for="title" class="required">
                <?php echo $MOD_LINKCOUNTER['LABEL_TITLE']; ?> *
            </label>
            <input type="text"
                   id="title"
                   name="title"
                   class="form-control"
                   value="<?php echo htmlspecialchars($download['title']); ?>"
                   placeholder="<?php echo $MOD_LINKCOUNTER['PLACEHOLDER_TITLE']; ?>"
                   required>
            <small class="form-text text-muted">
                <?php echo $MOD_LINKCOUNTER['HELP_TITLE']; ?>
            </small>
        </div>

        <!-- Link Type Field -->
        <div class="form-group">
            <label for="link_type" class="required">
                <?php echo $MOD_LINKCOUNTER['LABEL_LINK_TYPE']; ?> *
            </label>
            <select id="link_type"
                    name="link_type"
                    class="form-control"
                    onchange="toggleLinkFields()"
                    required>
                <option value="url" <?php echo $download['link_type'] == 'url' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['LINK_TYPE_URL']; ?>
                </option>
                <option value="page" <?php echo $download['link_type'] == 'page' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['LINK_TYPE_PAGE']; ?>
                </option>
            </select>
            <small class="form-text text-muted">
                <?php echo $MOD_LINKCOUNTER['HELP_LINK_TYPE']; ?>
            </small>
        </div>

        <!-- URL Field -->
        <div class="form-group" id="url-field">
            <label for="url" class="required">
                <?php echo $MOD_LINKCOUNTER['LABEL_URL']; ?> *
            </label>
            <input type="text"
                   id="url"
                   name="url"
                   class="form-control"
                   value="<?php echo htmlspecialchars($download['url']); ?>"
                   placeholder="<?php echo $MOD_LINKCOUNTER['PLACEHOLDER_URL']; ?>">
            <small class="form-text text-muted">
                <?php echo $MOD_LINKCOUNTER['HELP_URL']; ?>
            </small>
        </div>

        <!-- Page Field -->
        <div class="form-group" id="page-field" style="display: none;">
            <label for="page_id" class="required">
                <?php echo $MOD_LINKCOUNTER['LABEL_PAGE']; ?> *
            </label>
            <select id="page_id"
                    name="page_id"
                    class="form-control">
                <option value=""><?php echo $MOD_LINKCOUNTER['SELECT_PAGE']; ?></option>
                <?php foreach ($pages as $page): ?>
                    <option value="<?php echo $page['page_id']; ?>"
                            <?php echo $download['page_id'] == $page['page_id'] ? 'selected' : ''; ?>>
                        <?php echo $page['title']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">
                <?php echo $MOD_LINKCOUNTER['HELP_PAGE']; ?>
            </small>
        </div>

        <!-- Description Field -->
        <div class="form-group">
            <label for="description">
                <?php echo $MOD_LINKCOUNTER['LABEL_DESCRIPTION']; ?>
            </label>
            <textarea id="description"
                      name="description"
                      class="form-control"
                      rows="4"
                      placeholder="<?php echo $MOD_LINKCOUNTER['PLACEHOLDER_DESC']; ?>"><?php echo htmlspecialchars($download['description']); ?></textarea>
            <small class="form-text text-muted">
                <?php echo $MOD_LINKCOUNTER['HELP_DESCRIPTION']; ?>
            </small>
        </div>

        <!-- Active Checkbox -->
        <div class="form-group form-check">
            <input type="checkbox"
                   id="active"
                   name="active"
                   class="form-check-input"
                   value="1"
                   <?php echo $download['active'] ? 'checked' : ''; ?>>
            <label for="active" class="form-check-label">
                <?php echo $MOD_LINKCOUNTER['LABEL_ACTIVE']; ?>
            </label>
            <small class="form-text text-muted">
                <?php echo $MOD_LINKCOUNTER['HELP_ACTIVE']; ?>
            </small>
        </div>



        <!-- Buttons -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <span class="icon-save"></span> <?php echo $MOD_LINKCOUNTER['BTN_SAVE']; ?>
            </button>
            <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter" class="btn btn-secondary">
                <span class="icon-cancel"></span> <?php echo $MOD_LINKCOUNTER['BTN_CANCEL']; ?>
            </a>
        </div>
    </form>
    
        <!-- Counter Info (only in edit mode) -->
        <?php if ($edit_mode): ?>
            <div class="form-group counter-info" style="display: flex; align-items: center; gap: 10px;">
                <label style="margin-bottom: 0;"><?php echo $MOD_LINKCOUNTER['TH_COUNTER']; ?>:</label>
                <span class="badge badge-info badge-lg">
                    <?php echo number_format($download['counter'], 0, ',', '.'); ?>
                </span>
            </div>
        <!-- Reset Counter Form (outside main form to avoid nesting) -->
        <div style="margin-top: 20px;">
            <form method="post" action="<?php echo WB_URL; ?>/modules/linkcounter/reset_counter.php" style="display: inline;">
                <?php echo $admin->getFTAN(); ?>
                <input type="hidden" name="id" value="<?php echo (int)$download['id']; ?>">
                <button type="submit"
                        class="btn btn-sm btn-outline-secondary"
                        onclick="return confirm('<?php echo $MOD_LINKCOUNTER['CONFIRM_RESET']; ?>');">
                    <?php echo $MOD_LINKCOUNTER['BTN_RESET_COUNTER']; ?>
                </button>
            </form>
        </div>

        <!-- Droplet Usage Example -->
        <div class="droplet-usage-box">
            <h3><?php echo $MOD_LINKCOUNTER['DROPLET_CODE_HEADING']; ?></h3>
            <div class="code-block">
                <code>[[LinkCounter?id=<?php echo $download['id']; ?>]]</code>
                <button type="button" class="btn btn-sm btn-outline-primary copy-btn"
                        onclick="copyToClipboard('[[LinkCounter?id=<?php echo $download['id']; ?>]]')">
                    Copy
                </button>
            </div>
            <p class="text-muted"><?php echo $MOD_LINKCOUNTER['DROPLET_CODE_INFO']; ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
// Toggle between URL and Page fields based on link type
function toggleLinkFields() {
    var linkType = document.getElementById('link_type').value;
    var urlField = document.getElementById('url-field');
    var pageField = document.getElementById('page-field');
    var urlInput = document.getElementById('url');
    var pageInput = document.getElementById('page_id');

    if (linkType === 'url') {
        // Show URL field, hide page field
        urlField.style.display = 'block';
        pageField.style.display = 'none';
        urlInput.required = true;
        pageInput.required = false;
    } else {
        // Show page field, hide URL field
        urlField.style.display = 'none';
        pageField.style.display = 'block';
        urlInput.required = false;
        pageInput.required = true;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleLinkFields();
});

function copyToClipboard(text) {
    // Create temporary textarea
    var textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();

    try {
        document.execCommand('copy');
        alert('Copied to clipboard!');
    } catch (err) {
        alert('Failed to copy');
    }

    document.body.removeChild(textarea);
}
</script>
