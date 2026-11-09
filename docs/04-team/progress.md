# Tiến độ theo sprint

Ký hiệu: `[x]` đã có trong patch kiến trúc (có test), `[ ]` việc của sprint. Ưu tiên M/S/C theo requirements mục 3.

## Sprint 0 - Nền tảng
- [x] Docker Compose (nginx, app, queue, scheduler, mysql, redis, mailpit, phpmyadmin, vite), `setup.sh`, `Makefile`
- [x] Laravel 12 + Spatie Permission + layout Blade (Bootstrap 5 + Alpine), `lang/vi`
- [x] CI: Pint, Larastan, composer audit, test (SQLite + MySQL), build asset, build image
- [x] Cấu trúc modular monolith (8 module), `ModuleServiceProvider`, kiểm thử ranh giới module
- [x] Đăng nhập được (email hoặc mã số), phân quyền route 200/403
- [ ] Commit `composer.lock`, `package-lock.json`; Pint/Larastan sạch; bật branch protection (xem kickoff.md)

## Sprint 1 - Identity + Academic
- [x] Migration + model + factory + seeder cho toàn bộ bảng Identity/Academic; `RolePermissionSeeder`, `SuperAdminSeeder`
- [x] `AuthService` (FR-AUTH-01, 05 khóa sau 5 lần sai), `UserService`, `UserPolicy`, `CourseService` (tiên quyết không vòng), `SemesterService`
- [x] FR-AUTH-03 quên/đặt lại mật khẩu (M)
- [ ] FR-AUTH-04 trang buộc đổi mật khẩu lần đầu (S)
- [x] FR-AUTH-06 hồ sơ cá nhân (M)
- [ ] FR-USER-01..05 CRUD tài khoản, hồ sơ SV/GV, reset mật khẩu hộ, tìm kiếm/lọc/phân trang; FR-USER-03 import CSV (S)
- [ ] FR-ACAD-01..05 CRUD khoa, ngành, học phần, học kỳ, phòng; FR-ACAD-03 khung chương trình (S); cache danh mục + Observer
- [ ] Controller, FormRequest, Blade và route cho các mục trên (theo mẫu Identity)

## Sprint 2 - Teaching
- [x] Migration + model + factory cho `class_sections`, `class_schedules`, `class_sessions`; `ClassSectionPolicy`, `ClassSectionStatus`
- [x] `ScheduleSlot` (xung đột có test biên), `SessionDateGenerator`
- [ ] `ClassSectionService` (create, assignLecturer, changeStatus theo vòng đời) - FR-CLS-01, 05
- [ ] `ScheduleService::detectConflicts` (DB: phòng + giảng viên, BR-06) và `generateSessions` (giữ buổi đã điểm danh) - FR-CLS-02..04
- [ ] `TimetableQuery` cho **giảng viên / Admin / phòng** - FR-TT-01, 02. TKB sinh viên cần đăng ký nên chuyển sang Sprint 3
- [ ] Scheduler: tự chuyển `open → in_progress → finished` (mục 9.1)

## Sprint 3 - Enrollment
- [x] `EnrollmentService` (register/drop: transaction + `lockForUpdate`), `EnrollmentRuleChecker`, `ClassOpenRule`, `RegistrationWindowRule`, `CapacityRule`, `EnrollmentPolicy`
- [ ] `PrerequisiteRule` (BR-02, qua `PassedCoursesProvider`), `DuplicateCourseRule` (BR-01), `ScheduleConflictRule` (BR-04, dùng `ScheduleSlot`), `CreditLimitRule` (BR-03) - mỗi rule một test riêng
- [ ] **Test đồng thời** (MySQL): hai tiến trình đăng ký lớp còn một chỗ, chỉ một thành công (NFR-DATA-01)
- [ ] `adminOverride` (FR-ENR-05, S) có lý do + audit; listener gửi mail xác nhận (FR-ENR-06, C)
- [ ] FR-ENR-01..03 giao diện sinh viên; **TKB sinh viên** (FR-TT-01)

## Sprint 4 - Attendance + Grading
- [x] Migration + model; `AbsenceRateCalculator`, `AttendanceService::summaryForEnrollment`, `AttendancePolicy`
- [x] `GradeCalculator`, `GpaCalculator`, `StudentGpaQuery`, `PassedCoursesProvider`, `GradePolicy`
- [ ] `AttendanceService::markSession` (upsert + audit thủ công), `finalizeSession`, cửa sổ ±7 ngày, `CheckAbsenceThreshold` (FR-ATT-01..04)
- [ ] `GradeComponentService` (tổng trọng số = 100, BR-07), `GradeService` (nhập tay, import Excel), `GradePublishService` (publish/unlock + audit, BR-08) - FR-GRD-01..07
- [ ] Listener `GradesPublished`: chuyển enrollment `completed`, tính lại GPA, thông báo

## Sprint 5 - Announcement + Reporting + hoàn thiện
- [x] Migration + model + `AnnouncementService::resolveAudience`, `AnnouncementPolicy`
- [ ] FR-ANN-01..03: đăng/hẹn giờ/ghim, đã đọc, `SendAnnouncementNotification` (queue)
- [ ] FR-RPT-01..03: dashboard theo role, phổ điểm/chuyên cần, xem + lọc audit log
- [ ] Xuất PDF/Excel (`ExportService`, queue); FR-TT-03 iCal/PDF
- [ ] Seeder quy mô demo (1 Super Admin, ~20 GV, 300 SV, 3 khoa, 6 ngành, 60 học phần, 2 học kỳ, 40 lớp) bằng Factory
- [ ] README, báo cáo đồ án (`docs/05-report`), kịch bản demo 3 actor
