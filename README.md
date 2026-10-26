# Privatus Analytics for WordPress

**Simple, private analytics for WordPress and WooCommerce. No cookies, honest numbers, and a dashboard you
understand in seconds.** [Get it on WordPress.org](https://wordpress.org/plugins/privatus-analytics/).

## Why Privatus Analytics

**Google Analytics is slow, confusing and hungry for data.** It sends what your visitors do to Google. In many
countries it means showing a cookie pop-up (the box that asks visitors to accept cookies), and many people click
"reject", so you never see them. Bots pad the numbers that are left.

**Privatus Analytics fixes that.** It counts visits to your WordPress site without cookies and never stores IP
addresses. Bots are filtered out before they are counted. Your data is never sold or shared.

What you get:

- **A dashboard you understand in seconds.** Visitors, top pages and where people came from, all on one screen.
- **No cookie pop-up needed for our analytics.** We set no cookies and store no personal data. Laws differ by
  country, so check your own legal advice.
- **Honest numbers.** Known bots and fake traffic are left out on every plan.
- **A fast site.** The tracking script is about 3 KB, tiny next to most tracking tags.
- **Sales you can see.** WooCommerce orders show up as revenue, with no names, emails or order numbers sent.
- **Data you own.** We never sell it or show you ads, and you can pull it out any time through the API.

**Start free.** The Free plan has 10,000 events a month and 3 sites, with no card. Every new account also gets
14 days of Pro free, with no card. [Create your account](https://privatusanalytics.com).

## What this plugin does

- One-field setup: paste your site id and the tracker is added to every front-end page.
- Leaves out logged-in users by role (administrators by default).
- Tracks WooCommerce purchases from the visitor's browser, with no personal data.
- An opt-out shortcode, suggested privacy policy text and a dashboard card.
- Needs PHP 7.4 or newer and WordPress 6.4 or newer. The plugin lives in [`privatus-analytics/`](privatus-analytics/).

## Install

1. Zip the plugin folder: `cd integrations/wordpress && zip -r privatus-analytics.zip privatus-analytics`
   (or copy `privatus-analytics/` to `wp-content/plugins/`).
2. WordPress admin → Plugins → Add New → Upload Plugin → choose the zip → **Activate**.
3. **Settings → Privatus Analytics**: enter your site id (`pa_…`) and save. That is it. The tracker is printed in
   `<head>` on every front-end page.

### Updates

The plugin is submitted to the WordPress.org plugin directory and updates through it once listed.
Until then it is installed from a zip. The `Update URI` header was removed because the directory does not accept it.

## Settings

| Setting | Effect |
| --- | --- |
| Site ID | `data-site` (required). |
| Script host | Origin serving `/js/pa.js` and receiving hits (default `https://privatusstats.com`). |
| SPA mode, manual, modules (engage/auto/vitals/clicks), allowed domains, excluded paths, path masks, extra params, canonical, hash | Map 1:1 to the tracker's `data-*` attributes. |
| Do not track roles | Logged-in users with any selected role get no snippet (administrators by default). |
| WooCommerce: track purchases | Sends a `purchase` event from the browser on the thank-you page. |
| Dashboard link + capability | Optional `https://` link. The WordPress dashboard shows a small card with a button that opens it in a new tab (no iframe, no request to our servers), for users with the chosen capability. |

The snippet is skipped in wp-admin, previews, the Customizer, AJAX, feeds and robots.txt. It is printed with
`wp_print_inline_script_tag()` (queue stub) and `wp_print_script_tag()` (the `defer` tag with `data-*` attributes),
so all values are escaped by core. Filters: `privatus_analytics_should_track`, `privatus_analytics_script_attributes`.

### WooCommerce

On `woocommerce_thankyou` a single `purchase` event is sent per order (guarded by order meta `_privatus_tracked`,
HPOS compatible) with `revenue` (order total), `currency`, `items` (item count) and `coupon_used` (boolean).
**No order id, email, name or address is ever sent.** The order-received, order-pay and view-order URLs are
masked automatically (`/checkout/order-received/*->/checkout/order-received/`) so order ids never appear in page URLs.

The plugin prints `privatus.track('purchase', {...})` using the queue stub, so the event is sent from the visitor's
browser by the tracker. It respects the visitor opt-out, excluded roles and ad-blocker choices.

### Opt-out shortcode

`[privatus_optout]` renders a toggle button (attributes: `opt_out_label`, `opt_in_label`, `opted_out`, `opted_in`).
It calls `privatus.optOut()` / `optIn()` when the tracker is loaded and otherwise sets the same
`localStorage.privatus_optout = "1"` flag directly. It is the only thing ever stored in the browser.

### Privacy policy

Suggested text is registered via `wp_add_privacy_policy_content` (Settings → Privacy → Policy guide).

### Review request

After the plugin has been active for 14 days, Settings, Privatus Analytics shows administrators one note asking for a
review on WordPress.org. It shows on no other admin page. "Don't show this again" (a nonce-checked `admin-post.php`
action that needs `manage_options`) hides it for good. The state lives in the `privatus_analytics_review` option.

### Uninstall

Deleting the plugin removes its options (settings and the review note state) and all `_privatus_tracked` order meta (posts meta and HPOS
`wc_orders_meta`, every site on multisite).

## License: GPL-2.0-or-later

The plugin is released under a single licence, GPL-2.0-or-later, which is what the WordPress.org plugin directory
requires. The plugin header, readme.txt and `privatus-analytics/LICENSE` all say the same.
See `privatus-analytics/LICENSE` and `privatus-analytics/LICENSE-GPL-2.0.txt`.

## Development checks

```sh
# PHP syntax (PHP 8.3)
docker run --rm -v "$PWD":/w -w /w php:8.3-cli sh -c 'for f in $(find . -name "*.php"); do php -l $f || exit 1; done'

# WordPress Coding Standards + PHP 7.4 compatibility (tools installed inside the container only)
docker run --rm -v "$PWD/privatus-analytics":/w:ro composer:2 sh -c 'cd /tmp && composer config -g allow-plugins.dealerdirect/phpcodesniffer-composer-installer true && composer require -q --dev wp-coding-standards/wpcs:^3 phpcompatibility/phpcompatibility-wp:"*" && vendor/bin/phpcs --standard=WordPress,PHPCompatibilityWP --runtime-set testVersion 7.4- --runtime-set minimum_wp_version 6.4 --runtime-set text_domain privatus-analytics --extensions=php /w'
```
