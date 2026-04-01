<?php
/**
 * Link Counter - Overview
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

// Load language file
$lang = (file_exists(WB_PATH . '/modules/linkcounter/languages/' . LANGUAGE . '.php'))
    ? LANGUAGE
    : 'EN';
require_once(WB_PATH . '/modules/linkcounter/languages/' . $lang . '.php');

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Handle filter and sort parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$sort   = isset($_GET['sort'])   ? $_GET['sort']   : 'id_asc';

// Build WHERE clause based on filter
$where = '';
switch ($filter) {
    case 'active':   $where = "WHERE `active` = 1"; break;
    case 'inactive': $where = "WHERE `active` = 0"; break;
    default:         $where = '';
}

// Build ORDER BY clause based on sort
$order_by = '';
switch ($sort) {
    case 'id_asc':     $order_by = "ORDER BY `id` ASC";      break;
    case 'id_desc':    $order_by = "ORDER BY `id` DESC";     break;
    case 'title_asc':  $order_by = "ORDER BY `title` ASC";   break;
    case 'title_desc': $order_by = "ORDER BY `title` DESC";  break;
    case 'clicks_asc': $order_by = "ORDER BY `counter` ASC"; break;
    case 'clicks_desc':$order_by = "ORDER BY `counter` DESC";break;
    case 'date_asc':   $order_by = "ORDER BY `created` ASC"; break;
    case 'date_desc':  $order_by = "ORDER BY `created` DESC";break;
    default:           $order_by = "ORDER BY `id` ASC";
}

// Get all links
$sql    = "SELECT * FROM `$table` $where $order_by";
$result = $database->query($sql);

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

<!-- Action Bar -->
<div class="lc-card-inner">
    <div class="lc-topbar">
        <div class="lc-section-title">
            <i class="fa fa-link"></i> <?php echo $MOD_LINKCOUNTER['HEADING_OVERVIEW']; ?>
        </div>
        <div class="lc-action-bar">
            <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&action=add"
               class="lc-btn lc-btn-primary">
                <i class="fa fa-plus"></i> <?php echo $MOD_LINKCOUNTER['BTN_ADD']; ?>
            </a>
            <form method="post" action="<?php echo WB_URL; ?>/modules/linkcounter/export.php" style="display:inline;">
                <?php echo $admin->getFTAN(); ?>
                <button type="submit" class="lc-btn lc-btn-secondary">
                    <i class="fa fa-download"></i> <?php echo $MOD_LINKCOUNTER['BTN_EXPORT']; ?>
                </button>
            </form>
            <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&action=settings"
               class="lc-btn lc-btn-secondary">
                <i class="fa fa-cog"></i> <?php echo $MOD_LINKCOUNTER['BTN_SETTINGS']; ?>
            </a>
        </div>
    </div>
</div>

<!-- Filter and Sort -->
<div class="lc-card-inner">
    <div class="lc-filter-bar">
        <div class="lc-filter-group">
            <span class="lc-label-cap"><?php echo $MOD_LINKCOUNTER['FILTER_ALL']; ?></span>
            <select id="filter-select" onchange="applyFilter()">
                <option value="all"      <?php echo $filter == 'all'      ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['FILTER_ALL'];      ?></option>
                <option value="active"   <?php echo $filter == 'active'   ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['FILTER_ACTIVE'];   ?></option>
                <option value="inactive" <?php echo $filter == 'inactive' ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['FILTER_INACTIVE']; ?></option>
            </select>
        </div>
        <div class="lc-filter-group">
            <span class="lc-label-cap"><?php echo $MOD_LINKCOUNTER['SORT_BY']; ?></span>
            <select id="sort-select" onchange="applySort()">
                <option value="id_asc"    <?php echo $sort == 'id_asc'    ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['SORT_ID_ASC'];    ?></option>
                <option value="id_desc"   <?php echo $sort == 'id_desc'   ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['SORT_ID_DESC'];   ?></option>
                <option value="title_asc" <?php echo $sort == 'title_asc' ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['SORT_TITLE_ASC']; ?></option>
                <option value="title_desc"<?php echo $sort == 'title_desc'? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['SORT_TITLE_DESC'];?></option>
                <option value="clicks_desc"<?php echo $sort=='clicks_desc'? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['SORT_CLICKS_DESC'];?></option>
                <option value="clicks_asc" <?php echo $sort=='clicks_asc' ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['SORT_CLICKS_ASC']; ?></option>
                <option value="date_desc" <?php echo $sort == 'date_desc' ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['SORT_DATE_DESC']; ?></option>
                <option value="date_asc"  <?php echo $sort == 'date_asc'  ? 'selected' : ''; ?>><?php echo $MOD_LINKCOUNTER['SORT_DATE_ASC'];  ?></option>
            </select>
        </div>
    </div>
</div>

<!-- Links Table / Empty State -->
<div class="lc-card-inner">
<?php if ($result->numRows() > 0): ?>
    <table class="lc-table">
        <colgroup>
            <col style="width:45px">       <!-- ID -->
            <col style="width:24%">        <!-- Title -->
            <col>                          <!-- URL – restlicher Platz -->
            <col style="width:70px">       <!-- Clicks -->
            <col style="width:90px">       <!-- Status -->
            <col style="width:118px">      <!-- Date -->
            <col style="width:72px">       <!-- Actions -->
        </colgroup>
        <thead>
            <tr>
                <th><?php echo $MOD_LINKCOUNTER['TH_ID']; ?></th>
                <th><?php echo $MOD_LINKCOUNTER['TH_TITLE']; ?></th>
                <th><?php echo $MOD_LINKCOUNTER['TH_URL']; ?></th>
                <th><?php echo $MOD_LINKCOUNTER['TH_COUNTER']; ?></th>
                <th><?php echo $MOD_LINKCOUNTER['TH_STATUS']; ?></th>
                <th><?php echo $MOD_LINKCOUNTER['TH_CREATED']; ?></th>
                <th><?php echo $MOD_LINKCOUNTER['TH_ACTIONS']; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetchRow(MYSQLI_ASSOC)): ?>
            <tr>
                <td class="lc-col-id"><?php echo (int)$row['id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                    <?php if (!empty($row['open_target']) && $row['open_target'] === '_blank'): ?>
                        <i class="fa fa-external-link lc-icon-target" title="Neuer Tab"></i>
                    <?php endif; ?>
                    <?php if (!empty($row['description'])): ?>
                        <br><span class="lc-muted">
                            <?php echo htmlspecialchars(substr($row['description'], 0, 80)); ?><?php echo strlen($row['description']) > 80 ? '…' : ''; ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td class="lc-col-url">
                    <?php if ($row['link_type'] == 'page' && !empty($row['page_id'])): ?>
                        <span class="lc-muted"><i class="fa fa-file-o"></i> <?php echo $MOD_LINKCOUNTER['LINK_TYPE_PAGE']; ?> (ID&nbsp;<?php echo (int)$row['page_id']; ?>)</span>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank" class="lc-url-link">
                            <?php echo htmlspecialchars(substr($row['url'], 0, 55)); ?><?php echo strlen($row['url']) > 55 ? '…' : ''; ?>
                        </a>
                    <?php endif; ?>
                </td>
                <td class="lc-col-clicks">
                    <span class="lc-badge lc-badge-clicks"><?php echo number_format($row['counter'], 0, ',', '.'); ?></span>
                </td>
                <td>
                    <?php if ($row['active']): ?>
                        <span class="lc-badge lc-badge-active"><?php echo $MOD_LINKCOUNTER['STATUS_ACTIVE']; ?></span>
                    <?php else: ?>
                        <span class="lc-badge lc-badge-inactive"><?php echo $MOD_LINKCOUNTER['STATUS_INACTIVE']; ?></span>
                    <?php endif; ?>
                </td>
                <td class="lc-col-date lc-muted"><?php echo date('d.m.Y H:i', strtotime($row['created'])); ?></td>
                <td class="lc-col-actions">
                    <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&action=edit&id=<?php echo (int)$row['id']; ?>"
                       class="lc-icon-btn" title="<?php echo $MOD_LINKCOUNTER['BTN_EDIT']; ?>">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <form method="post" action="<?php echo WB_URL; ?>/modules/linkcounter/delete.php" style="display:inline;">
                        <?php echo $admin->getFTAN(); ?>
                        <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                        <button type="submit" class="lc-icon-btn lc-icon-btn-danger"
                                onclick="return confirm('<?php echo addslashes($MOD_LINKCOUNTER['CONFIRM_DELETE']); ?>');"
                                title="<?php echo $MOD_LINKCOUNTER['BTN_DELETE']; ?>">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="lc-empty">
        <div class="lc-empty-icon"><i class="fa fa-link"></i></div>
        <div class="lc-empty-title"><?php echo $MOD_LINKCOUNTER['TEXT_NO_DOWNLOADS']; ?></div>
        <div class="lc-empty-sub"><?php echo $MOD_LINKCOUNTER['TEXT_ADD_FIRST']; ?></div>
    </div>
<?php endif; ?>
</div>

</div><!-- /.lc-stack -->
</div><!-- /.lc-wrap -->

<script>
function applyFilter() {
    var filter = document.getElementById('filter-select').value;
    var sort   = document.getElementById('sort-select').value;
    window.location.href = '<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&filter=' + filter + '&sort=' + sort;
}
function applySort() {
    var filter = document.getElementById('filter-select').value;
    var sort   = document.getElementById('sort-select').value;
    window.location.href = '<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&filter=' + filter + '&sort=' + sort;
}
</script>
