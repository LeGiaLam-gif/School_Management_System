# School Management System (SMS)

Hệ thống quản lý đào tạo tín chỉ cho một cơ sở đào tạo: tài khoản và phân quyền, cấu trúc đào tạo, mở lớp học phần,
đăng ký học phần, thời khóa biểu, điểm danh, chấm điểm và GPA, thông báo, báo cáo, audit log.

**Stack:** Laravel 12 (PHP 8.3) · MySQL 8 · Redis · Nginx + PHP-FPM · Docker Compose · Spatie Permission ·
Blade + Bootstrap 5 + Alpine.js · PHPUnit · GitHub Actions.
**Kiến trúc:** modular monolith, 8 module, tầng Controller → FormRequest → Policy → Service → Eloquent.

## Chạy nhanh

```bash
./setup.sh          # cần Docker; tạo .env, build, migrate --seed
```

| Dịch vụ | Địa chỉ |
|---|---|
| Ứng dụng | http://localhost:8080 |
| Mailpit (bắt email) | http://localhost:8025 |
| phpMyAdmin | http://localhost:8081 |

Tài khoản mẫu (mật khẩu `password`): `superadmin@example.com`, `admin@example.com`, `gv1@example.com` … `gv3@`,
`sv1@example.com` … `sv12@`. Đăng nhập bằng email **hoặc** mã số (`SV00001`, `GV001`).

Lệnh thường dùng: `make test`, `make lint`, `make analyse`, `make fresh`, `make shell` (xem `Makefile`).
Chạy không cần Docker: xem [docs/04-team/kickoff.md](docs/04-team/kickoff.md).

## Cấu trúc

```
app/Modules/<Module>/   Identity · Academic · Teaching · Enrollment · Attendance · Grading · Announcement · Reporting
app/Shared/             Enums, Audit, Authorization (permission), Exceptions, Traits, ModuleServiceProvider
config/sms.php          quy tắc nghiệp vụ cấu hình được (thang điểm, tín chỉ, ngưỡng vắng...)
database/               migration hạ tầng + seeder điều phối; migration nghiệp vụ nằm trong từng module
docker/ · docker-compose.yml · .github/workflows/ci.yml
tests/{Unit,Feature,Architecture}
docs/                   requirements, ERD, kiến trúc, quy trình nhóm, tiến độ, báo cáo
```

## Tài liệu

| | |
|---|---|
| Yêu cầu và thiết kế | [docs/01-requirements/requirements.md](docs/01-requirements/requirements.md) · [review-notes](docs/01-requirements/review-notes.md) |
| ERD | [docs/02-erd/erd.md](docs/02-erd/erd.md) |
| Kiến trúc | [decisions](docs/03-architecture/decisions.md) · [module-map](docs/03-architecture/module-map.md) · [conventions](docs/03-architecture/conventions.md) · [legacy-migration](docs/03-architecture/legacy-migration.md) |
| Nhóm | [kickoff](docs/04-team/kickoff.md) · [Git workflow](docs/04-team/GIT-WORKFLOW.md) · [contracts](docs/04-team/contracts.md) · [progress](docs/04-team/progress.md) |

## Kiểm thử

`php artisan test` chạy Unit (calculator, rule, lịch), Architecture (ranh giới module), Feature (đăng nhập, phân quyền
200/403, đăng ký học phần, GPA, toàn vẹn schema, dữ liệu mẫu). CI chạy trên SQLite và MySQL 8.
