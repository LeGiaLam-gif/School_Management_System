# Hợp đồng giữa các module

Những gì module này **được phép dựa vào** ở module khác. Thay đổi chữ ký ở đây là thay đổi phá vỡ: báo cả nhóm và sửa
cùng PR với người dùng của nó. Nền tảng: [module-map.md](../03-architecture/module-map.md).

## Contract (interface)
| Interface | Nhà cung cấp | Người dùng | Ý nghĩa |
|---|---|---|---|
| `Grading\Contracts\PassedCoursesProvider::passedCourseIds(int $studentId): array` | Grading (`FinalResultPassedCoursesProvider`, bind trong `GradingServiceProvider`) | Enrollment (`PrerequisiteRule`, BR-02) | id học phần sinh viên đã đạt (đã công bố + `is_passed`) |
| `Enrollment\Services\Rules\EnrollmentRule::check(EnrollmentContext): void` | Enrollment | Enrollment | một quy tắc đăng ký; ném `BusinessRuleException` khi vi phạm |

## Service được module khác gọi
| Service | Người gọi | Ghi chú |
|---|---|---|
| `Identity\Services\UserService` (`createStudent`, `createLecturer`, `deactivate`, `activate`) | Academic/Admin UI, import CSV | tạo user + hồ sơ + role trong 1 transaction |
| `Academic\Services\SemesterService`, `CourseService` | Teaching, Enrollment | `setCurrent`, `scheduleRegistration`, `setPrerequisites` |
| `Enrollment\Services\EnrollmentService` (`register`, `drop`) | Admin UI (đăng ký hộ), Student UI | luôn qua service, không `Enrollment::create` trực tiếp |
| `Attendance\Services\AttendanceService::summaryForEnrollment` | Reporting, Student UI | |
| `Grading\Queries\StudentGpaQuery` | Student UI, Reporting | chỉ tính kết quả đã công bố |
| `Announcement\Services\AnnouncementService::resolveAudience` | Announcement job | |
| `Shared\Audit\AuditLogger::record` | mọi module | hành động có lý do (mở khóa điểm, ghi đè đăng ký, sửa điểm danh đã chốt) |

## Hợp đồng sự kiện
Xem bảng "Sự kiện" ở module-map. Event mang **model**, không mang dữ liệu nhạy cảm (mật khẩu, token). Listener chạy qua
queue; Listener không được làm chậm luồng chính của người dùng.

## Hợp đồng route
- Trang theo vai trò: `/admin`, `/lecturer`, `/student` (middleware `role:`).
- Route module: `admin/<module>/...` (Admin), `lecturer/...`, `student/...`; middleware `['auth', 'active', 'permission:...']`.
- Tên route: `<vai-trò>.<module>.<hành-động>`, vd `student.enrollment.register`.
- API (Phase 2): `/api/v1/...`, bật bằng `SMS_API_ENABLED=true`, dùng chung Service.

## Quy ước thông báo lỗi nghiệp vụ
`BusinessRuleException($message, $rule)`; `$rule` là `BR-xx` (mục 9.3) hoặc `MODULE-TOPIC`
(vd `ENR-WINDOW-CLOSED`, `ACAD-PREREQ-CYCLE`). Web: quay lại kèm lỗi `business_rule`; JSON: HTTP 422 `{message, rule}`.
