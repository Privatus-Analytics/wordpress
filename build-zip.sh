#!/bin/sh
# Builds the WordPress.org submission zip: build/privatus-analytics.zip (folder privatus-analytics/ inside).
# Fails if the plugin carries an Update URI header or a licence that is not GPL-2.0-or-later.
set -e
cd "$(dirname "$0")"
if grep -rqi "^[ *]*Update URI" privatus-analytics/*.php; then echo "Update URI header found" >&2; exit 1; fi
grep -q "^ \* License:           GPL-2.0-or-later" privatus-analytics/privatus-analytics.php || { echo "header licence is not GPL-2.0-or-later" >&2; exit 1; }
grep -q "^License: GPLv2 or later" privatus-analytics/readme.txt || { echo "readme licence is not GPLv2 or later" >&2; exit 1; }
mkdir -p build
rm -f build/privatus-analytics.zip
zip -rq build/privatus-analytics.zip privatus-analytics -x '*.DS_Store' '*/.git/*'
unzip -l build/privatus-analytics.zip | tail -1
