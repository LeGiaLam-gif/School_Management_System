# Kickoff - bắt đầu làm việc

## Chạy dự án
```bash
./setup.sh                 # docker compose up + migrate --seed
make test                  # php artisan test trong container
```
Ứng dụng: http://localhost:8080 · Mailpit: http://localhost:8025 · phpMyAdmin: http://localhost:8081.
Tài khoản mẫu (mật khẩu `password`): `superadmin@example.com`, `admin@example.com`, `gv1@example.com`, `sv1@example.com`.

Không dùng Docker (SQLite, nhanh để thử): sửa `.env`: `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`,
`SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, `MAIL_MAILER=log`; rồi
`touch database/database.sqlite && composer setup && php artisan serve`.

## Việc làm một lần sau khi áp dụng patch kiến trúc
1. `composer update` rồi commit `composer.lock` (patch không kèm lock vì được tạo khi không truy cập được Packagist).
2. `npm install` rồi commit `package-lock.json`.
3. `composer lint:fix` (Pint) và `composer analyse`; nếu Larastan báo lỗi cũ, sinh baseline
   (`vendor/bin/phpstan analyse --generate-baseline`) rồi bỏ `continue-on-error` ở job `analyse` trong CI.
4. Bật branch protection cho `main`: yêu cầu PR, review, và các job CI `Laravel Pint`, `Test (sqlite)`, `Test (mysql)`.
5. Chạy `php artisan test` trên MySQL ít nhất một lần.

## Đọc theo thứ tự
1. [requirements.md](../01-requirements/requirements.md) và [review-notes.md](../01-requirements/review-notes.md)
2. [decisions.md](../03-architecture/decisions.md), [module-map.md](../03-architecture/module-map.md)
3. Module mẫu: `app/Modules/Identity` (Request → Controller → Service → Policy → test)
4. [conventions.md](../03-architecture/conventions.md), [GIT-WORKFLOW.md](GIT-WORKFLOW.md)
