#!/bin/sh
# End-to-end check of WS Form tracking in Docker.
# Runs WordPress + MariaDB, installs WS Form LITE from WordPress.org and this
# plugin, creates the "Contact Us" form, then a headless browser fills it in
# and sends it. The test passes when exactly one "WS Form Submit" event with
# the form id and form name is queued, and nothing the visitor typed is in it.
# pa.js is blocked in the browser, so the event stays in window.privatus.q.
# Usage: ./tests/ws-form/verify-docker.sh
set -e
cd "$(dirname "$0")/../.."
ROOT="$PWD"
NET=pa-wp-wsform
PORT="${PORT:-8091}"
cleanup() { docker rm -f pa-wp-web pa-wp-db >/dev/null 2>&1 || true; docker network rm $NET >/dev/null 2>&1 || true; }
trap cleanup EXIT
cleanup

docker network create $NET >/dev/null
docker run -d --name pa-wp-db --network $NET -e MARIADB_RANDOM_ROOT_PASSWORD=1 -e MARIADB_DATABASE=wp \
  -e MARIADB_USER=wp -e MARIADB_PASSWORD=wppw mariadb:11 >/dev/null
docker run -d --name pa-wp-web --network $NET -p "$PORT:80" \
  -e WORDPRESS_DB_HOST=pa-wp-db -e WORDPRESS_DB_USER=wp -e WORDPRESS_DB_PASSWORD=wppw -e WORDPRESS_DB_NAME=wp \
  -e WORDPRESS_DEBUG=1 -e WORDPRESS_CONFIG_EXTRA="define('WP_DEBUG_LOG', true); define('WP_DEBUG_DISPLAY', false);" \
  -v "$ROOT/privatus-analytics":/var/www/html/wp-content/plugins/privatus-analytics:ro \
  wordpress:php8.3-apache >/dev/null

wp() { docker run --rm --network $NET --volumes-from pa-wp-web --user 33:33 -e HOME=/tmp \
  -e WORDPRESS_DB_HOST=pa-wp-db -e WORDPRESS_DB_USER=wp -e WORDPRESS_DB_PASSWORD=wppw -e WORDPRESS_DB_NAME=wp \
  wordpress:cli-2.12-php8.3 wp "$@"; }

for i in $(seq 1 60); do curl -fs -o /dev/null "http://localhost:$PORT/wp-admin/install.php" && break; sleep 1; done
sleep 3
wp core install --url="http://localhost:$PORT" --title=Example --admin_user=admin --admin_password="$(head -c 12 /dev/urandom | od -An -tx1 | tr -d ' \n')" \
  --admin_email=admin@example.com --skip-email >/dev/null
wp plugin install ws-form --activate >/dev/null
wp plugin activate privatus-analytics >/dev/null
wp option update privatus_analytics_settings '{"site_id":"pa_Docker123","modules":["engage"],"exclude_roles":["administrator"],"wsform_enabled":true}' --format=json >/dev/null
FORM_ID=$(wp --user=admin eval '$f = new WS_Form_Form(); $id = $f->db_create_from_template("contact-us"); $f->id = $id; $f->db_publish(true); echo $id;')
PAGE_URL=$(wp post create --post_type=page --post_status=publish --post_title=Contact --post_content="[ws_form id=\"$FORM_ID\"]" --porcelain | xargs -I{} sh -c "echo http://localhost:$PORT/?page_id={}")
echo "form $FORM_ID on $PAGE_URL"

CHECK_JS=$(mktemp -d)/check.mjs
cat > "$CHECK_JS" <<'JS'
import { chromium } from 'playwright';
const url = process.argv[2], formId = Number(process.argv[3]), mode = process.argv[4];
const browser = await chromium.launch();
const page = await browser.newPage();
await page.route('**/js/pa.js', r => r.abort());
await page.goto(url);
await page.waitForSelector('form.wsf-form input[type="email"]');
const fill = async (sel, v) => { for (const el of await page.$$(sel)) { if (await el.isVisible()) await el.fill(v); } };
await fill('form.wsf-form input[type="text"]', 'Testname');
await fill('form.wsf-form input[type="email"]', 'person@example.com');
await fill('form.wsf-form input[type="tel"]', '5551234');
await fill('form.wsf-form textarea', 'Secret message text');
for (const el of await page.$$('form.wsf-form input[type="checkbox"]')) { await el.check({ force: true }); }
if (mode === 'success') {
  await page.click('form.wsf-form button[type="submit"], form.wsf-form input[type="submit"]');
  await page.waitForFunction(() => (window.privatus && window.privatus.q || []).some(c => c[0] === 'track'), null, { timeout: 20000 });
} else {
  await page.waitForTimeout(1500);
}
const q = await page.evaluate(() => (window.privatus && window.privatus.q) || []);
const tracks = q.filter(c => c[0] === 'track');
console.log(JSON.stringify(tracks));
let fail = 0;
const check = (name, ok) => { console.log((ok ? 'ok   ' : 'FAIL ') + name); if (!ok) fail++; };
if (mode === 'success') {
  check('one WS Form Submit event after sending the form', tracks.length === 1 && tracks[0][1][0] === 'WS Form Submit');
  const p = tracks[0] && tracks[0][1][1] || {};
  check('event has the form id', p.form_id === formId);
  check('event has the form name', p.form === 'Contact Us');
  check('nothing the visitor typed is sent', !JSON.stringify(q).match(/Testname|person@example|5551234|Secret message/));
} else {
  check('no event before the form is sent', tracks.length === 0);
}
await browser.close();
process.exit(fail ? 1 : 0);
JS

PW="mcr.microsoft.com/playwright:v1.56.1-jammy"
# The browser joins the WordPress network and reaches the web container by name.
run_check_net() {
  docker run --rm --network $NET -v "$CHECK_JS":/work/check.mjs:ro -w /work "$PW" \
    sh -c 'npm init -y >/dev/null 2>&1 && npm i -s playwright@1.56.1 >/dev/null 2>&1 && node check.mjs "$@"' sh "$@"
}
INNER_URL=$(echo "$PAGE_URL" | sed "s#localhost:$PORT#pa-wp-web#")
wp option update home "http://pa-wp-web" >/dev/null
wp option update siteurl "http://pa-wp-web" >/dev/null
run_check_net "$INNER_URL" "$FORM_ID" idle
run_check_net "$INNER_URL" "$FORM_ID" success

# Setting off: no listener is printed.
wp option update privatus_analytics_settings '{"site_id":"pa_Docker123","modules":["engage"],"exclude_roles":["administrator"],"wsform_enabled":false}' --format=json >/dev/null
if docker exec pa-wp-web curl -fs "$INNER_URL" | grep -q "wsf-submit-success"; then echo "FAIL listener printed with the setting off"; exit 1; fi
echo "ok   no listener when the setting is off"

if docker exec pa-wp-web sh -c 'test -f /var/www/html/wp-content/debug.log && grep -i privatus /var/www/html/wp-content/debug.log'; then
  echo "FAIL PHP messages from the plugin"; exit 1
fi
echo "ok   no PHP messages from the plugin"
