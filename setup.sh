#!/usr/bin/env bash
# Dung moi truong phat trien bang mot lenh:  ./setup.sh
# Yeu cau: Docker + Docker Compose v2.
set -euo pipefail

cd "$(dirname "$0")"

if [ ! -f .env ]; then
    cp .env.example .env
    echo ">> Da tao .env tu .env.example"
fi

echo ">> Build va khoi dong container (lan dau mat vai phut: cai composer + migrate --seed)"
docker compose up -d --build

echo ">> Cho ung dung san sang..."
for _ in $(seq 1 120); do
    status="$(docker compose ps --format '{{.Service}} {{.Health}}' app 2>/dev/null | awk '{print $2}')"
    [ "$status" = "healthy" ] && break
    sleep 3
done

if [ "${status:-}" != "healthy" ]; then
    echo "!! Ung dung chua san sang. Xem log:  docker compose logs -f app" >&2
    exit 1
fi

# shellcheck disable=SC1091
set -a; . ./.env; set +a

cat <<MSG

SMS da chay.
  Ung dung  : ${APP_URL:-http://localhost:8080}
  Mailpit   : http://localhost:${FORWARD_MAILPIT_DASHBOARD_PORT:-8025}
  phpMyAdmin: http://localhost:${FORWARD_PHPMYADMIN_PORT:-8081}

Tai khoan mau (mat khau: password):
  superadmin@example.com | admin@example.com | gv1@example.com | sv1@example.com

Chay test:  docker compose exec app php artisan test
MSG
