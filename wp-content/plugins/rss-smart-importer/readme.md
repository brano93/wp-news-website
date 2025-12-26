
**Goal:** Generate a complete, production-ready WordPress plugin named **“RSS Smart Importer”** using only built-in WP APIs (no external packages). Follow WordPress coding standards, write clean, commented code, and keep it roughly **≤ 450 LOC** across multiple files.

### Core Features

1) **Multiple RSS feeds**
- Settings page with a textarea to enter multiple feed URLs (one per line).
- Use WordPress SimplePie via `fetch_feed()` to read each feed.

2) **Customizable scheduler (HH:MM) + Manual run**
- Scheduler options:
  - **Manual only** (default), i.e., no recurring cron.
  - Or user sets **Hours** and **Minutes** (e.g., every 2 hours 30 minutes).
- Convert to `interval_seconds = hours*3600 + minutes*60`.
- Validate min interval **5 minutes** and max **24 hours**.
- On saving settings, schedule or reschedule WP-Cron according to this custom interval.
- Provide a **“Run Now”** button to trigger import manually from the settings page.

3) **Duplicate & update detection**
- Use `<guid>` or fallback `<link>` as unique key.
- Save `_rss_guid` post meta.
- Maintain a normalized content **fingerprint** (e.g., `_rss_fp_v1`) to detect changes:
  - If GUID exists and fingerprint changed → **update** the existing post (content + images) and count as **updated**.
  - If unchanged → count as **duplicate** and skip.

4) **Post creation**
- Global setting for **post status**: `draft` or `publish`.
- Use feed item’s publication date (`pubDate`) as the post date when available.

5) **Image downloading & rewriting**
- Parse all `<img>` tags found inside `<content:encoded>`.
- Download each image to the Media Library (`media_sideload_image()` / `media_handle_sideload()`).
- **Rewrite** the post content so *all* image `src` point to the local URLs.
- Set the **first downloaded image** as the **featured image** (cover). Keep it in content too.

6) **Category mapping (kebab-case → Title Case)**
- Read the first `<category>` value from the feed item (e.g., `world-news`).
- Convert to **Title Case** (e.g., `World News`).
- If a WP category with that **name already exists**, assign it; otherwise assign **Uncategorized** (do **not** auto-create terms).

7) **Iframe option (allow vs strip)**
- Settings checkbox: **“Allow iframes”**.
- If checked → preserve all `<iframe>` tags (allow attributes such as `src`, `width`, `height`, `allow`, `allowfullscreen`, `frameborder`, `title`, `loading`).
- If unchecked → strip all `<iframe>` tags from imported content before saving.

8) **Stats & history**
- For each **run** and **per feed**, track:
  - `found`, `duplicates`, `imported`, `updated`, `failed`, and `duration (seconds)`.
- After clicking **Run Now**, display a results table (one row per feed + totals).
- Persist the **last 10 runs** to an option (e.g., `rsi_last_runs`) and show a simple history view.

9) **Locking & reliability**
- Use a transient lock (e.g., `rsi_lock`, TTL 10 minutes) to avoid overlapping imports.
- If one feed fails, continue with others and log the error.
- Log errors via `error_log()` with `[RSS Smart Importer]` prefix.

### Implementation Details

- **Sanitization:** Use `wp_kses_allowed_html('post')`. Conditionally allow or strip `<iframe>` based on the setting.
- **Images:** Handle HTTP timeouts, invalid MIME, and skip broken URLs without failing the entire item.
- **Batching:** Add a setting for “Items per feed per run” (default 10–20) to avoid long requests.
- **Dates:** Fallback to `current_time('mysql')` if `pubDate` missing/invalid.
- **Dedupe Query:** Use a fast `WP_Query` on `_rss_guid` before insert/update.
- **Manual button:** Implement via `admin-post.php` with nonce/capability checks.

### File Structure (required)

```
/wp-content/plugins/rss-smart-importer/
├─ rss-smart-importer.php              // main bootstrap: loads classes, hooks, activation/uninstall
├─ includes/
│  ├─ class-rsi-settings.php           // admin page (settings form, save, Run Now, results table, history)
│  ├─ class-rsi-importer.php           // cron + import orchestration, locking, stats aggregation
│  ├─ helpers-images.php               // sideload all <img>, rewrite HTML src to local, set featured image
│  └─ helpers-sanitize.php             // sanitize HTML, allow/strip iframes, build normalized content fingerprint
└─ uninstall.php                       // unschedule and clean up options on uninstall
```

### Admin Settings (minimum fields)

- Textarea: **Feed URLs** (one per line)
- Number: **Items per feed per run** (default 10)
- Select: **Post status** (`draft` | `publish`)
- Checkbox: **Allow iframes**
- Scheduler inputs: **Hours** and **Minutes** (min 5 min, max 24h)
- **Run Now** button
- Display **Last Run** results (table) + **Run History** (last 10)

### API Configuration

The plugin supports external API configuration via constants or environment variables. These settings take precedence over the plugin's admin settings.

#### RSI_API_HOST

Sets the base URL for the RSS API service. The plugin will use this URL to make API requests.

**Priority order:**
1. Constant `RSI_API_HOST` (in `wp-config.php`)
2. Constant `RSI_API_BASE_URL` (alternative name)
3. Environment variable `RSI_API_HOST` or `RSI_API_BASE_URL`
4. Default: `http://localhost:3030`

**Example in wp-config.php:**
```php
define('RSI_API_HOST', 'https://your-api-host.com');
```

#### RSI_API_BEARER_TOKEN

Sets the Bearer token for API authentication. This token is used in the `Authorization` header for all API requests.

**Priority order:**
1. Constant `RSI_API_BEARER_TOKEN` (in `wp-config.php`)
2. Constant `RSI_BEARER_TOKEN` (alternative name)
3. Environment variable `RSI_API_BEARER_TOKEN` or `RSI_BEARER_TOKEN`
4. Plugin settings (admin UI)

**Example in wp-config.php:**
```php
define('RSI_API_BEARER_TOKEN', 'your-bearer-token-here');
```

**Security Note:** When set via constant or environment variable, the token takes precedence over the admin UI setting. The admin UI will display a notice showing only the last 4 characters of the token for security purposes.

**Important:** 
- Constants and environment variables take precedence over plugin settings
- If a token is set via constant/environment variable, the admin UI input field will be disabled
- The plugin UI will show a notice indicating the token source (constant, environment variable, or settings)

#### RSI_IMAGE_PROXY_HOST and RSI_IMAGE_PROXY_PORT

Configures a proxy server for image downloads. All image download requests will be routed through the specified proxy.

**Priority order:**
1. Constants `RSI_IMAGE_PROXY_HOST` and `RSI_IMAGE_PROXY_PORT` (in `wp-config.php`)
2. Environment variables `RSI_IMAGE_PROXY_HOST` and `RSI_IMAGE_PROXY_PORT`
3. If not configured, proxy is not used (standard WordPress download method)

**Note:** Both `RSI_IMAGE_PROXY_HOST` and `RSI_IMAGE_PROXY_PORT` must be set for the proxy to be used.

**Example in wp-config.php:**
```php
define('RSI_IMAGE_PROXY_HOST', '127.0.0.1');
define('RSI_IMAGE_PROXY_PORT', '20003');
```

**Example in .env file (Docker):**
```
RSI_IMAGE_PROXY_HOST=127.0.0.1
RSI_IMAGE_PROXY_PORT=20003
```

**Note:** 
- Image downloads use cURL with HTTP proxy support
- Only image downloads (jpg, png, gif, webp, etc.) will use the proxy
- The proxy must be accessible from the WordPress server

### Acceptance Criteria

- Plugin activates without errors on a fresh site.
- With valid feeds, **Run Now**:
  - Creates posts with correct status/date.
  - Downloads all images and rewrites `src`.
  - Sets first image as featured.
  - Maps category when an exact-named term exists; otherwise uses Uncategorized.
  - Respects “Allow iframes” setting.
  - Outputs a per-feed results table and updates run history.
- Duplicate items are skipped; changed items are updated and counted correctly.
- Scheduler:
  - Manual mode does not schedule recurring events.
  - Custom HH:MM schedules a recurring event (min 5 min, max 24 hours) or uses a self-rescheduling approach.
  - Changing the interval reschedules properly.
- Concurrency lock prevents overlapping runs.
- Errors are logged with `[RSS Smart Importer]` prefix.

### Deliverables

- All PHP files described above, with concise inline comments.
- A brief summary at the end of generation explaining where each file goes and how to activate the plugin.

> **Important:** Do **not** use external libraries. Keep code size concise (≈450 LOC total). Favor clarity, guardrails, and reliable behavior over micro-optimizations.

---

## Tips

- Start by generating a **single-file prototype** if desired, then ask Cursor to refactor into the multi-file structure above.
- Test on a staging WP with a couple of known RSS feeds to verify images, categories, and iframe handling.

---
