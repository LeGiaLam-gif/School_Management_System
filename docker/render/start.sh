#!/bin/sh
set -eu

: "${PORT:=10000}"
export PORT

envsubst '${PORT}' \
    < /etc/nginx/templates/default.conf.template \
    > /etc/nginx/conf.d/default.conf

exec /usr/local/bin/entrypoint.sh /usr/bin/supervisord -n -c /etc/supervisor/conf.d/sms.conf
