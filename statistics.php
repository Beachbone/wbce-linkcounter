<?php
/**
 * Link Counter - Statistics View
 *
 * @author      WBCE Community
 * @copyright   2026 WBCE Community
 * @license     MIT License
 */

if (!defined('WB_PATH')) {
    require('../../config.php');
}

global $database;
$table = TABLE_PREFIX . 'mod_linkcounter';

// Get limit parameter
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int)$_GET['limit'] : 10;
$limit = max(1, min($limit, 100)); // Between 1 and 100

// Get display mode (embed or standalone)
$embed = isset($_GET['embed']) && $_GET['embed'] == '1';

// Get links ordered by counter
$sql = "SELECT * FROM `$table` WHERE `active` = 1 ORDER BY `counter` DESC LIMIT $limit";
$result = $database->query($sql);

// If not embed mode, output HTML structure
if (!$embed) {
    ?>
    <!DOCTYPE html>
    <html lang="<?php echo LANGUAGE; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Link Statistics - <?php echo WEBSITE_TITLE; ?></title>
        <link rel="stylesheet" href="<?php echo WB_URL; ?>/modules/linkcounter/css/frontend.css">
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
        </style>
    </head>
    <body>
        <div class="container">
    <?php
}
?>

<div class="linkcounter-stats">
    <h2>Link Statistics</h2>

    <?php if ($result->numRows() > 0): ?>
        <table class="stats-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Clicks</th>
                    <th>Link</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $position = 1;
                while ($row = $result->fetchRow(MYSQLI_ASSOC)):
                    $track_url = WB_URL . '/modules/linkcounter/track.php?id=' . $row['id'];
                ?>
                    <tr>
                        <td class="position"><?php echo $position++; ?></td>
                        <td class="title">
                            <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                        </td>
                        <td class="description">
                            <?php
                            $desc = htmlspecialchars($row['description']);
                            echo strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                            ?>
                        </td>
                        <td class="counter">
                            <span class="badge">
                                <?php echo number_format($row['counter'], 0, ',', '.'); ?>
                            </span>
                        </td>
                        <td class="action">
                            <a href="<?php echo $track_url; ?>" class="download-btn">
                                Visit
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results">No links available yet.</p>
    <?php endif; ?>
</div>

<?php
// Close HTML structure if not embed mode
if (!$embed) {
    ?>
        </div>
    </body>
    </html>
    <?php
}
?>
