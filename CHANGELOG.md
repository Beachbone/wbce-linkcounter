# Changelog

All notable changes to the Link Counter module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.0] - 2026-09-23

### Added
- **Exit notice for external links**: optional notice page shown before redirecting to a foreign website
  - New per-link toggle "Notice when leaving the website" (only for external URLs)
  - Global notice text in Settings, with placeholders `{host}` and `{title}`; empty = language default
  - Only shown when the target host differs from the site's own host
  - Click is counted only when the visitor clicks "Continue" (visitors who turn back are not counted)
  - Crawler-protection timestamp is carried over to the second request
  - Notice page is `noindex, nofollow`, not cached, supports light/dark mode, no external resources
  - "Back" returns to the previous page or closes the tab when the link was opened in a new tab
- New column `exit_notice` in `mod_linkcounter`, new setting `exit_notice_text` (added automatically on upgrade)
- Overview shows an icon for links with exit notice enabled

### Fixed
- Links with "open in new tab" opened two windows per click when a LinkCounter droplet was placed more than once on a page. The frontend script was included and initialized multiple times (caused by WBCE's droplet processing evaluating repeated placeholders again) and now initializes only once per page.
- Invalid link targets (e.g. deleted internal page) are no longer counted before redirecting to the homepage

### Changed
- Settings page heading is now just "Settings" (it covers crawler protection and exit notice)

## [1.2.0] - 2026-04-01

### Added
- **Open in new tab**: new per-link toggle; links open with `target="_blank" rel="noopener noreferrer"`
  - New column `open_target` ENUM('_self','_blank'), default `_self` (existing links keep their behavior)
  - Both droplets (`LinkCounter`, `LinkCounterStats`) respect the setting and are updated automatically on upgrade
  - Overview shows an icon for links that open in a new tab

### Changed
- Admin backend redesigned in Sesterze style (cards, toggle switches, Font Awesome 4.x icons, no external fonts or CDN)
  - `css/backend.css` rewritten, central font size token `--lc-font-base`, column widths via `colgroup`
  - Emojis in buttons replaced by Font Awesome icons
  - Droplet help and droplet code box removed from overview and edit form
- `js/frontend.js`: uses `window.open()` for links with `target="_blank"` so crawler protection also works for new tabs
- Save errors now include the database error message
- Checked for PHP 8.5 compatibility

## [1.1.0] - 2026-01-23

### Added
- **Crawler Protection System**: Time-based bot detection to filter automated traffic
  - JavaScript-based timestamp tracking (page load vs. click time)
  - XOR + Base64 obfuscation of timestamps
  - Server-side validation with configurable minimum delay (100-10000ms, default: 500ms)
  - Two protection modes: Skip count or block redirect
  - Safe referer-based redirect for blocked crawlers (same-domain only)
- **Settings Page**: New admin interface for crawler protection configuration
  - Enable/disable crawler protection
  - Configure minimum delay
  - Choose action (skip count or block)
  - Helpful documentation and usage info
- **Frontend JavaScript**: New `js/frontend.js` for crawler protection
  - Timestamp capture on page load
  - Click interception for tracked links
  - Automatic obfuscation and URL parameter injection
- **Settings Database Table**: New `mod_linkcounter_settings` table
  - Stores crawler protection configuration
  - Automatically created on install/upgrade

### Changed
- **Updated Droplets**: Both `LinkCounter` and `LinkCounterStats` droplets now include:
  - `data-linkcounter-id` attribute on links
  - `linkcounter-link` CSS class for JavaScript targeting
  - Automatic loading of `frontend.js` (loaded once per page)
- **Enhanced track.php**: Extended with crawler detection logic
  - Reads and validates timestamps from `_t` URL parameter
  - Implements time-based filtering
  - Logs detected crawlers to error log
  - Maintains backward compatibility (works without timestamps)
- **Improved CSS**: All inline styles moved to `backend.css`
  - Added settings page styles
  - Unified dropdown padding to `4px 10px`
  - Better organization and maintainability
- **Module Description**: Updated to mention crawler protection feature
- **Documentation**: Comprehensive README updates with crawler protection documentation

### Fixed
- Crawler redirect now goes to referring page instead of homepage (when blocked)
- Consistent padding for all dropdown/select elements

## [1.0.0] - 2026-01-21

### Added
- Initial release of Link Counter module
- Track clicks on external URLs and internal WBCE pages
- Two link types: External URL or Internal WBCE page
- Individual link statistics with click counter
- Active/Inactive status for links
- Admin interface for link management
  - Add/Edit/Delete links
  - Reset counters
  - Export to CSV
  - Filter and sort options
- Two droplets for easy integration:
  - `[[LinkCounter?id=X]]` - Display tracked link
  - `[[LinkCounterStats?limit=N]]` - Display statistics table
- Security features:
  - CSRF protection with FTAN tokens
  - SQL injection prevention
  - XSS protection
  - URL validation (blocks dangerous schemes)
  - Open redirect prevention
- Multi-language support (German, English)
- Responsive admin interface
- Database tables:
  - `mod_linkcounter` - Main link storage
- Clean uninstallation (removes tables and droplets)

[1.3.0]: https://github.com/Beachbone/wbce-linkcounter/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/Beachbone/wbce-linkcounter/compare/1.1.0...1.2.0
[1.1.0]: https://github.com/Beachbone/wbce-linkcounter/compare/1.0.0...1.1.0
[1.0.0]: https://github.com/Beachbone/wbce-linkcounter/releases/tag/1.0.0
