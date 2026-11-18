# BÁO CÁO TỔNG HỢP CƠ SỞ DỮ LIỆU & ELOQUENT MODEL
**Hệ thống Quản lý Đào tạo & Sinh viên (School Management System - HTA)**  
*Được chuẩn hóa theo kiến trúc Modular Monolith (Laravel 12, PHP 8.3, MySQL 8)*

---

## 1. TỔNG QUAN HỆ THỐNG
- **Tổng số bảng Database:** 28 bảng
- **Tổng số file Migration:** 24 migrations (Trạng thái: 100% `Ran`)
- **Tổng số Eloquent Models:** 19 Models
- **3 Actor chính:** `Admin`, `Lecturer` (Giảng viên), `Student` (Sinh viên)

---

## 2. CHI TIẾT THEO TỪNG MODULE

### 📦 2.1. Module Identity (Định danh & Phân quyền)
Quản lý người dùng, hồ sơ sinh viên, hồ sơ giảng viên và phân quyền RBAC (Spatie).

| Bảng Database | File Migration | Model Eloquent | Mô tả & Quan hệ chính |
| :--- | :--- | :--- | :--- |
| `users` | `2026_11_04_000100_create_users_table.php` | `User` | Tài khoản đăng nhập chung (`email`, `password`, `status`). 1-1 với Student/Lecturer. |
| `students` | `2026_11_04_000300_create_students_table.php`<br>`2026_11_11_000101_add_class_id_to_students_table.php` | `Student` | Hồ sơ sinh viên (`student_code`, `cohort_year`). Thuộc về `AdministrativeClass`, `Program`. |
| `lecturers` | `2026_11_04_000301_create_lecturers_table.php`<br>`2026_11_09_000100_add_contact_fields_to_admin_and_lecturer_profiles.php` | `Lecturer` | Hồ sơ giảng viên (`lecturer_code`, `academic_title`). Thuộc về `Department`. |
| `roles`<br>`permissions`<br>`model_has_roles`<br>`model_has_permissions`<br>`role_has_permissions` | `2026_11_04_000101_create_permission_tables.php` | *(Spatie)* | Bảng phân quyền Role-Based Access Control cho 3 vai trò: Admin, Lecturer, Student. |

---

### 📦 2.2. Module Academic (Chương trình Đào tạo & Học vụ)
Quản lý cơ cấu tổ chức khoa, ngành, môn học, học kỳ, lớp hành chính và cảnh báo học vụ.

| Bảng Database | File Migration | Model Eloquent | Mô tả & Quan hệ chính |
| :--- | :--- | :--- | :--- |
| `departments` | `2026_11_04_000201_create_departments_table.php` | `Department` | Khoa / Bộ môn đào tạo (CNTT, Kinh tế...). Có nhiều `Program`, `Course`. |
| `programs` | `2026_11_04_000202_create_programs_table.php` | `Program` | Ngành đào tạo (KTPM, HTTT, QTKD...). |
| `courses` | `2026_11_04_000203_create_courses_table.php` | `Course` | Môn học (`credits`, số tiết lý thuyết, thực hành). |
| `course_prerequisites` | `2026_11_04_000204_create_course_prerequisites_table.php` | *(Pivot)* | Môn điều kiện tiên quyết (Course self-referencing N-N). |
| `program_courses` | `2026_11_04_000205_create_program_courses_table.php` | *(Pivot)* | Khung chương trình đào tạo của từng ngành (bắt buộc/tự chọn, học kỳ gợi ý). |
| `semesters` | `2026_11_04_000206_create_semesters_table.php` | `Semester` | Học kỳ (`academic_year`, `registration_open_at`, `drop_deadline`). |
| `rooms` | `2026_11_04_000207_create_rooms_table.php` | `Room` | Phòng học, hội trường, phòng lab thực hành máy tính. |
| `administrative_classes` ⭐ | `2026_11_11_000100_create_administrative_classes_table.php` | `AdministrativeClass` | **Lớp hành chính / sinh hoạt** (ví dụ: 22DTH01), gắn Cố vấn học tập (`advisor_lecturer_id`). |
| `academic_warnings` ⭐ | `2026_11_13_000100_create_academic_warnings_table.php` | `AcademicWarning` | **Cảnh báo học vụ theo TT08/2021/TT-BGDĐT** (ĐTBHK, ĐTBCTL theo năm 1-4, nợ tín chỉ). |

---

### 📦 2.3. Module Teaching (Giảng dạy & Lớp học phần)
Quản lý việc tổ chức các lớp học phần trong từng kỳ học cụ thể.

| Bảng Database | File Migration | Model Eloquent | Mô tả & Quan hệ chính |
| :--- | :--- | :--- | :--- |
| `class_sections` | `2026_11_04_000401_create_class_sections_table.php` | `ClassSection` | Lớp học phần mở trong kỳ (`max_capacity`, trạng thái mở/khóa lớp). |
| `class_schedules` | `2026_11_04_000402_create_class_schedules_table.php` | `ClassSchedule` | Lịch học định kỳ hàng tuần (thứ, tiết bắt đầu, số tiết, phòng học). |
| `class_sessions` | `2026_11_04_000403_create_class_sessions_table.php` | `ClassSession` | Từng buổi học cụ thể theo ngày để giảng viên điểm danh. |

---

### 📦 2.4. Module Enrollment (Đăng ký học phần)
| Bảng Database | File Migration | Model Eloquent | Mô tả & Quan hệ chính |
| :--- | :--- | :--- | :--- |
| `enrollments` | `2026_11_04_000501_create_enrollments_table.php` | `Enrollment` | Đăng ký lớp học phần của sinh viên (`status`: Enrolled, Dropped, Cancelled). |

---

### 📦 2.5. Module Attendance (Điểm danh chuyên cần)
| Bảng Database | File Migration | Model Eloquent | Mô tả & Quan hệ chính |
| :--- | :--- | :--- | :--- |
| `attendance_records` | `2026_11_04_000601_create_attendance_records_table.php` | `AttendanceRecord` | Ghi nhận trạng thái có mặt/vắng/trễ của từng sinh viên theo từng `ClassSession`. |

---

### 📦 2.6. Module Grading (Đánh giá & Điểm số)
| Bảng Database | File Migration | Model Eloquent | Mô tả & Quan hệ chính |
| :--- | :--- | :--- | :--- |
| `grade_components` | `2026_11_04_000701_create_grade_components_table.php` | `GradeComponent` | Cột điểm thành phần (chuyên cần, giữa kỳ, thực hành, cuối kỳ) kèm trọng số %. |
| `grades` | `2026_11_04_000702_create_grades_table.php` | `Grade` | Điểm thô của từng sinh viên ứng với từng cột điểm thành phần. |
| `final_results` | `2026_11_04_000703_create_final_results_table.php` | `FinalResult` | Tổng kết môn: Điểm hệ 10, Điểm hệ 4, Điểm chữ (A, B, C, F), trạng thái Đạt/Rớt. |

---

### 📦 2.7. Module Announcement & Hệ thống Core
| Bảng Database | File Migration | Model Eloquent | Mô tả & Quan hệ chính |
| :--- | :--- | :--- | :--- |
| `announcements` | `2026_11_04_000801_create_announcements_table.php` | `Announcement` | Thông báo gửi tới toàn trường, theo khoa hoặc theo lớp học phần. |
| `audit_logs` | `2026_11_04_000110_create_audit_logs_table.php` | *(Log)* | Lưu vết lịch sử thao tác người dùng (IP, User Agent, hành vi). |
| `notifications` | `2026_11_04_000120_create_notifications_table.php` | *(Laravel)* | Bảng quản lý thông báo nội bộ hệ thống. |
| `cache`<br>`jobs` | `0001_01_01_000001_create_cache_table.php`<br>`0001_01_01_000002_create_jobs_table.php` | *(Core)* | Bộ nhớ tạm (Cache) và Hàng đợi xử lý tiến trình nền (Queued Jobs). |

---

## 3. CÁC TÍNH NĂNG MỞ RỘNG NỔI BẬT ĐÃ ĐÓNG GÓP
1. **Lớp hành chính (`administrative_classes`) - PR #27, #30, #31:**
   - Hoàn thiện mô hình lớp cố định của sinh viên trong suốt 4 năm đại học.
   - Bổ sung quan hệ `class_id` trên `Student` và liên kết Cố vấn học tập với `Lecturer`.
2. **Cảnh báo học vụ (`academic_warnings`) - PR #37:**
   - Hiện thực hóa đầy đủ quy chế đào tạo tín chỉ theo **Thông tư 08/2021/TT-BGDĐT**.
   - Hỗ trợ Enum `ViolationType` (ĐTBHK, ĐTBCTL theo năm 1-4, rớt >50% tín chỉ, nợ tích lũy >24 tín chỉ).

