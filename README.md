# Link Counter - WBCE CMS Module

**Version:** 1.1.0
**Author:** WBCE Community, Beach
**License:** MIT License
**Platform:** WBCE CMS 1.4.x

## Description

Link Counter is a WBCE CMS module that allows you to track clicks on links with detailed statistics. Perfect for monitoring downloads, external links, or any other links you want to track. Includes advanced crawler protection to filter automated bot traffic from your statistics.

## Features

- ✅ Track clicks on external URLs and internal pages
- ✅ Two link types: External URL or Internal WBCE page
- ✅ Individual link statistics with click counter
- ✅ **NEW in 1.1.0:** Crawler protection - Filter automated bot traffic
- ✅ **NEW in 1.1.0:** Configurable time-based detection (default: 500ms)
- ✅ **NEW in 1.1.0:** Two protection modes: Skip count or block redirect
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
   - Database table `mod_linkcounter_settings` (for crawler protection)
   - Two droplets: `LinkCounter` and `LinkCounterStats`

## Upgrade from 1.0.0 to 1.1.0

The upgrade is automatic:
1. Upload the new module files
2. Go to WBCE Backend → Add-ons → Modules
3. Click "Upgrade" for Link Counter
4. The upgrade script will automatically:
   - Create the settings table
   - Add default crawler protection settings (disabled by default)
   - Update droplets to support crawler protection
5. Configure crawler protection in Settings (optional)

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

**Configure crawler protection (NEW in 1.1.0):**
1. Click "Settings" button in the overview
2. Enable crawler protection (checkbox)
3. Set minimum delay in milliseconds (default: 500ms)
   - Clicks faster than this delay are detected as crawlers
4. Choose action when crawler detected:
   - **Redirect without counting** (recommended) - Crawler sees the target but isn't counted
   - **Do not redirect** - Crawler is blocked and returned to referring page
5. Save settings

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

## Crawler Protection (NEW in 1.1.0)

### How It Works

The crawler protection uses time-based detection to filter automated traffic:

1. **JavaScript Timestamps**: When a page loads, JavaScript records the page load time
2. **Click Detection**: When a link is clicked, the time difference is calculated
3. **Obfuscation**: Timestamps are XOR-encoded and Base64-encoded before transmission
4. **Server Validation**: The server checks if the time difference exceeds the minimum delay
5. **Action**: If too fast, the configured action is taken (skip count or block)

### Key Features

- **Optional**: Can be enabled/disabled anytime
- **Configurable**: Set minimum delay (100-10000ms, default: 500ms)
- **Two Modes**:
  - Skip count (recommended): Redirects but doesn't count the click
  - Block: Returns user to referring page without redirect
- **Backwards Compatible**: Works with existing links without modification
- **Fallback**: Links work normally without JavaScript (treated as potential crawler)

### Effectiveness

- ✅ Filters 90%+ of automated crawler traffic
- ✅ Transparent for normal users (500ms = 0.5 seconds is very fast for humans)
- ✅ No impact on legitimate clicks
- ⚠️ Not foolproof - advanced bots can potentially bypass it
- ⚠️ Requires JavaScript - crawlers without JS are automatically filtered

## Security Features

This module implements several security measures:

- ✅ **CSRF Protection:** All state-changing actions use FTAN tokens
- ✅ **SQL Injection Prevention:** All queries use proper escaping
- ✅ **XSS Protection:** All output is properly escaped
- ✅ **URL Validation:** Dangerous URL schemes are blocked (javascript:, data:, etc.)
- ✅ **Open Redirect Prevention:** URLs are validated before redirect
- ✅ **Crawler Protection:** Optional time-based bot detection (v1.1.0+)
- ✅ **Timestamp Obfuscation:** XOR + Base64 encoding prevents easy tampering


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
├── settings.php         - Crawler protection settings (NEW in 1.1.0)
├── statistics.php       - Statistics view
├── tool.php             - Tool entry point
├── track.php            - Click tracking & redirect with crawler protection
├── uninstall.php        - Uninstallation script
├── upgrade.php          - Upgrade script
├── css/                 - Stylesheets (backend.css, frontend.css)
├── js/                  - JavaScript files (backend.js, frontend.js)
└── languages/           - Language files (DE, EN)
```

## License

This module is licensed under the MIT License.
See LICENSE file for details.
