# Quyết định kiến trúc (ADR)

Mỗi ADR: bối cảnh → quyết định → hệ quả. Nguồn lý do chi tiết: mục 5 của
[requirements.md](../01-requirements/requirements.md). ADR mới thêm ở cuối, không sửa ADR cũ (đánh dấu
`Thay thế bởi ADR-xxx` nếu đổi ý).

## ADR-001 - Modular monolith trên Laravel
**Quyết định:** một codebase, một DB, tám module (Identity, Academic, Teaching, Enrollment, Attendance, Grading,
Announcement, Reporting) trong `app/Modules`, mỗi module tự đăng ký qua `ModuleServiceProvider`.
**Vì sao:** đăng ký học phần/điểm danh/điểm xoay quanh `enrollments`, cần ACID + khóa dòng; quy mô vài nghìn người
dùng; nhóm nhỏ. Microservices và SPA tách bị loại (xem mục 5.3-5.5 của requirements).
**Hệ quả:** ranh giới module được **kiểm tra tự động** bởi `tests/Architecture/ModuleBoundaryTest`.

## ADR-002 - Luồng tầng cố định
`Route → Middleware (role/permission) → Controller (mỏng) → FormRequest → Policy → Service → Eloquent → View/JsonResource`.
Controller không chứa nghiệp vụ; Service không biết HTTP; Policy chỉ kiểm quyền theo đối tượng. Mẫu tham chiếu hoàn
chỉnh: module **Identity** (đăng nhập). Module khác sao chép mẫu này.

## ADR-003 - RBAC bằng Spatie Permission, hai lớp kiểm tra
Role `admin`, `lecturer`, `student`; permission dạng `resource.action` định nghĩa tại
`App\Shared\Authorization\Permission` (nguồn duy nhất cho ma trận 2.5, seed bởi `RolePermissionSeeder`).
Lớp 1: middleware `role:`/`permission:` ở route. Lớp 2: Policy theo đối tượng (chặn IDOR).
**Super Admin** = role `admin` + permission `roles.manage` gán **trực tiếp** (không thuộc role nào).

## ADR-004 - `enrollments` là bảng trung tâm
Điểm danh, điểm thành phần và kết quả đều trỏ `enrollment_id`. Dữ liệu học vụ dùng `restrictOnDelete` và soft delete,
không xóa cứng; bản ghi đăng ký bị hủy giữ lại với `status = dropped`.

## ADR-005 - Đăng ký học phần: transaction ngắn + khóa dòng + chuỗi rule
`register` = `DB::transaction` → `lockForUpdate` dòng `class_sections` → `EnrollmentRuleChecker` chạy các
`EnrollmentRule` độc lập → tạo enrollment → tăng `enrolled_count` → commit → phát `EnrollmentCreated`. Chốt chặn cuối
là unique index. Thêm/bớt quy tắc = sửa danh sách rule trong `EnrollmentServiceProvider`.
**Lưu ý kiểm thử:** SQLite bỏ qua `lockForUpdate`; test đồng thời thật phải chạy trên MySQL (CI có job MySQL).

## ADR-006 - Audit: phần GHI ở `Shared`, module Reporting chỉ ĐỌC
**Bối cảnh:** tài liệu đặt `audit_logs`/`AuditLogger` trong module Reporting (Phase 2) nhưng BR-10 và FR-RPT-03 (Must)
đòi mọi module ghi audit từ Phase 1 - nếu để ở Reporting thì Identity/Grading/Attendance phải phụ thuộc ngược vào
module cuối chuỗi. **Quyết định:** `AuditLog`, `AuditLogger`, trait `Auditable` và migration nằm ở `App\Shared`;
Reporting sở hữu phần xem/lọc (FR-RPT-03). `audit_logs` append-only (model chặn update/delete). Trait `Auditable`
chỉ bắt được thay đổi qua model; `upsert()`/`insert()` hàng loạt không bắn event nên Service phải gọi
`AuditLogger::record()` thủ công.

## ADR-007 - Contract thay vì truy vấn chéo module
Enrollment cần biết sinh viên đã đạt học phần tiên quyết chưa (dữ liệu của Grading, trong khi Grading phụ thuộc
Enrollment). Giải quyết bằng interface `Grading\Contracts\PassedCoursesProvider`; Enrollment chỉ được dùng
`Grading\Contracts` và `Grading\Models`. Giao tiếp ngược chiều khác dùng Event.

## ADR-008 - Cấu hình nghiệp vụ trong `config/sms.php`
Thang điểm, ngưỡng đạt, chính sách học lại, giới hạn tín chỉ, cửa sổ điểm danh, ngưỡng vắng, giới hạn đăng nhập sai
đều đọc từ config (ghi đè bằng `.env`), không hardcode trong Service. `GradeCalculator`/`GpaCalculator` là lớp
thuần (không phụ thuộc framework) nên test đơn vị chạy trong mili-giây.
**Điểm tổng kết** làm tròn 2 chữ số trước khi quy đổi, so sánh bằng `>=` ngưỡng dưới của dải (8.45 → B+, 8.5 → A).

## ADR-009 - Lịch học ISO-8601 và tách `class_schedules` / `class_sessions`
`day_of_week` 1 = Thứ hai … 7 = Chủ nhật (repo cũ dùng 2..8). Lịch lặp dùng để phát hiện xung đột (`ScheduleSlot`:
cùng thứ, giao tiết, giao khoảng ngày; tiết liền kề không tính là trùng); buổi cụ thể là đơn vị điểm danh.

## ADR-010 - Hạ tầng Docker
Một lệnh `docker compose up` (hoặc `./setup.sh`). `app` chờ MySQL/Redis `healthy` rồi `migrate --seed`; `queue` và
`scheduler` chờ `app` healthy (cờ `storage/framework/.ready`). Dịch vụ dev (mailpit, phpmyadmin, vite) nằm ở profile
`dev`. Image `production` đa tầng, mã nguồn nằm trong image.
