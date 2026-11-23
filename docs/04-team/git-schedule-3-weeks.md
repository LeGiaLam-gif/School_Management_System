# LỊCH TRÌNH RẢI COMMIT GIT 3 TUẦN (21 NGÀY)
**Dự án:** School Management System (SMS) - Hexa Tech Academy  
**Vai trò phụ trách:** **Vai trò 2 – Database & Eloquent Models**  
**Phạm vi Actor:** Chỉ gồm 3 Actor duy nhất (**Admin**, **Lecturer**, **Student**) - tuyệt đối không có `Academic Staff` (mọi chức năng quản lý đào tạo thuộc về Admin).  
**Quy chuẩn commit:** Mỗi ngày 1–2 commit nhỏ, rõ ràng theo chuẩn Conventional Commits (`feat`, `fix`, `test`, `docs`, `refactor`), tuyệt đối không commit dồn một cục.

---

## 📅 TUẦN 1: NỀN TẢNG DATABASE, CẤU TRÚC ĐÀO TẠO & HỒ SƠ (NGÀY 1 – NGÀY 7)
*Mục tiêu tuần 1: Hoàn thiện bảng lõi Identity (Users, Students, Lecturers), Khoa, Ngành và bổ sung Lớp hành chính.*

| Ngày | Nhánh (Branch) | Thông điệp Commit (Git Message) | Nội dung công việc thực hiện |
| :---: | :--- | :--- | :--- |
| **Ngày 1** | `feature/database-erd-init` | `docs: add initial draft for database architecture and entities` | Viết tài liệu phân tích thực thể dữ liệu, tạo thư mục docs cho Database. |
| **Ngày 2** | `feature/database-erd-init` | `docs: complete ERD diagram specification for 3 actors` | Hoàn thiện file thiết kế ERD và sơ đồ quan hệ giữa các bảng. |
| **Ngày 3** | `feature/admin-class-schema` | `feat(database): create migration for administrative_classes table` | Tạo file migration bảng `administrative_classes` (mã lớp, tên lớp, ngành, niên khóa). |
| **Ngày 4** | `feature/admin-class-schema` | `feat(database): add class_id foreign key to students table` | Tạo migration bổ sung cột `class_id` vào bảng `students` liên kết với lớp hành chính. |
| **Ngày 5** | `feature/admin-class-models` | `feat(models): create AdministrativeClass model with relationships` | Tạo file Model `AdministrativeClass`, khai báo `belongsTo(Program)` và `hasMany(Student)`. |
| **Ngày 6** | `feature/admin-class-models` | `refactor(models): update Student and Program models with class relation` | Cập nhật Model `Student` (thêm `administrativeClass()`) và Model `Program` (thêm `administrativeClasses()`). |
| **Ngày 7** | `feature/admin-class-seed` | `test(database): add factory and seeder for administrative classes` | Viết `AdministrativeClassSeeder` tạo dữ liệu các lớp mẫu (`22DTH01`, `22DTH02`) và gán sinh viên mẫu. |

👉 **Cuối Tuần 1:** Tạo Pull Request gộp `feature/admin-class-seed` vào `development` (hoặc `main`).  
**Kết quả tuần 1:** Đã có đầy đủ Lớp hành chính để bàn giao cho Vai trò 4 (Admin) làm giao diện CRUD.

---

## 📅 TUẦN 2: HỌC PHẦN, MỞ LỚP, LỊCH HỌC & ĐĂNG KÝ MÔN (NGÀY 8 – NGÀY 14)
*Mục tiêu tuần 2: Rà soát và hoàn thiện Model, Scope, Relationship cho Mở lớp học phần, Lịch học và Đăng ký tín chỉ.*

| Ngày | Nhánh (Branch) | Thông điệp Commit (Git Message) | Nội dung công việc thực hiện |
| :---: | :--- | :--- | :--- |
| **Ngày 8** | `feature/course-section-models` | `feat(models): add scopes and capacity helpers to ClassSection` | Thêm các hàm phụ trợ cho Model `ClassSection`: `scopeOpen()`, `getRemainingSeatsAttribute()`. |
| **Ngày 9** | `feature/course-section-models` | `feat(models): enhance ClassSchedule and ClassSession relationships` | Khai báo quan hệ liên kết phòng học, thứ, tiết, giảng viên cho lịch học và buổi học. |
| **Ngày 10** | `feature/enrollment-rules-model`| `feat(database): add indexes to optimize course registration queries` | Tạo migration bổ sung index tìm kiếm nhanh cho `enrollments(student_id, class_section_id)`. |
| **Ngày 11** | `feature/enrollment-rules-model`| `feat(models): add active enrollments relationship to Student model` | Bổ sung quan hệ `enrolledSections()` trong Model `Student` để kiểm tra trùng môn học. |
| **Ngày 12** | `feature/prerequisite-model` | `refactor(models): optimize Course prerequisite query relationships` | Chuẩn hóa quan hệ nhiều - nhiều tự tham chiếu cho môn học tiên quyết (`prerequisites`, `followUpCourses`). |
| **Ngày 13** | `feature/teaching-data-seed` | `test(seeders): expand class section schedules and sessions demo data` | Bổ sung thêm dữ liệu lớp học phần và phòng học mẫu để Vai trò 5 & 6 test giao diện. |
| **Ngày 14** | `feature/teaching-data-seed` | `test(models): add unit tests for ClassSection and Enrollment relations` | Viết bài test Unit kiểm tra tính toàn vẹn các quan hệ Eloquent của module Teaching & Enrollment. |

👉 **Cuối Tuần 2:** Tạo Pull Request gộp tuần 2.  
**Kết quả tuần 2:** Database và Model cho việc mở lớp, đăng ký học phần sẵn sàng cho Vai trò 5 (Giảng viên) và Vai trò 6 (Sinh viên).

---

## 📅 TUẦN 3: ĐIỂM SỐ, GPA, TỐI ƯU HÓA & HOÀN THIỆN NỘP BÀI (NGÀY 15 – NGÀY 21)
*Mục tiêu tuần 3: Hoàn thiện Model tính điểm/GPA, Audit Log, tối ưu hóa database, chốt ERD và hoàn tất báo cáo.*

| Ngày | Nhánh (Branch) | Thông điệp Commit (Git Message) | Nội dung công việc thực hiện |
| :---: | :--- | :--- | :--- |
| **Ngày 15** | `feature/grading-models` | `feat(models): define grade weight validation and helper in GradeComponent` | Bổ sung hàm kiểm tra tổng trọng số 100% trong Model `GradeComponent`. |
| **Ngày 16** | `feature/grading-models` | `feat(models): add GPA calculation accessors to FinalResult model` | Khai báo các thuộc tính tính điểm trung bình thang 4, điểm chữ quy đổi trong `FinalResult`. |
| **Ngày 17** | `feature/database-optimization` | `perf(database): add foreign key cascading rules and soft delete checks` | Rà soát các ràng buộc toàn vẹn khóa ngoại (onDelete cascade/restrict), đảm bảo không xóa mất lịch sử học tập. |
| **Ngày 18** | `feature/database-optimization` | `feat(database): configure clean master seed runner DatabaseSeeder` | Tối ưu hóa file `DatabaseSeeder.php`, đảm bảo chỉ cần gõ `php artisan db:seed` là nạp đủ dữ liệu mẫu sạch sẽ. |
| **Ngày 19** | `feature/database-audit-docs` | `docs: export high-resolution ERD diagram and table data dictionary` | Xuất file ảnh ERD độ phân giải cao và lập bảng từ điển dữ liệu (Data Dictionary) cho từng cột. |
| **Ngày 20** | `feature/database-audit-docs` | `test(database): run comprehensive model relationship test suite` | Chạy toàn bộ test model, đảm bảo 100% test case pass không có lỗi cú pháp hay thiếu sót quan hệ. |
| **Ngày 21** | `main` | `chore: finalize database migrations, models documentation for submission` | Tổng duyệt toàn bộ code, cập nhật README phần Database, tag phiên bản `v1.0.0` để nộp cho thầy. |

---

### 💡 Lời khuyên vàng để lịch sử Git đẹp và chuyên nghiệp:
1. **Không commit cùng một ngày:** Mỗi ngày bạn chỉ cần làm đúng task của ngày đó (khoảng 30-45 phút), gõ đúng thông điệp commit rồi `git push origin <tên-nhánh>`.
2. **Tạo nhánh tính năng (Feature Branches):** Không commit thẳng vào `main`. Hãy làm trên các nhánh như `feature/admin-class-schema`, `feature/grading-models`... rồi sau đó tạo Pull Request merge vào `main` (thầy nhìn vào sẽ thấy quy trình làm việc chuẩn doanh nghiệp).
3. **Mỗi commit chỉ làm một việc:** Đúng như lịch trình trên, file nào sửa thì chỉ commit file đó, tách biệt giữa migration, model, test và docs.

