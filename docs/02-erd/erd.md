# ERD - Mô hình dữ liệu

Khớp 1-1 với migration trong `app/Modules/*/Database/Migrations` (xem từ điển dữ liệu ở mục 8 của
[requirements.md](../01-requirements/requirements.md)). Bảng chuẩn của Spatie Permission, `jobs`, `cache`,
`sessions`, `notifications` không vẽ.

## Quan hệ

```mermaid
erDiagram
    USERS ||--o| STUDENTS : "hồ sơ SV"
    USERS ||--o| LECTURERS : "hồ sơ GV"
    USERS ||--o{ ANNOUNCEMENTS : "đăng"
    USERS ||--o{ AUDIT_LOGS : "thực hiện"
    USERS ||--o{ ENROLLMENTS : "registered_by (Admin đăng ký hộ)"
    USERS ||--o{ ATTENDANCE_RECORDS : "marked_by"
    USERS ||--o{ GRADES : "graded_by"
    USERS ||--o{ FINAL_RESULTS : "unlocked_by"
    DEPARTMENTS ||--o{ PROGRAMS : "có ngành"
    DEPARTMENTS ||--o{ COURSES : "quản lý"
    DEPARTMENTS ||--o{ LECTURERS : "thuộc khoa"
    PROGRAMS ||--o{ STUDENTS : "theo học"
    PROGRAMS ||--o{ ADMINISTRATIVE_CLASSES : "thuộc ngành"
    ADMINISTRATIVE_CLASSES ||--o{ STUDENTS : "gồm sinh viên"
    LECTURERS ||--o{ ADMINISTRATIVE_CLASSES : "cố vấn học tập (CVHT)"
    PROGRAMS ||--o{ PROGRAM_COURSES : "khung CT"
    COURSES ||--o{ PROGRAM_COURSES : "thuộc khung"
    COURSES ||--o{ COURSE_PREREQUISITES : "cần tiên quyết"
    COURSES ||--o{ COURSE_PREREQUISITES : "là tiên quyết của"
    COURSES ||--o{ CLASS_SECTIONS : "được mở lớp"
    SEMESTERS ||--o{ CLASS_SECTIONS : "trong kỳ"
    LECTURERS ||--o{ CLASS_SECTIONS : "phụ trách"
    CLASS_SECTIONS ||--o{ CLASS_SCHEDULES : "có lịch"
    ROOMS ||--o{ CLASS_SCHEDULES : "dùng phòng"
    ROOMS ||--o{ CLASS_SESSIONS : "dùng phòng từng buổi"
    CLASS_SCHEDULES ||--o{ CLASS_SESSIONS : "sinh buổi"
    CLASS_SECTIONS ||--o{ CLASS_SESSIONS : "gồm buổi"
    CLASS_SECTIONS ||--o{ ENROLLMENTS : "nhận đăng ký"
    STUDENTS ||--o{ ENROLLMENTS : "đăng ký"
    CLASS_SESSIONS ||--o{ ATTENDANCE_RECORDS : "điểm danh"
    ENROLLMENTS ||--o{ ATTENDANCE_RECORDS : "có mặt"
    CLASS_SECTIONS ||--o{ GRADE_COMPONENTS : "cấu hình điểm"
    GRADE_COMPONENTS ||--o{ GRADES : "có điểm"
    ENROLLMENTS ||--o{ GRADES : "được chấm"
    ENROLLMENTS ||--o| FINAL_RESULTS : "tổng kết"
    CLASS_SECTIONS ||--o{ ANNOUNCEMENTS : "thông báo lớp"
```

So với sơ đồ gốc của tài liệu, bổ sung các quan hệ vốn đã có FK trong từ điển dữ liệu nhưng chưa vẽ
(`registered_by`, `marked_by`, `graded_by`, `unlocked_by`, `class_sessions.room_id`, chiều "là tiên quyết của").

## Các bảng trọng tâm

```mermaid
erDiagram
    USERS {
        bigint id PK
        string code UK "mã đăng nhập"
        string email UK
        string status "active | locked"
        bool must_change_password
        timestamp last_login_at
        timestamp deleted_at
    }
    ADMINISTRATIVE_CLASSES {
        bigint id PK
        string code UK "mã lớp hành chính"
        string name "tên lớp"
        bigint program_id FK "thuộc ngành"
        smallint academic_year "niên khóa"
        bigint advisor_lecturer_id FK "nullable, cố vấn"
        timestamp created_at
        timestamp updated_at
    }
    CLASS_SECTIONS {
        bigint id PK
        bigint course_id FK
        bigint semester_id FK
        bigint lecturer_id FK "nullable"
        string code "UK cùng course_id, semester_id"
        smallint capacity
        smallint enrolled_count "phi chuẩn, khóa dòng"
        string status "draft..grades_published"
        timestamp deleted_at
    }
    ENROLLMENTS {
        bigint id PK
        bigint student_id FK
        bigint class_section_id FK "UK cùng student_id"
        string status "registered | dropped | completed"
        timestamp registered_at
        timestamp dropped_at
        bigint registered_by FK "nullable"
        text override_reason
    }
    FINAL_RESULTS {
        bigint id PK
        bigint enrollment_id FK "UK"
        decimal total_score "thang 10"
        string letter_grade
        decimal gpa_point "thang 4"
        bool is_passed
        timestamp published_at
        timestamp locked_at
        text unlock_reason
    }
    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK "nullable"
        string action
        string auditable_type
        bigint auditable_id
        json old_values
        json new_values
        text reason
        string ip_address
        timestamp created_at "chỉ có created_at, append-only"
    }
    CLASS_SECTIONS ||--o{ ENROLLMENTS : "nhận đăng ký"
    ENROLLMENTS ||--o| FINAL_RESULTS : "tổng kết"
    USERS ||--o{ AUDIT_LOGS : "thực hiện"
```

## Ràng buộc toàn vẹn đã cài

| Ràng buộc | Nơi thực thi | Test |
|---|---|---|
| unique (student_id, class_section_id) ở `enrollments` | migration | `SchemaIntegrityTest` |
| unique (class_session_id, enrollment_id) ở `attendance_records` | migration | `SchemaIntegrityTest` |
| unique (enrollment_id, grade_component_id) ở `grades` | migration | `SchemaIntegrityTest` |
| unique (course_id, semester_id, code) ở `class_sections` | migration | `SchemaIntegrityTest` |
| course_id <> prerequisite_course_id | CHECK (MySQL) + `PrerequisiteCycleDetector` | `PrerequisiteCycleDetectorTest` |
| enrolled_count không vượt capacity | `lockForUpdate` + `CapacityRule` | `EnrollmentServiceTest` (+ concurrency MySQL, Sprint 3) |
| Dữ liệu học vụ không xóa cứng | `restrictOnDelete` trên FK + soft delete (users, courses, class_sections) | `SchemaIntegrityTest` |
| audit_logs append-only | model chặn update/delete | `AuditTest` |

Quy ước FK: dữ liệu học vụ (`enrollments`, `grades`, `attendance_records`, `final_results`) dùng
`restrictOnDelete`, chỉ dữ liệu con thuần túy (lịch, buổi học, cấu hình điểm của lớp) dùng `cascadeOnDelete`.
