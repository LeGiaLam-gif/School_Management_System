# Quy ước lập trình

- **Controller mỏng:** nhận request → gọi Service → trả response. Không truy vấn phức tạp, không quy tắc nghiệp vụ.
- **Service:** quy tắc nghiệp vụ + `DB::transaction`; phát Event **sau** khi commit; ném `BusinessRuleException(message, ruleCode)`
  khi vi phạm BR-xx (handler trong `bootstrap/app.php` đổi thành thông báo hoặc HTTP 422).
- **Policy:** kiểm quyền theo đối tượng; không chứa quy tắc nghiệp vụ. Mọi route đọc/ghi dữ liệu của người khác phải
  qua Policy (NFR-SEC-02).
- **Lớp thuần** (`Support/`): tính toán không phụ thuộc DB/framework (calculator, value object). Dễ test, ưu tiên khi
  logic có nhiều biên.
- **Repository:** không bọc cho mọi model. Chỉ dùng lớp `Queries/` cho truy vấn phức tạp (thời khóa biểu, báo cáo, GPA).
- **Enum PHP** cho mọi cột trạng thái (`App\Shared\Enums`), cột DB là `string`.
- **Cấu hình** nghiệp vụ trong `config/sms.php`; không `env()` ngoài thư mục `config` (trừ seeder).
- **Audit:** thay đổi nhạy cảm (điểm, role, mở khóa, sửa điểm danh đã chốt) → trait `Auditable` hoặc
  `AuditLogger::record()` có `reason`. Chú ý `upsert()` không bắn model event.
- **Test:** Unit cho rule/calculator; Feature cho HTTP + DB; mỗi route mới thêm dòng vào
  `RoleAuthorizationTest` (200/403); thay đổi ranh giới module phải giữ `tests/Architecture` xanh.
- **Đặt tên:** bảng số nhiều snake_case, FK `xxx_id`, permission `resource.action`, mã lỗi nghiệp vụ `BR-xx` hoặc
  `MODULE-TOPIC`.
- **Ngôn ngữ:** giao diện và thông báo lỗi tiếng Việt (`lang/vi`); code, tên biến, commit bằng tiếng Anh.
- **Style:** Laravel Pint (`composer lint:fix`), Larastan level 5 (`composer analyse`).
