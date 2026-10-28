=== Privatus Analytics ===
Contributors: privatusanalytics
Tags: analytics, privacy, cookieless, gdpr, woocommerce
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simple, private analytics. No cookies, bots kept out of your numbers, and a dashboard you understand in seconds.

== Description ==

**Website stats should be easy. Google Analytics is not.**

Google Analytics is slow and confusing. You click through menu after menu just to see how many people visited. It sends what your visitors do to Google. In many countries it means showing a cookie pop-up (the box that asks visitors to accept cookies), and many people click "reject", so you never see them at all. Bots then pad the numbers that are left.

**Privatus Analytics is the simple, private way to see who visits your site.**

It counts visits without cookies and never stores IP addresses. Bots are filtered out before they are counted. Your data is never sold or shared. You paste one code into this plugin and you are done.

= What you get =

* **A dashboard you understand in seconds.** Visitors, top pages, where people came from and which countries they are in, all on one screen. No training needed.
* **No cookie pop-up needed for our analytics.** Privatus Analytics sets no cookies and stores no personal data, so there is nothing to ask visitors about. Laws differ by country, so check your own legal advice.
* **Honest numbers.** Known bots, fake traffic and spam are left out on every plan, so you see real people. Pro adds a bot score for every visit to catch smarter bots.
* **A fast site.** The tracking script is about 3 KB and loads without blocking your page.
* **Sales you can see.** With WooCommerce, every order shows up as revenue, so you know which pages and sources bring in money. No names, emails or order numbers are ever sent.
* **Data you own.** We never sell it and never show you ads. You can pull it out any time through the API.
* **Your history, kept.** Moving from Google Analytics? You can import your old Google Analytics 4 data into Privatus Analytics.

= Start free =

The Free plan has 10,000 events a month and 3 sites, with no card. Every new account also gets 14 days of Pro free, with no card. [Create your account](https://privatusanalytics.com), copy your site id, and paste it into Settings, Privatus Analytics. You will see your first visitors within minutes.

= What the plugin does =

* One-field setup: paste your site id (it starts with pa_).
* Leaves out your own visits. Administrators are left out by default, and you can pick other roles.
* WooCommerce purchase tracking with the order total, currency, number of items and whether a coupon was used. Order page addresses are cleaned before they are sent, so order numbers never show up in your reports.
* WS Form tracking: one `WS Form Submit` event each time a WS Form form is sent, with the form id and form name. Turn it into a goal to see which pages and sources bring in form sends. What the visitor typed is never sent.
* A `[privatus_optout]` shortcode that lets visitors choose not to be counted, or be counted again.
* Suggested privacy policy text under Settings, Privacy.
* A dashboard card with a button that opens your Privatus Analytics dashboard.
* Every tracker option for advanced sites: single-page-app mode, manual pageviews, extra modules (engagement, automatic events, page speed, click maps and surveys), allowed domains, excluded paths, path masks, extra query parameters, canonical URLs and hash routing.
* Nothing is written to the visitor's device, except the optional opt-out choice a visitor makes.

= External service =

This plugin relies on the Privatus Analytics service, operated by Two Phase LLC (Wyoming, USA).

**What it is used for:** counting visits and conversions on your site and showing them in your Privatus Analytics dashboard.

**What is loaded and sent:** after you save a site id, every front-end page loads the script `https://privatusstats.com/js/pa.js` in the visitor's browser. That script sends the following to the endpoint `https://privatusstats.com/api/event` (it only accepts data sent by the script, so it shows an error if you open it in a browser): the page address (query parameters are removed except campaign parameters and any you allow), the referrer, the browser language, the screen width, the time zone name, and the names and properties of events (for WooCommerce, the order total, currency, item count and a coupon flag, and for WS Form, the form id and form name). Your site id is sent with each request. The visitor's IP address and browser details are used in memory only, together with a random value that changes every day, to count unique visits and spot bots. They are never stored. The visitor's country comes from the country code that Cloudflare adds to each request. Nothing is sent when the visitor has opted out, when the visitor is a logged-in user with an excluded role, or in the admin area.

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

= How is this different from Google Analytics? =

Privatus Analytics shows the numbers most site owners need on one simple screen. It sets no cookies, never stores IP addresses, filters out bots and never sends your visitors' data to Google or to ad networks. Google Analytics is free because it uses your visitors' data. Privatus Analytics never sells it.

= Do I need a cookie pop-up? =

Privatus Analytics sets no cookies and stores nothing on the visitor's device, so our analytics does not need a pop-up by itself. Other tools on your site might. Laws differ by country, so check your own legal advice.

= Is there a free plan? =

Yes. The Free plan has 10,000 events a month and 3 sites, with no card. An event is a pageview or another action you track. Every new account also gets 14 days of Pro free, with no card.

= Does the plugin work without a Privatus Analytics account? =

No. The plugin adds the tracking script. Your reports live in your Privatus Analytics account.

= Where do I find my site id? =

In your Privatus Analytics dashboard under Site settings, then Setup. It looks like pa_AbC123.

= Will it slow down my site? =

No. The script is about 3 KB and loads with `defer`, so it waits until your page is ready.

= How are WooCommerce purchases tracked? =

The plugin prints a small script on the order-received page that records one `purchase` event in the visitor's browser. It respects the visitor's opt-out. Order numbers, names, emails and addresses are never sent.

= How are WS Form submissions tracked? =

Turn on "Track form submissions" under Settings, Privatus Analytics, WS Form. The plugin then listens for the WS Form events that fire when a form is sent (wsf-submit-success, or wsf-submit-complete for forms that post the normal way). Each send records one `WS Form Submit` event in the visitor's browser, with the form id and form name. What the visitor typed is never sent. It respects the visitor's opt-out. In Privatus Analytics, make a goal from the event to see which pages and sources bring in form sends.

= Will my own visits be counted? =

Not if you are logged in with an excluded role. Administrators are excluded by default.

= Caching plugins break the script. What do I do? =

Exclude `pa.js` from the combine, defer and delay JavaScript features of your caching or optimization plugin.

= Can I change the script tag from code? =

Yes. Use the `privatus_analytics_should_track` filter (a boolean) to skip tracking on a request. Use the `privatus_analytics_script_attributes` filter (an array) to change the script tag attributes.

= How do I leave a review? =

Thank you! Go to the [Privatus Analytics reviews page](https://wordpress.org/support/plugin/privatus-analytics/reviews/#new-post) on WordPress.org and click "Add your review". Kind words and honest feedback both help us make the plugin better.

= Will the plugin ask me for a review? =

Once. After 14 days, a short note on the Settings, Privatus Analytics page asks if you would like to leave a review. Only administrators see it, it never shows on other admin pages, and "Don't show this again" hides it for good.

= What is removed when I delete the plugin? =

Its settings, the review note setting and the per-order purchase marker. Your data in Privatus Analytics is not touched.

== Screenshots ==

1. Your Privatus Analytics dashboard: visitors, top pages and where they came from, all on one screen.
2. See which countries your visitors are in, which devices they use and which campaigns bring them in.
3. Setup takes one field. Paste your site id and save.
4. Leave out your own visits and turn on WooCommerce sales tracking.
5. A card on your WordPress dashboard opens your stats in one click.
6. Let visitors choose not to be counted with the opt-out shortcode.

== Changelog ==

= 1.0.4 =
* New: WS Form tracking. Turn it on under Settings, Privatus Analytics, WS Form, and each form send records a "WS Form Submit" event with the form id and form name.

= 1.0.3 =
* A clearer plugin description, with dashboard screenshots.
* After 14 days, a single note on the settings page asks administrators for a review. "Don't show this again" hides it for good. Deleting the plugin removes its setting.

= 1.0.2 =
* The plugin home page link now goes to the WordPress install guide.
* The tracking script and the WooCommerce purchase event are now loaded the standard WordPress way, with wp_enqueue_script.
* Needs WordPress 6.4 or newer.

= 1.0.1 =
* The suggested privacy policy text is easier to read and links to the Privatus Analytics privacy page.
* The suggested text now says correctly where the country comes from and what the opt-out button stores.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.3 =
A clearer description and dashboard screenshots. No change to tracking.

= 1.0.2 =
The tracking script now loads the standard WordPress way. Needs WordPress 6.4 or newer.

= 1.0.1 =
Clearer suggested privacy policy text. Check Settings, Privacy, Policy guide and update your policy.

= 1.0.0 =
Initial release.
