#!/usr/bin/env bash
# Installs WordPress with Keystone, creates demo content, runs the theme checks and checks every template over HTTP.
# Usage: bash tests/integration/run.sh
set -euo pipefail
export MSYS_NO_PATHCONV=1   # Git Bash on Windows would otherwise rewrite "/%postname%/" into a Windows path.
cd "$(dirname "$0")/../.."

wp() { docker compose exec -T cli wp "$@"; }
SITE=http://127.0.0.1:8080

docker compose up -d
echo "waiting for WordPress"
for _ in $(seq 1 60); do curl -fsS -o /dev/null "$SITE/wp-admin/install.php" 2>/dev/null && break; sleep 2; done
if ! wp core is-installed 2>/dev/null; then
  wp core install --url="$SITE" --title="Riverside Dental" --admin_user=admin --admin_password=admin --admin_email=admin@example.com --skip-email
fi
wp rewrite structure '/%postname%/' --hard >/dev/null
wp theme activate keystone
wp eval-file tests/setup.php

echo "== inside WordPress"
wp eval-file tests/checks.php

echo "== every template over HTTP"
fail=0
ok() { echo "  ok    $1"; }
bad() { echo "  FAIL  $1"; fail=1; }
for path in / /about/ /news/ /brushing-guide/ "/?s=teeth" /category/uncategorized/ /no-such-page/; do
  RESP=$(curl -s -w '\n%{http_code}' "$SITE$path")
  CODE=${RESP##*$'\n'}
  BODY=${RESP%$'\n'*}
  want=200; [ "$path" = "/no-such-page/" ] && want=404
  H1=$( (grep -o '<h1' <<<"$BODY" || true) | wc -l | tr -d ' ')
  if [ "$CODE" = "$want" ] && ! grep -qi 'fatal error\|warning:' <<<"$BODY" && grep -qE 'wp-skip-link|wp--skip-link--target' <<<"$BODY" && [ "$H1" = "1" ]; then
    ok "$path → $CODE, one h1, skip link, no PHP errors"
  else
    bad "$path → $CODE (wanted $want), h1 count $H1"
  fi
done
HOME_HTML=$(curl -fsS "$SITE/")
grep -q 'wp-content/themes/keystone/.*\.js' <<<"$HOME_HTML" && bad "theme JavaScript on the page" || ok "no theme JavaScript"
grep -qE 'fonts\.googleapis|\.woff2' <<<"$HOME_HTML" && bad "web fonts requested" || ok "no web fonts"
grep -q '<html lang="en-US"' <<<"$HOME_HTML" && ok "document language set" || bad "html lang missing"

[ "$fail" = "0" ] && echo "all HTTP checks passed" || { echo "HTTP checks failed"; exit 1; }
