# Link Counter – WBCE CMS Module

Counts clicks on links (external URLs or internal WBCE pages) and shows the statistics in the backend.

**Version:** 1.3.0 · **Platform:** WBCE CMS 1.4+ · **License:** MIT · **Author:** WBCE Community, Beach

## Features

- Tracked links to external URLs or internal WBCE pages, each with its own click counter
- Per link: active/inactive, open in new tab, optional exit notice before leaving the website
- Crawler protection: time-based bot filter (optional)
- Overview with filter and sort, CSV export, counter reset
- Two droplets for the frontend: single link and statistics table
- German and English

## Installation & Upgrade

Upload the release ZIP via **Backend → Add-ons → Modules → Install module**.
On upgrade, new database columns, settings and droplet code are added automatically; existing links and counters are kept.

## Usage

Manage links under **Backend → Admin-Tools → Link Counter**.

| Droplet | Output |
|---|---|
| `[[LinkCounter?id=5]]` | Tracked link, link text = title |
| `[[LinkCounterStats?limit=10]]` | Table of the most clicked links (default 10) |

## Settings

**Crawler protection** – JavaScript measures the time between page load and click. Faster clicks than the minimum delay (default 500 ms) are treated as bots: either redirected without counting (recommended) or not redirected at all. Clients without JavaScript are not counted while protection is enabled.

**Exit notice** – For external links with the option enabled, a notice page is shown before redirecting. The text is set once in the settings (placeholders `{host}`, `{title}`; empty = default text). The click is counted only when the visitor clicks *Continue*. The notice is skipped if the target is on your own host.

## Privacy

The module stores one aggregated counter per link. No IP addresses, cookies, user agents or per-click records are stored.

## Security

CSRF tokens (FTAN) for all actions, escaped database input and HTML output, only `http(s)` targets and relative URLs allowed (no `javascript:`, `data:` etc.).

## Changelog & License

See [CHANGELOG.md](CHANGELOG.md) and [LICENSE](LICENSE).
