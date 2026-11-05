# Ghi chú review requirement và cách xử lý trong cấu trúc

Kết quả đọc [requirements.md](requirements.md) (bản 1.0 nháp). Mỗi mục: vấn đề → quyết định đã áp dụng trong code.
Mục nào cần nhóm xác nhận được đánh dấu **[Cần xác nhận]**.

| # | Vấn đề trong tài liệu | Xử lý trong cấu trúc |
|---|---|---|
| 1 | `audit_logs` thuộc Reporting (Phase 2) nhưng BR-10 / FR-RPT-03 là Must và mọi module cần ghi audit từ Phase 1 | Phần ghi audit chuyển vào `App\Shared\Audit` (ADR-006); Reporting chỉ đọc/lọc. Migration `audit_logs` nằm ở `database/migrations` |
| 2 | Sprint 2 hứa "Student xem được thời khóa biểu tuần" nhưng thời khóa biểu của sinh viên cần `enrollments` (Sprint 3) | `progress.md`: Sprint 2 chỉ kiểm chứng TKB giảng viên/Admin/phòng; TKB sinh viên chuyển sang cuối Sprint 3 |
| 3 | Mục 6.4 ghi "chỉ chuyển tiến" nhưng 9.1 cho `grades_published → finished`; FR-CLS-05 thiếu `grades_published` | `ClassSectionStatus`: chuyển ngược duy nhất là mở khóa điểm (`isUnlockTransition`), chỉ Admin, bắt buộc lý do + audit |
| 4 | Bảng quy đổi điểm không nói làm tròn (8.4 vs 8.5) | Làm tròn 2 chữ số, so `>=` ngưỡng dưới (giữ hành vi code cũ); cấu hình trong `config/sms.php` **[Cần xác nhận với quy chế trường]** |
| 5 | Ma trận 2.5: Admin chỉ "hủy hộ" nhưng FR-ENR-05 là "thêm/hủy hộ"; Super Admin không có role riêng; FR-AUTH-04 là Should nhưng 6.2 coi là bắt buộc | Quyền `enrollments.override` cho cả thêm và hủy; Super Admin = role `admin` + `roles.manage` gán trực tiếp (`SuperAdminSeeder`); `must_change_password = true` được đặt khi Admin tạo tài khoản, trang buộc đổi mật khẩu làm ở Sprint 1 (S) |
| 6 | Chiều phụ thuộc "Identity ← Academic ← …" mơ hồ; Enrollment cần dữ liệu Grading | Bảng phụ thuộc tường minh ở `module-map.md`, kiểm tra tự động bằng `ModuleBoundaryTest`; `PassedCoursesProvider` (ADR-007) |
| 7 | Luồng hủy đăng ký không nói khóa dòng; tỷ lệ vắng không nói `late`; unique `email`/`code` xung đột với soft delete | `drop` khóa dòng lớp và giảm `enrolled_count`; `late` **không** tính vắng (chỉ `absent`); unique + soft delete **[Cần xác nhận]**: hiện chặn tạo lại tài khoản trùng mã cho tới khi xóa cứng/đổi mã |
| 8 | ERD thiếu một số quan hệ FK | Bổ sung ở `docs/02-erd/erd.md` |
| 9 | Ba câu hỏi mở (mục 13) | Giữ giả định **tín chỉ + Blade (Phase 1)**; `/api/v1` được chuẩn bị sẵn (`sms.api_enabled`, `Routes/api.php`) **[Cần xác nhận]**. Bảng `classes` (lớp hành chính) của repo cũ **không** đưa sang vì không thuộc mô hình tín chỉ; nếu trường theo niên chế thì thêm lại (xem `legacy-migration.md`) |

## Chưa có trong tài liệu, được thêm để đủ cấu trúc
- `config/sms.php` gom toàn bộ hằng số nghiệp vụ rải rác trong tài liệu (6.5, 6.6, 6.7).
- Kiểm thử kiến trúc (`tests/Architecture`) để ranh giới module không bị phá âm thầm.
