# Bản đồ module

## Cấu trúc thư mục

```
app/
  Modules/
    <Module>/
      Controllers/  Requests/  Policies/  Services/  Models/  Resources/
      Events/  Listeners/  Jobs/  Queries/  Middleware/
      Support/            # lớp thuần (calculator, value object), không phụ thuộc DB
      Contracts/          # interface cung cấp cho module khác
      Views/              # Blade, namespace `<module>::` (vd identity::auth.login)
      Database/{Migrations,Seeders,Factories}
      Routes/{web.php,api.php}
      Providers/<Module>ServiceProvider.php
  Shared/
    Audit/  Authorization/  Enums/  Exceptions/  Providers/  Traits/
config/sms.php            # quy tắc nghiệp vụ cấu hình được
database/migrations/      # chỉ bảng hạ tầng Laravel (cache, jobs, notifications, audit_logs)
database/seeders/         # DatabaseSeeder + DemoDataSeeder (điều phối seeder của module)
lang/vi/                  # tiếng Việt (mặc định); thiếu key tự rơi về `en`
resources/views/layouts/  # layout dùng chung
tests/{Unit,Feature,Architecture}
docker/{php,nginx,mysql}
```

Migration được nạp từ mọi module và sắp xếp **toàn cục theo tên file**, nên prefix thời gian chính là thứ tự phụ thuộc
FK (`2026_11_04_0001xx` Identity cơ bản → `0002xx` Academic → `0003xx` hồ sơ SV/GV → `0004xx` Teaching → `0005xx`
Enrollment → `0006xx` Attendance → `0007xx` Grading → `0008xx` Announcement). Migration mới thêm sau cùng, dùng ngày
hiện tại, không chèn vào giữa.

## Module, thực thể, phụ thuộc

| Module | Sở hữu bảng | Được gọi Service/Support/Contracts của | Phase |
|---|---|---|---|
| Identity | users, students, lecturers (+ bảng Spatie) | - | 1 |
| Academic | departments, programs, courses, course_prerequisites, program_courses, semesters, rooms | Identity | 1 |
| Teaching | class_sections, class_schedules, class_sessions | Academic, Identity | 1 |
| Enrollment | enrollments | Teaching, Academic, Identity, Grading (**chỉ** Contracts) | 1 |
| Attendance | attendance_records | Enrollment, Teaching, Identity | 1 |
| Grading | grade_components, grades, final_results | Enrollment, Teaching, Academic, Identity | 1 |
| Announcement | announcements | Teaching, Enrollment, Identity | 1-2 |
| Reporting | (đọc mọi module; xem/lọc audit_logs) | tất cả | 2 |

Luôn được phép giữa các module (ngoại lệ có chủ đích, mục 5.6): quan hệ Eloquent tới **Model** của module khác, và
giao tiếp qua **Event**. Không bao giờ được phép: dùng `Controllers`, `Requests`, `Policies`, `Jobs`, `Listeners`,
`Queries`, `Resources`, `Middleware` của module khác. `Database/Factories` và `Database/Seeders` chỉ được tham chiếu từ
`Database/` của module khác. `Shared` chỉ được tham chiếu **Model** của module (vd `AuditLog → User`).

Quy tắc trên được ghi trong `tests/Architecture/ModuleBoundaryTest.php` (hằng `ALLOWED`); thêm phụ thuộc mới thì
sửa hằng đó **và** bảng này trong cùng PR.

## Sự kiện (Event)

| Event | Module phát | Listener dự kiến |
|---|---|---|
| UserCreated, PasswordResetRequested | Identity | gửi mail mật khẩu/đặt lại |
| EnrollmentCreated, EnrollmentDropped | Enrollment | mail xác nhận (queue), audit |
| AttendanceFinalized | Attendance | `CheckAbsenceThreshold` |
| AbsenceThresholdExceeded | Attendance | thông báo cho sinh viên và giảng viên (BR-09) |
| GradesPublished, GradesUnlocked | Grading | tính lại GPA, thông báo, audit |

## Thêm module mới
1. Tạo thư mục theo mẫu trên, `Providers/<Module>ServiceProvider` kế thừa `ModuleServiceProvider`.
2. Đăng ký trong `bootstrap/providers.php` (theo thứ tự phụ thuộc).
3. Khai báo trong `ModuleBoundaryTest::ALLOWED` và bảng ở trên.
4. Migration dùng timestamp sau cùng; viết test Unit/Feature/Authorization (200/403).
