=== Privatus Analytics ===
Contributors: privatusanalytics
Tags: analytics, privacy, cookieless, gdpr, woocommerce
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Cookieless website analytics for WordPress and WooCommerce. No cookies, no personal data and no consent banner needed for analytics.

== Description ==

Privatus Analytics counts visits, sources, pages and conversions without cookies, local storage or fingerprinting. This plugin adds the Privatus Analytics tracking script to your site and records WooCommerce purchases.

The plugin does nothing until you enter your site id. You need a Privatus Analytics account to see reports. Sign up at [privatusanalytics.com](https://privatusanalytics.com).

**Features**

* One-field setup: paste your site id (it starts with pa_).
* Every tracker option: single-page-app mode, manual pageviews, modules (engagement, automatic events, Web Vitals, click maps, surveys), allowed domains, excluded paths, path masks, extra query parameters, canonical URLs and hash routing.
* Leave out logged-in users by role. Administrators are excluded by default.
* WooCommerce purchase tracking with revenue, currency, number of items and whether a coupon was used. Order ids, names, emails and addresses are never sent, and order-specific URLs are masked.
* A dashboard card with a button that opens your Privatus Analytics dashboard.
* A `[privatus_optout]` shortcode that lets visitors opt out of being counted, or back in.
* Suggested privacy policy text under Settings, Privacy.
* No cookies are set and nothing is written to the visitor's device, except the optional opt-out flag a visitor chooses to set.

= External service =

This plugin relies on the Privatus Analytics service, operated by Two Phase LLC (Wyoming, USA).

**What it is used for:** counting visits and conversions on your site and showing them in your Privatus Analytics dashboard.

**What is loaded and sent:** after you save a site id, every front-end page loads the script `https://privatusstats.com/js/pa.js` in the visitor's browser. That script sends the following to `https://privatusstats.com/api/event`: the page address (query parameters are removed except campaign parameters and any you allow), the referrer, the browser language, the screen width, the time zone name, and the names and properties of events (for WooCommerce, the order total, currency, item count and a coupon flag). Your site id is sent with each request. The visitor's IP address and browser details are used in memory only, together with a random value that changes every day, to count unique visits and spot bots. They are never stored. The visitor's country comes from the country code that Cloudflare adds to each request. Nothing is sent when the visitor has opted out, when the visitor is a logged-in user with an excluded role, or in the admin area.

**When:** only after you enter a site id in Settings, Privatus Analytics. If you change the script host setting, the same data goes to the host you enter.

**Optional modules:** if you turn on the engagement, automatic events, Web Vitals, click maps or surveys modules, the tracking script loads the matching file from the same host, such as `https://privatusstats.com/js/pa.clicks.js`.

**The dashboard card** is a plain link. It makes no request from your server and embeds nothing. The link opens `https://privatusanalytics.com` or the https address you enter.

* Terms of service: https://privatusanalytics.com/legal/terms
* Privacy policy: https://privatusanalytics.com/legal/privacy

== Installation ==

1. Upload the `privatus-analytics` folder to `/wp-content/plugins/`, or install it from Plugins, Add New.
2. Activate the plugin.
3. Go to Settings, Privatus Analytics and enter your site id.
4. Optional: turn on WooCommerce purchase tracking, and add `[privatus_optout]` to your privacy page.

== Frequently Asked Questions ==

= Do I need a cookie banner? =

Privatus Analytics stores nothing on the visitor's device and uses no cookies, so analytics does not need a banner by itself. Check with your own counsel for your country.

= Does the plugin work without a Privatus Analytics account? =

No. The plugin only adds the tracking script. The reports live in your Privatus Analytics account.

= Where do I find my site id? =

In your Privatus Analytics dashboard under Site settings, then Setup. It looks like pa_AbC123.

= How are WooCommerce purchases tracked? =

The plugin prints a small script on the order-received page that records one `purchase` event in the visitor's browser. It respects the visitor's opt-out. Order ids, names, emails and addresses are never sent.

= Will my own visits be counted? =

Not if you are logged in with an excluded role. Administrators are excluded by default.

= Caching plugins break the script. What do I do? =

Exclude `pa.js` from the combine, defer and delay JavaScript features of your caching or optimization plugin.

= Can I change the script tag from code? =

Yes. Use the `privatus_analytics_should_track` filter (a boolean) to skip tracking on a request. Use the `privatus_analytics_script_attributes` filter (an array) to change the script tag attributes.

= What is removed when I delete the plugin? =

Its settings and the per-order purchase marker. Your data in Privatus Analytics is not touched.

== Screenshots ==

1. The settings page with the site id and script options.
2. Exclusions and WooCommerce settings.
3. The dashboard card on the WordPress dashboard.
4. The opt-out shortcode on a privacy page.

== Changelog ==

= 1.0.1 =
* The suggested privacy policy text is easier to read and links to the Privatus Analytics privacy page.
* The suggested text now says correctly where the country comes from and what the opt-out button stores.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.1 =
Clearer suggested privacy policy text. Check Settings, Privacy, Policy guide and update your policy.

= 1.0.0 =
Initial release.
