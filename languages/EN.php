<?php
/**
 * Link Counter - English Language
 *
 * @author      WBCE Community, Beach
 * @copyright   2026-01 WBCE Community, Beach
 * @license     MIT License
 * @version     1.3.0
 */

if (!defined('WB_PATH')) {
    exit('Direct access is not allowed');
}

// Module Info
$MOD_LINKCOUNTER = array(
    'MODULE_NAME'           => 'Link Counter',
    'MODULE_DESCRIPTION'    => 'Manage links with click statistics',

    // Navigation
    'MENU_OVERVIEW'         => 'Overview',
    'MENU_ADD'              => 'Add New',
    'MENU_STATS'            => 'Statistics',

    // Overview/List
    'HEADING_OVERVIEW'      => 'Link Overview',
    'TEXT_NO_DOWNLOADS'     => 'No links available yet.',
    'TEXT_ADD_FIRST'        => 'Create your first link.',

    // Table Headers
    'TH_ID'                 => 'ID',
    'TH_TITLE'              => 'Title',
    'TH_URL'                => 'URL',
    'TH_DESCRIPTION'        => 'Description',
    'TH_COUNTER'            => 'Clicks',
    'TH_STATUS'             => 'Status',
    'TH_CREATED'            => 'Created',
    'TH_ACTIONS'            => 'Actions',

    // Status
    'STATUS_ACTIVE'         => 'Active',
    'STATUS_INACTIVE'       => 'Inactive',

    // Add/Edit Form
    'HEADING_ADD'           => 'Add New Link',
    'HEADING_EDIT'          => 'Edit Link',
    'LABEL_TITLE'           => 'Title',
    'LABEL_LINK_TYPE'       => 'Link Type',
    'LABEL_URL'             => 'Target URL',
    'LABEL_PAGE'            => 'Internal Page',
    'LABEL_DESCRIPTION'     => 'Description',
    'LABEL_ACTIVE'          => 'Active',
    'LABEL_OPEN_TARGET'     => 'Open in new tab',
    'LINK_TYPE_URL'         => 'External URL',
    'LINK_TYPE_PAGE'        => 'Internal Page',
    'SELECT_PAGE'           => '-- Select Page --',
    'PLACEHOLDER_TITLE'     => 'e.g. Product Catalog 2026',
    'PLACEHOLDER_URL'       => 'https://example.com/download.pdf',
    'PLACEHOLDER_DESC'      => 'Optional: Description for internal purposes',
    'HELP_TITLE'            => 'Display name for the link',
    'HELP_LINK_TYPE'        => 'Choose between external URL or internal WBCE page',
    'HELP_URL'              => 'Full URL (e.g. https://example.com/file.pdf)',
    'HELP_PAGE'             => 'Select a page from your WBCE installation',
    'HELP_DESCRIPTION'      => 'Internal note or description',
    'HELP_ACTIVE'           => 'Only active links are displayed in frontend',
    'HELP_OPEN_TARGET'      => 'Link will be opened in a new browser tab',
    'LABEL_EXIT_NOTICE'     => 'Notice when leaving the website',
    'HELP_EXIT_NOTICE'      => 'A notice page is shown before redirecting to a foreign website. The click is counted only when "Continue" is clicked. The text is set under Settings.',
    'TITLE_EXIT_NOTICE_ICON' => 'With exit notice',

    // Exit Notice (Frontend)
    'EXIT_NOTICE_TITLE'        => 'You are leaving this website',
    'EXIT_NOTICE_DEFAULT_TEXT' => 'You are now being redirected to {host}. The operators of external websites are solely responsible for their content.',
    'EXIT_NOTICE_TARGET'       => 'Destination:',
    'EXIT_NOTICE_CONTINUE'     => 'Continue to %s',
    'EXIT_NOTICE_BACK'         => 'Back',

    // Buttons
    'BTN_SAVE'              => 'Save',
    'BTN_CANCEL'            => 'Cancel',
    'BTN_BACK'              => 'Back',
    'BTN_ADD'               => 'Add',
    'BTN_EDIT'              => 'Edit',
    'BTN_DELETE'            => 'Delete',
    'BTN_RESET_COUNTER'     => 'Reset Counter',
    'BTN_EXPORT'            => 'Export as CSV',
    'BTN_VIEW_STATS'        => 'Statistics',
    'BTN_SETTINGS'          => 'Settings',

    // Messages - Success
    'SUCCESS_SAVED'         => 'Link saved successfully.',
    'SUCCESS_DELETED'       => 'Link deleted successfully.',
    'SUCCESS_COUNTER_RESET' => 'Counter reset successfully.',

    // Messages - Errors
    'ERROR_TITLE_EMPTY'     => 'Please enter a title.',
    'ERROR_URL_EMPTY'       => 'Please enter a URL.',
    'ERROR_URL_INVALID'     => 'The entered URL is invalid.',
    'ERROR_NOT_FOUND'       => 'Link not found.',
    'ERROR_DELETE_FAILED'   => 'Error deleting link.',
    'ERROR_SAVE_FAILED'     => 'Error saving link.',
    'ERROR_SECURITY'        => 'Security error: Invalid token.',

    // Confirm Dialogs
    'CONFIRM_DELETE'        => 'Do you really want to delete this link?',
    'CONFIRM_RESET'         => 'Do you really want to reset the counter?',

    // Statistics
    'HEADING_STATS'         => 'Link Statistics',
    'TEXT_TOTAL_CLICKS'     => 'Total Clicks',
    'TEXT_AVG_CLICKS'       => 'Average',
    'TEXT_TOP_DOWNLOADS'    => 'Top Links',

    // Export
    'EXPORT_FILENAME'       => 'linkcounter-export',

    // Droplet Help
    'DROPLET_USAGE_HEADING'     => 'Droplet Usage',
    'DROPLET_CODE_HEADING'      => 'Droplet Code for this Link',
    'DROPLET_CODE_INFO'         => 'The link text will be automatically taken from the title field.',
    'DROPLET_LINKCOUNTER_DESC'  => 'Creates a tracked link using the title from the database as link text.',
    'DROPLET_LINKSTATS_DESC'    => 'Displays a table with link statistics showing the most clicked links.',

    // Filter
    'FILTER_ALL'            => 'All',
    'FILTER_ACTIVE'         => 'Active',
    'FILTER_INACTIVE'       => 'Inactive',

    // Sort
    'SORT_BY'               => 'Sort by',
    'SORT_ID_ASC'           => 'ID (ascending)',
    'SORT_ID_DESC'          => 'ID (descending)',
    'SORT_TITLE_ASC'        => 'Title (A-Z)',
    'SORT_TITLE_DESC'       => 'Title (Z-A)',
    'SORT_CLICKS_ASC'       => 'Clicks (ascending)',
    'SORT_CLICKS_DESC'      => 'Clicks (descending)',
    'SORT_DATE_ASC'         => 'Date (oldest first)',
    'SORT_DATE_DESC'        => 'Date (newest first)',

    // Settings
    'HEADING_SETTINGS'              => 'Settings',
    'SETTINGS_CRAWLER_PROTECTION'   => 'Crawler Protection Configuration',
    'SETTINGS_CRAWLER_DESCRIPTION'  => 'Crawler protection filters automated link calls based on the time between page load and click. This reduces bot traffic in your statistics.',
    'SETTINGS_ENABLE_PROTECTION'    => 'Enable crawler protection',
    'SETTINGS_ENABLE_PROTECTION_HELP' => 'When enabled, clicks with too short delay will be detected as crawlers.',
    'SETTINGS_MIN_DELAY'            => 'Minimum delay (in milliseconds)',
    'SETTINGS_MIN_DELAY_HELP'       => 'Clicks that occur faster will be detected as crawlers. Recommended: 500ms (0.5 seconds). Range: 100-10000ms.',
    'SETTINGS_CRAWLER_ACTION'       => 'Action when crawler detected',
    'SETTINGS_CRAWLER_ACTION_HELP'  => 'Choose what should happen when a crawler is detected.',
    'SETTINGS_ACTION_SKIP_COUNT'    => 'Redirect without counting (recommended)',
    'SETTINGS_ACTION_BLOCK'         => 'Do not redirect',
    'SETTINGS_HOW_IT_WORKS'         => 'How does crawler protection work?',
    'SETTINGS_HOW_POINT_1'          => 'JavaScript captures the time at page load and when clicking a link',
    'SETTINGS_HOW_POINT_2'          => 'The timestamps are obfuscated and transmitted as URL parameters',
    'SETTINGS_HOW_POINT_3'          => 'The server checks the time difference against the configured minimum time',
    'SETTINGS_HOW_POINT_4'          => 'Crawlers without JavaScript or with too short wait time are automatically filtered',
    'SUCCESS_SETTINGS_SAVED'        => 'Settings saved successfully.',
    'SETTINGS_EXIT_NOTICE'          => 'Notice when leaving the website',
    'SETTINGS_EXIT_NOTICE_DESCRIPTION' => 'This notice page is shown before external links that have the option "Notice when leaving the website" enabled.',
    'SETTINGS_EXIT_NOTICE_TEXT'     => 'Notice text',
    'SETTINGS_EXIT_NOTICE_TEXT_HELP' => 'Leave empty for the default text. Placeholders: {host} = destination domain, {title} = link title. Maximum 255 characters.',
);

?>
