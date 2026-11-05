#!/bin/bash
# Tao them CSDL `${DB_DATABASE}_testing` de chay `php artisan test` tren MySQL trong docker.
set -e

mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" <<SQL
CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE}_testing\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON \`${MYSQL_DATABASE}_testing\`.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;
SQL
