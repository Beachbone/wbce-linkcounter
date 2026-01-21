# Link Counter - WBCE CMS Module

**Version:** 1.0.0
**Author:** WBCE Community, Beach
**License:** MIT License
**Platform:** WBCE CMS 1.4.x

## Description

Link Counter is a WBCE CMS module that allows you to track clicks on links with detailed statistics. Perfect for monitoring downloads, external links, or any other links you want to track.

## Features

- ✅ Track clicks on external URLs and internal pages
- ✅ Two link types: External URL or Internal WBCE page
- ✅ Individual link statistics with click counter
- ✅ Active/Inactive status for links
- ✅ Easy integration via Droplets
- ✅ Export functionality (CSV)
- ✅ Filter and sort options
- ✅ Secure implementation with CSRF protection
- ✅ URL validation against malicious links

## Installation

1. Download the module files
2. Upload to `/modules/linkcounter/` in your WBCE installation
3. Go to WBCE Backend → Add-ons → Modules
4. Install the "Link Counter" module
5. The module will automatically create:
   - Database table `mod_linkcounter`
   - Two droplets: `LinkCounter` and `LinkCounterStats`

## Usage

### Admin Interface

Access the module via Backend → Admin-Tools → Link Counter

**Add a new link:**
1. Click "Add New"
2. Enter title and description
3. Choose link type (URL or Page)
4. Set active status
5. Save

**View statistics:**
- Overview shows all links with click counters
- Filter by active/inactive
- Sort by title, clicks, or date

### Droplets

#### LinkCounter
Creates a tracked link using the title from database.

**Syntax:**
```
[[LinkCounter?id=1]]
```

**Parameters:**
- `id` (required) - The ID of the link to display

**Example:**
```
[[LinkCounter?id=5]]
```

#### LinkCounterStats
Displays a table with link statistics.

**Syntax:**
```
[[LinkCounterStats?limit=10]]
```

**Parameters:**
- `limit` (optional) - Maximum number of links to show (default: 10)

**Example:**
```
[[LinkCounterStats?limit=5]]
```

## Security Features

This module implements several security measures:

- ✅ **CSRF Protection:** All state-changing actions use FTAN tokens
- ✅ **SQL Injection Prevention:** All queries use proper escaping
- ✅ **XSS Protection:** All output is properly escaped
- ✅ **URL Validation:** Dangerous URL schemes are blocked (javascript:, data:, etc.)
- ✅ **Open Redirect Prevention:** URLs are validated before redirect


## Files

```
linkcounter/
├── add.php              - Add/Edit link form
├── delete.php           - Delete link handler
├── export.php           - CSV export
├── info.php             - Module information
├── install.php          - Installation script
├── overview.php         - Main overview page
├── reset_counter.php    - Reset counter handler
├── save.php             - Save link handler
├── statistics.php       - Statistics view
├── tool.php             - Tool entry point
├── track.php            - Click tracking & redirect
├── uninstall.php        - Uninstallation script
├── upgrade.php          - Upgrade script
├── css/                 - Stylesheets
├── js/                  - JavaScript files
└── languages/           - Language files (DE, EN)
```

## License

This module is licensed under the MIT License.
See LICENSE file for details.
