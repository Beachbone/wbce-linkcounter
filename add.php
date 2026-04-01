<?php
/**
 * Link Counter - Add/Edit Form
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.2.0
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

// Check if editing existing link
$edit_mode = false;
$download  = array(
    'id'          => 0,
    'title'       => '',
    'link_type'   => 'url',
    'url'         => '',
    'page_id'     => null,
    'description' => '',
    'open_target' => '_self',
    'active'      => 1,
    'counter'     => 0,
);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $result = $database->query("SELECT * FROM `$table` WHERE `id` = $id");
    if ($result->numRows() > 0) {
        $download  = $result->fetchRow(MYSQLI_ASSOC);
        $edit_mode = true;
    }
}

// Get all pages from WBCE for dropdown
$pages_table  = TABLE_PREFIX . 'pages';
$pages_result = $database->query(
    "SELECT `page_id`, `menu_title`, `page_title`, `level`
     FROM `$pages_table`
     WHERE `visibility` != 'deleted'
     ORDER BY `position` ASC"
);
$pages = array();
while ($page = $pages_result->fetchRow(MYSQLI_ASSOC)) {
    $indent  = str_repeat('&nbsp;&nbsp;&nbsp;', $page['level']);
    $pages[] = array(
        'page_id' => $page['page_id'],
        'title'   => $indent . ($page['menu_title'] ? $page['menu_title'] : $page['page_title']),
    );
}

// Include CSS
echo '<link rel="stylesheet" href="' . WB_URL . '/modules/linkcounter/css/backend.css">';
?>

<div class="lc-wrap">
<div class="lc-stack">

<?php
// Alerts
if (isset($_SESSION['linkcounter_success'])) {
    echo '<div class="lc-alert lc-alert-ok"><i class="fa fa-check-circle"></i> ' . htmlspecialchars($_SESSION['linkcounter_success']) . '</div>';
    unset($_SESSION['linkcounter_success']);
}
if (isset($_SESSION['linkcounter_error'])) {
    echo '<div class="lc-alert lc-alert-error"><i class="fa fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['linkcounter_error']) . '</div>';
    unset($_SESSION['linkcounter_error']);
}
if (isset($_SESSION['linkcounter_errors']) && is_array($_SESSION['linkcounter_errors'])) {
    echo '<div class="lc-alert lc-alert-error"><i class="fa fa-exclamation-circle"></i> ';
    echo implode(' &bull; ', array_map('htmlspecialchars', $_SESSION['linkcounter_errors']));
    echo '</div>';
    unset($_SESSION['linkcounter_errors']);
}
?>

<!-- Form Card -->
<div class="lc-card-inner">
    <div class="lc-section-title">
        <i class="fa fa-<?php echo $edit_mode ? 'pencil' : 'plus'; ?>"></i>
        <?php echo $edit_mode ? $MOD_LINKCOUNTER['HEADING_EDIT'] : $MOD_LINKCOUNTER['HEADING_ADD']; ?>
    </div>

    <form action="<?php echo WB_URL; ?>/modules/linkcounter/save.php" method="post">
        <?php echo $admin->getFTAN(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$download['id']; ?>">

        <!-- Title -->
        <div class="lc-field">
            <label for="title"><?php echo $MOD_LINKCOUNTER['LABEL_TITLE']; ?> *</label>
            <input type="text" id="title" name="title" required
                   value="<?php echo htmlspecialchars($download['title']); ?>"
                   placeholder="<?php echo htmlspecialchars($MOD_LINKCOUNTER['PLACEHOLDER_TITLE']); ?>">
            <span class="lc-field-hint"><?php echo $MOD_LINKCOUNTER['HELP_TITLE']; ?></span>
        </div>

        <!-- Link Type -->
        <div class="lc-field">
            <label for="link_type"><?php echo $MOD_LINKCOUNTER['LABEL_LINK_TYPE']; ?> *</label>
            <select id="link_type" name="link_type" onchange="toggleLinkFields()">
                <option value="url"  <?php echo $download['link_type'] == 'url'  ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['LINK_TYPE_URL'];  ?></option>
                <option value="page" <?php echo $download['link_type'] == 'page' ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['LINK_TYPE_PAGE']; ?></option>
            </select>
            <span class="lc-field-hint"><?php echo $MOD_LINKCOUNTER['HELP_LINK_TYPE']; ?></span>
        </div>

        <!-- URL -->
        <div class="lc-field" id="url-field">
            <label for="url"><?php echo $MOD_LINKCOUNTER['LABEL_URL']; ?> *</label>
            <input type="text" id="url" name="url"
                   value="<?php echo htmlspecialchars($download['url']); ?>"
                   placeholder="<?php echo htmlspecialchars($MOD_LINKCOUNTER['PLACEHOLDER_URL']); ?>">
            <span class="lc-field-hint"><?php echo $MOD_LINKCOUNTER['HELP_URL']; ?></span>
        </div>

        <!-- Internal Page -->
        <div class="lc-field" id="page-field" style="display:none;">
            <label for="page_id"><?php echo $MOD_LINKCOUNTER['LABEL_PAGE']; ?> *</label>
            <select id="page_id" name="page_id">
                <option value=""><?php echo $MOD_LINKCOUNTER['SELECT_PAGE']; ?></option>
                <?php foreach ($pages as $page): ?>
                    <option value="<?php echo $page['page_id']; ?>"
                            <?php echo $download['page_id'] == $page['page_id'] ? 'selected' : ''; ?>>
                        <?php echo $page['title']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="lc-field-hint"><?php echo $MOD_LINKCOUNTER['HELP_PAGE']; ?></span>
        </div>

        <!-- Description -->
        <div class="lc-field">
            <label for="description"><?php echo $MOD_LINKCOUNTER['LABEL_DESCRIPTION']; ?></label>
            <textarea id="description" name="description" rows="3"
                      placeholder="<?php echo htmlspecialchars($MOD_LINKCOUNTER['PLACEHOLDER_DESC']); ?>"><?php echo htmlspecialchars($download['description']); ?></textarea>
            <span class="lc-field-hint"><?php echo $MOD_LINKCOUNTER['HELP_DESCRIPTION']; ?></span>
        </div>

        <!-- Toggles -->
        <div class="lc-toggle-row">
            <div class="lc-toggle-label">
                <div class="lc-toggle-title"><?php echo $MOD_LINKCOUNTER['LABEL_OPEN_TARGET']; ?></div>
                <div class="lc-toggle-hint"><?php echo $MOD_LINKCOUNTER['HELP_OPEN_TARGET']; ?></div>
            </div>
            <label class="lc-switch">
                <input type="checkbox" name="open_target" value="_blank"
                       <?php echo ($download['open_target'] === '_blank') ? 'checked' : ''; ?>>
                <span class="lc-switch-track"></span>
                <span class="lc-switch-handle"></span>
            </label>
        </div>

        <div class="lc-toggle-row">
            <div class="lc-toggle-label">
                <div class="lc-toggle-title"><?php echo $MOD_LINKCOUNTER['LABEL_ACTIVE']; ?></div>
                <div class="lc-toggle-hint"><?php echo $MOD_LINKCOUNTER['HELP_ACTIVE']; ?></div>
            </div>
            <label class="lc-switch">
                <input type="checkbox" name="active" value="1"
                       <?php echo $download['active'] ? 'checked' : ''; ?>>
                <span class="lc-switch-track"></span>
                <span class="lc-switch-handle"></span>
            </label>
        </div>

        <!-- Buttons -->
        <div class="lc-form-actions">
            <button type="submit" class="lc-save-btn">
                <i class="fa fa-floppy-o"></i> <?php echo $MOD_LINKCOUNTER['BTN_SAVE']; ?>
            </button>
            <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter" class="lc-btn lc-btn-secondary">
                <i class="fa fa-times"></i> <?php echo $MOD_LINKCOUNTER['BTN_CANCEL']; ?>
            </a>
        </div>
    </form>
</div>

<?php if ($edit_mode): ?>
<!-- Counter + Reset Card -->
<div class="lc-card-inner">
    <div class="lc-section-title">
        <i class="fa fa-bar-chart"></i> <?php echo $MOD_LINKCOUNTER['TH_COUNTER']; ?>
    </div>
    <div class="lc-counter-row">
        <span class="lc-counter-value"><?php echo number_format($download['counter'], 0, ',', '.'); ?></span>
        <form method="post" action="<?php echo WB_URL; ?>/modules/linkcounter/reset_counter.php" style="display:inline;">
            <?php echo $admin->getFTAN(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$download['id']; ?>">
            <button type="submit" class="lc-btn lc-btn-secondary"
                    onclick="return confirm('<?php echo addslashes($MOD_LINKCOUNTER['CONFIRM_RESET']); ?>');">
                <i class="fa fa-refresh"></i> <?php echo $MOD_LINKCOUNTER['BTN_RESET_COUNTER']; ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>

</div><!-- /.lc-stack -->
</div><!-- /.lc-wrap -->

<script>
function toggleLinkFields() {
    var type      = document.getElementById('link_type').value;
    var urlField  = document.getElementById('url-field');
    var pageField = document.getElementById('page-field');
    var urlInput  = document.getElementById('url');
    var pageInput = document.getElementById('page_id');
    if (type === 'url') {
        urlField.style.display  = 'block';
        pageField.style.display = 'none';
        urlInput.required  = true;
        pageInput.required = false;
    } else {
        urlField.style.display  = 'none';
        pageField.style.display = 'block';
        urlInput.required  = false;
        pageInput.required = true;
    }
}
document.addEventListener('DOMContentLoaded', function() { toggleLinkFields(); });
</script>
