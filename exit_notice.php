<?php
/**
 * Link Counter - Exit Notice (interstitial page, included by track.php)
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.3.0
 */
defined('WB_PATH') or die("This file can't be accessed directly!");

$h = function ($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
};

// Escape first, then insert placeholders (also escaped) and keep line breaks
$text_html = str_replace(
    array('{host}', '{title}'),
    array('<strong>' . $h($target_host) . '</strong>', $h($download['title'])),
    $h($notice_text)
);
$text_html = nl2br($text_html);

$site_title = defined('WEBSITE_TITLE') ? WEBSITE_TITLE : '';
$html_lang  = strtolower(defined('LANGUAGE') ? LANGUAGE : 'en');

header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store, max-age=0');
?>
<!DOCTYPE html>
<html lang="<?php echo $h($html_lang); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?php echo $h($MOD_LINKCOUNTER['EXIT_NOTICE_TITLE']) . ($site_title !== '' ? ' – ' . $h($site_title) : ''); ?></title>
<style>
:root {
    --lc-bg: #f3f4f6;
    --lc-card: #ffffff;
    --lc-text: #1f2937;
    --lc-muted: #6b7280;
    --lc-border: #e5e7eb;
    --lc-accent: #2563eb;
    --lc-accent-text: #ffffff;
    --lc-code-bg: #f9fafb;
}
@media (prefers-color-scheme: dark) {
    :root {
        --lc-bg: #111827;
        --lc-card: #1f2937;
        --lc-text: #f3f4f6;
        --lc-muted: #9ca3af;
        --lc-border: #374151;
        --lc-accent: #3b82f6;
        --lc-accent-text: #ffffff;
        --lc-code-bg: #111827;
    }
}
* { box-sizing: border-box; }
body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: var(--lc-bg);
    color: var(--lc-text);
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    line-height: 1.5;
}
.lc-card {
    width: 100%;
    max-width: 520px;
    background: var(--lc-card);
    border: 1px solid var(--lc-border);
    border-radius: 10px;
    padding: 28px 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
}
.lc-icon { color: var(--lc-accent); margin-bottom: 8px; }
h1 { font-size: 1.35rem; margin: 0 0 12px; }
p { margin: 0 0 16px; }
.lc-target-label { font-size: .85rem; color: var(--lc-muted); margin-bottom: 4px; }
.lc-target {
    font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
    font-size: .9rem;
    background: var(--lc-code-bg);
    border: 1px solid var(--lc-border);
    border-radius: 6px;
    padding: 8px 10px;
    margin-bottom: 24px;
    overflow-wrap: anywhere;
}
.lc-actions { display: flex; flex-wrap: wrap; gap: 10px; }
.lc-btn {
    display: inline-block;
    padding: 10px 18px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    border: 1px solid var(--lc-border);
    color: var(--lc-text);
    background: transparent;
}
.lc-btn-primary {
    background: var(--lc-accent);
    border-color: var(--lc-accent);
    color: var(--lc-accent-text);
}
.lc-btn:focus-visible { outline: 3px solid var(--lc-accent); outline-offset: 2px; }
@media (max-width: 420px) {
    .lc-btn { flex: 1 1 100%; text-align: center; }
}
</style>
</head>
<body>
<main class="lc-card">
    <div class="lc-icon" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
    </div>
    <h1><?php echo $h($MOD_LINKCOUNTER['EXIT_NOTICE_TITLE']); ?></h1>
    <p><?php echo $text_html; ?></p>

    <div class="lc-target-label"><?php echo $h($MOD_LINKCOUNTER['EXIT_NOTICE_TARGET']); ?></div>
    <div class="lc-target"><?php echo $h($target_url); ?></div>

    <div class="lc-actions">
        <a class="lc-btn lc-btn-primary" href="<?php echo $h($continue_url); ?>" rel="nofollow">
            <?php echo $h(sprintf($MOD_LINKCOUNTER['EXIT_NOTICE_CONTINUE'], $target_host)); ?>
        </a>
        <a class="lc-btn" id="lc-back" href="<?php echo $h($back_url); ?>">
            <?php echo $h($MOD_LINKCOUNTER['EXIT_NOTICE_BACK']); ?>
        </a>
    </div>
</main>
<script>
(function () {
    var back = document.getElementById('lc-back');
    back.addEventListener('click', function (e) {
        e.preventDefault();
        if (window.history.length > 1) {
            window.history.back();
            return;
        }
        // Opened in a new tab: close it; fall back to the link target if the browser refuses
        var href = back.href;
        window.close();
        setTimeout(function () { window.location.href = href; }, 150);
    });
})();
</script>
</body>
</html>
