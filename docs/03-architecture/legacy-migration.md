# Chuyển từ repo cũ (LAPTRINHWEB-PROJECT) sang cấu trúc mới

Repo cũ (Laravel 12, schema giai đoạn 2) là schema đơn giản: `role` enum trên `users`, môn học có đúng một lịch cố
định, điểm giữa/cuối kỳ cứng 40/60, điểm danh theo ngày. Tài liệu yêu cầu đòi hỏi schema chi tiết hơn, nên việc "đưa
sang" là **chuyển đổi có ánh xạ**, không sao chép nguyên file. Không có file nào từ repo cũ bị bỏ qua mà không có lý do
trong bảng dưới.

## Ánh xạ bảng / model

| Repo cũ | Repo mới | Thay đổi |
|---|---|---|
| `users.role` (enum) | Spatie `roles` (`admin`, `lecturer`, `student`) | bỏ cột `role`; thêm `code`, `status`, `must_change_password`, `last_login_at`, soft delete; `phone` chuyển xuống hồ sơ; `is_active` → `status` |
| `faculties` / `Faculty` | `departments` / `Department` | đổi tên; thêm `description` |
| `majors` / `Major` | `programs` / `Program` | đổi tên; thêm `total_credits_required`, `duration_years` |
| `classes` / `Classroom` | **không đưa sang** | lớp hành chính thuộc mô hình niên chế; tài liệu giả định tín chỉ. `students.class_id` → `program_id` + `cohort_year` (K22 → 2022) |
| `lecturers` | `lecturers` | `faculty_id` → `department_id`; `degree` → `academic_title`; thêm `phone`, `office` |
| `students` | `students` | `class_id` → `program_id` + `cohort_year`; `dob` → `date_of_birth`; thêm `gender`, `phone`, `status` |
| `subjects` / `Subject` | `courses` / `Course` | đổi tên; `faculty_id` (nullable) → `department_id` (bắt buộc); thêm tiết LT/TH, `is_active`, soft delete; thêm `course_prerequisites`, `program_courses` |
| `semesters` | `semesters` | `school_year` → `academic_year`; thêm `code`, hạn đăng ký, hạn rút, min/max tín chỉ |
| `course_sections` | `class_sections` + `class_schedules` + `class_sessions` + `rooms` | tách lịch ra bảng riêng; `room` (chuỗi) → bảng `rooms`; `max_students` → `capacity` + `enrolled_count`; `is_open` → `status` (vòng đời 5 trạng thái); `day_of_week` 2..8 → ISO 1..7 |
| `enrollments` | `enrollments` | thêm `status`, `registered_at`, `dropped_at`, `registered_by`, `override_reason`; **không còn là Pivot** |
| `grades` (midterm/final/total) | `grade_components` + `grades` + `final_results` | điểm theo thành phần cấu hình được; tổng kết lưu ở `final_results` kèm công bố/khóa |
| `attendances` (theo ngày) | `attendance_records` (theo buổi) | gắn `class_session_id`; thêm `marked_by`, `marked_at` |
| `announcements` | `announcements` | `content` → `body`; thêm `scope`, `is_pinned`, `published_at` |

## Ánh xạ logic

| Repo cũ | Repo mới |
|---|---|
| `Grade::saving` tự tính `total = 0.4*GK + 0.6*CK` | `GradeCalculator::weightedTotal` (thành phần/trọng số cấu hình); seeder giữ 40/60 làm cấu hình mẫu |
| `Grade::gradePoint()` / `letter()` (ngưỡng `>=`) | `GradeCalculator::toGpa4` / `toLetter`, thang đọc từ `config/sms.php` (cùng ngưỡng) |
| `Student::gpa()` | `GpaCalculator` (thuần) + `StudentGpaQuery` (chỉ kết quả đã công bố, có chính sách học lại) |
| `CourseSection::isFull()` (đếm enrollments) | `ClassSection::isFull()` đọc `enrolled_count` (an toàn khi đồng thời) |
| `User::isAdmin()` … (so cột `role`) | giữ tên hàm, nay dựa trên Spatie `hasRole` |
| `Semester::scopeCurrent` | giữ nguyên |
| `UserSeeder`, `FacultySeeder`, `AcademicSeeder`, `CourseSectionSeeder` | `IdentityDemoSeeder`, `AcademicDemoSeeder`, `TeachingDemoSeeder`, `EnrollmentDemoSeeder`, `AttendanceDemoSeeder`, `GradingDemoSeeder`, `AnnouncementDemoSeeder`; **giữ nguyên dữ liệu mẫu** (tài khoản `admin@`, `gv1-3@`, `sv1-12@example.com`, mật khẩu `password`, 6 môn, 2 học kỳ, 5 lớp, công thức điểm/điểm danh mẫu) |
| `docs/database.md` | thay bằng `docs/02-erd/erd.md` |

## Tệp hạ tầng

| Repo cũ | Xử lý |
|---|---|
| `artisan`, `bootstrap/`, `config/*`, `public/`, `storage/`, `vite.config.js`, `resources/js`, `tests/TestCase.php`, migration cache/jobs | đưa sang; `config/app.php` (timezone/locale/faker) và `config/auth.php` (model User) được chỉnh |
| `composer.json` | viết lại: PHP ^8.3, thêm `spatie/laravel-permission`, `larastan`, bỏ `sail`/`pail`, thêm script `lint`/`analyse`/`ci` |
| `composer.lock` | **không đưa sang** (khóa phụ thuộc cũ). Chạy `composer update` một lần rồi commit lock mới |
| `package.json`, `vite.config.js`, `resources/css/app.css` | đổi Tailwind → Bootstrap 5 + Alpine.js theo mục 5.1 |
| `compose.yaml` (Laravel Sail) | thay bằng `docker-compose.yml` (nginx, app, queue, scheduler, mysql, redis, mailpit, phpmyadmin) |
| `.env.example` (sqlite) | viết lại cho MySQL/Redis/Mailpit |
| `README.md` mặc định của Laravel, `welcome.blade.php`, `tests/*/ExampleTest.php`, `UserFactory` cũ | thay bằng nội dung của dự án |

## Nếu cần chuyển dữ liệu đã nhập ở DB cũ
Chưa có script vì repo cũ chỉ có dữ liệu seed. Nếu sau này cần: viết một migration/command một lần theo bảng ánh xạ
trên (đặc biệt `users.role` → Spatie, `course_sections` → 3 bảng, `attendances` theo ngày → theo buổi) và chạy
trước khi mở đăng ký.
