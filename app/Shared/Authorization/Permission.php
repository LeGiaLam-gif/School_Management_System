<?php

namespace App\Shared\Authorization;

/**
 * Danh muc permission dang `resource.action` (muc 5.8) va ma tran vai tro
 * (muc 2.5). Nguon su that duy nhat cho RolePermissionSeeder va route.
 */
final class Permission
{
    // Tai khoan & phan quyen
    public const USERS_VIEW = 'users.view';

    public const USERS_CREATE = 'users.create';

    public const USERS_UPDATE = 'users.update';

    public const USERS_DELETE = 'users.delete';

    public const USERS_RESET_PASSWORD = 'users.reset_password';

    public const USERS_IMPORT = 'users.import';

    /** Chi Super Admin: tao/sua Admin khac, gan role. */
    public const ROLES_MANAGE = 'roles.manage';

    // Cau truc dao tao
    public const ACADEMIC_VIEW = 'academic.view';

    public const ACADEMIC_MANAGE = 'academic.manage';

    // Lop hoc phan & thoi khoa bieu
    public const CLASS_SECTIONS_VIEW = 'class_sections.view';

    public const CLASS_SECTIONS_MANAGE = 'class_sections.manage';

    public const TIMETABLE_VIEW_OWN = 'timetable.view_own';

    public const TIMETABLE_VIEW_ALL = 'timetable.view_all';

    // Dang ky hoc phan
    public const ENROLLMENTS_REGISTER = 'enrollments.register';

    public const ENROLLMENTS_VIEW_OWN = 'enrollments.view_own';

    public const ENROLLMENTS_VIEW_CLASS = 'enrollments.view_class';

    public const ENROLLMENTS_OVERRIDE = 'enrollments.override';

    // Diem danh
    public const ATTENDANCE_MARK = 'attendance.mark';

    public const ATTENDANCE_VIEW_OWN = 'attendance.view_own';

    public const ATTENDANCE_VIEW_ALL = 'attendance.view_all';

    public const ATTENDANCE_EDIT_FINALIZED = 'attendance.edit_finalized';

    // Diem
    public const GRADES_MANAGE = 'grades.manage';

    public const GRADES_PUBLISH = 'grades.publish';

    public const GRADES_UNLOCK = 'grades.unlock';

    public const GRADES_VIEW_OWN = 'grades.view_own';

    // Thong bao
    public const ANNOUNCEMENTS_VIEW = 'announcements.view';

    public const ANNOUNCEMENTS_MANAGE = 'announcements.manage';

    // Bao cao & audit
    public const REPORTS_VIEW_ALL = 'reports.view_all';

    public const REPORTS_VIEW_CLASS = 'reports.view_class';

    public const REPORTS_VIEW_OWN = 'reports.view_own';

    public const AUDIT_VIEW = 'audit.view';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_values((new \ReflectionClass(self::class))->getConstants());
    }

    /**
     * Ma tran phan quyen theo vai tro (muc 2.5). `roles.manage` KHONG nam trong
     * vai tro admin: chi gan truc tiep cho Super Admin.
     *
     * @return array<string, list<string>>
     */
    public static function byRole(): array
    {
        return [
            'admin' => [
                self::USERS_VIEW, self::USERS_CREATE, self::USERS_UPDATE, self::USERS_DELETE,
                self::USERS_RESET_PASSWORD, self::USERS_IMPORT,
                self::ACADEMIC_VIEW, self::ACADEMIC_MANAGE,
                self::CLASS_SECTIONS_VIEW, self::CLASS_SECTIONS_MANAGE,
                self::TIMETABLE_VIEW_ALL,
                self::ENROLLMENTS_OVERRIDE, self::ENROLLMENTS_VIEW_CLASS,
                self::ATTENDANCE_VIEW_ALL, self::ATTENDANCE_EDIT_FINALIZED,
                self::GRADES_UNLOCK,
                self::ANNOUNCEMENTS_VIEW, self::ANNOUNCEMENTS_MANAGE,
                self::REPORTS_VIEW_ALL, self::AUDIT_VIEW,
            ],
            'lecturer' => [
                self::ACADEMIC_VIEW,
                self::CLASS_SECTIONS_VIEW,
                self::TIMETABLE_VIEW_OWN,
                self::ENROLLMENTS_VIEW_CLASS,
                self::ATTENDANCE_MARK, self::ATTENDANCE_EDIT_FINALIZED,
                self::GRADES_MANAGE, self::GRADES_PUBLISH,
                self::ANNOUNCEMENTS_VIEW, self::ANNOUNCEMENTS_MANAGE,
                self::REPORTS_VIEW_CLASS,
            ],
            'student' => [
                self::ACADEMIC_VIEW,
                self::CLASS_SECTIONS_VIEW,
                self::TIMETABLE_VIEW_OWN,
                self::ENROLLMENTS_REGISTER, self::ENROLLMENTS_VIEW_OWN,
                self::ATTENDANCE_VIEW_OWN,
                self::GRADES_VIEW_OWN,
                self::ANNOUNCEMENTS_VIEW,
                self::REPORTS_VIEW_OWN,
            ],
        ];
    }
}
