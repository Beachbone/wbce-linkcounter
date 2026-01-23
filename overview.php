<?php
/**
 * Link Counter - Overview
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.1.0
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
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_asc';

// Build WHERE clause based on filter
$where = '';
switch ($filter) {
    case 'active':
        $where = "WHERE `active` = 1";
        break;
    case 'inactive':
        $where = "WHERE `active` = 0";
        break;
    default:
        $where = '';
}

// Build ORDER BY clause based on sort
$order_by = '';
switch ($sort) {
    case 'id_asc':
        $order_by = "ORDER BY `id` ASC";
        break;
    case 'id_desc':
        $order_by = "ORDER BY `id` DESC";
        break;
    case 'title_asc':
        $order_by = "ORDER BY `title` ASC";
        break;
    case 'title_desc':
        $order_by = "ORDER BY `title` DESC";
        break;
    case 'clicks_asc':
        $order_by = "ORDER BY `counter` ASC";
        break;
    case 'clicks_desc':
        $order_by = "ORDER BY `counter` DESC";
        break;
    case 'date_asc':
        $order_by = "ORDER BY `created` ASC";
        break;
    case 'date_desc':
        $order_by = "ORDER BY `created` DESC";
        break;
    default:
        $order_by = "ORDER BY `id` ASC";
}

// Get all links
$sql = "SELECT * FROM `$table` $where $order_by";
$result = $database->query($sql);

// Include CSS and JavaScript
echo '<link rel="stylesheet" href="' . WB_URL . '/modules/linkcounter/css/backend.css">';
echo '<script src="' . WB_URL . '/modules/linkcounter/js/backend.js"></script>';

?>

<div class="linkcounter-admin">
    <h2><?php echo $MOD_LINKCOUNTER['HEADING_OVERVIEW']; ?></h2>

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

    <!-- Action Bar -->
    <div class="action-bar">
        <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&action=add" class="btn btn-primary">
            <span class="icon-plus"></span> <?php echo $MOD_LINKCOUNTER['BTN_ADD']; ?>
        </a>
        <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&action=settings" class="btn btn-secondary">
            ⚙️ <?php echo $MOD_LINKCOUNTER['BTN_SETTINGS']; ?>
        </a>
        <form method="post" action="<?php echo WB_URL; ?>/modules/linkcounter/export.php">
            <?php echo $admin->getFTAN(); ?>
            <button type="submit" class="btn btn-secondary">
                <span class="icon-download"></span> <?php echo $MOD_LINKCOUNTER['BTN_EXPORT']; ?>
            </button>
        </form>
    </div>

    <!-- Filter and Sort -->
    <div class="filter-sort-bar">
        <div class="filter-group">
            <label><?php echo $MOD_LINKCOUNTER['FILTER_ALL']; ?>:</label>
            <select id="filter-select" onchange="applyFilter()">
                <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['FILTER_ALL']; ?>
                </option>
                <option value="active" <?php echo $filter == 'active' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['FILTER_ACTIVE']; ?>
                </option>
                <option value="inactive" <?php echo $filter == 'inactive' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['FILTER_INACTIVE']; ?>
                </option>
            </select>
        </div>

        <div class="sort-group">
            <label><?php echo $MOD_LINKCOUNTER['SORT_BY']; ?>:</label>
            <select id="sort-select" onchange="applySort()">
                <option value="id_asc" <?php echo $sort == 'id_asc' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['SORT_ID_ASC']; ?>
                </option>
                <option value="id_desc" <?php echo $sort == 'id_desc' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['SORT_ID_DESC']; ?>
                </option>
                <option value="title_asc" <?php echo $sort == 'title_asc' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['SORT_TITLE_ASC']; ?>
                </option>
                <option value="title_desc" <?php echo $sort == 'title_desc' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['SORT_TITLE_DESC']; ?>
                </option>
                <option value="clicks_desc" <?php echo $sort == 'clicks_desc' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['SORT_CLICKS_DESC']; ?>
                </option>
                <option value="clicks_asc" <?php echo $sort == 'clicks_asc' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['SORT_CLICKS_ASC']; ?>
                </option>
                <option value="date_desc" <?php echo $sort == 'date_desc' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['SORT_DATE_DESC']; ?>
                </option>
                <option value="date_asc" <?php echo $sort == 'date_asc' ? 'selected' : ''; ?>>
                    <?php echo $MOD_LINKCOUNTER['SORT_DATE_ASC']; ?>
                </option>
            </select>
        </div>
    </div>

    <?php if ($result->numRows() > 0): ?>
        <!-- Links Table -->
        <table class="table table-striped linkcounter-table">
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
                        <td><?php echo (int)$row['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                            <?php if (!empty($row['description'])): ?>
                                <br><small class="text-muted">
                                    <?php echo htmlspecialchars(substr($row['description'], 0, 80)); ?>
                                    <?php echo strlen($row['description']) > 80 ? '...' : ''; ?>
                                </small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small>
                                <?php if ($row['link_type'] == 'page' && !empty($row['page_id'])): ?>
                                    <span class="text-muted">
                                        <?php echo $MOD_LINKCOUNTER['LINK_TYPE_PAGE']; ?> (ID: <?php echo (int)$row['page_id']; ?>)
                                    </span>
                                <?php else: ?>
                                    <a href="<?php echo htmlspecialchars($row['url']); ?>" target="_blank">
                                        <?php echo htmlspecialchars(substr($row['url'], 0, 50)); ?>
                                        <?php echo strlen($row['url']) > 50 ? '...' : ''; ?>
                                    </a>
                                <?php endif; ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                <?php echo number_format($row['counter'], 0, ',', '.'); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['active']): ?>
                                <span class="badge badge-success">
                                    <?php echo $MOD_LINKCOUNTER['STATUS_ACTIVE']; ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">
                                    <?php echo $MOD_LINKCOUNTER['STATUS_INACTIVE']; ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small><?php echo date('d.m.Y H:i', strtotime($row['created'])); ?></small>
                        </td>
                        <td class="actions">
                            <a href="<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&action=edit&id=<?php echo $row['id']; ?>"
                               class="btn btn-sm btn-warning"
                               title="<?php echo $MOD_LINKCOUNTER['BTN_EDIT']; ?>">
                                ✏️
                            </a>
                            <form method="post" action="<?php echo WB_URL; ?>/modules/linkcounter/delete.php" style="display: inline;">
                                <?php echo $admin->getFTAN(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
                                <button type="submit"
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('<?php echo $MOD_LINKCOUNTER['CONFIRM_DELETE']; ?>');"
                                        title="<?php echo $MOD_LINKCOUNTER['BTN_DELETE']; ?>">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <!-- No Links Message -->
        <div class="alert alert-info">
            <p><?php echo $MOD_LINKCOUNTER['TEXT_NO_DOWNLOADS']; ?></p>
            <p><?php echo $MOD_LINKCOUNTER['TEXT_ADD_FIRST']; ?></p>
        </div>
    <?php endif; ?>

    <!-- Droplet Help -->
    <div class="droplet-help">
        <h3><?php echo $MOD_LINKCOUNTER['DROPLET_USAGE_HEADING']; ?></h3>
        <div class="help-box">
            <h4>[[LinkCounter]]</h4>
            <p><?php echo $MOD_LINKCOUNTER['DROPLET_LINKCOUNTER_DESC']; ?></p>
            <code>[[linkcounter?id=1]]</code>
        </div>
        <div class="help-box">
            <h4>[[LinkCounterStats]]</h4>
            <p><?php echo $MOD_LINKCOUNTER['DROPLET_LINKSTATS_DESC']; ?></p>
            <code>[[linkcounterstats?limit=10]]</code>
        </div>
    </div>
</div>

<script>
function applyFilter() {
    var filter = document.getElementById('filter-select').value;
    var sort = document.getElementById('sort-select').value;
    window.location.href = '<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&filter=' + filter + '&sort=' + sort;
}

function applySort() {
    var filter = document.getElementById('filter-select').value;
    var sort = document.getElementById('sort-select').value;
    window.location.href = '<?php echo ADMIN_URL; ?>/admintools/tool.php?tool=linkcounter&filter=' + filter + '&sort=' + sort;
}
</script>
