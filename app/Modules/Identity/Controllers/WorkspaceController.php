<?php

namespace App\Modules\Identity\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WorkspaceController extends Controller
{
    /**
     * @var array<string, array{title: string, description: string}>
     */
    private const ADMIN_PAGES = [
        'users' => [
            'title' => 'Quản lý người dùng',
            'description' => 'Tài khoản, vai trò và trạng thái truy cập trong hệ thống.',
        ],
        'departments' => [
            'title' => 'Quản lý khoa',
            'description' => 'Các khoa và đơn vị đào tạo của nhà trường.',
        ],
        'programs' => [
            'title' => 'Chương trình đào tạo',
            'description' => 'Ngành học và chương trình đào tạo được khai báo trong hệ thống.',
        ],
        'courses' => [
            'title' => 'Quản lý học phần',
            'description' => 'Danh mục học phần và thông tin tín chỉ.',
        ],
        'semesters' => [
            'title' => 'Quản lý học kỳ',
            'description' => 'Các học kỳ và thời gian đào tạo.',
        ],
        'class-sections' => [
            'title' => 'Lớp học phần',
            'description' => 'Các lớp học phần được mở trong từng học kỳ.',
        ],
        'reports' => [
            'title' => 'Thống kê & báo cáo',
            'description' => 'Tổng quan dữ liệu đào tạo dành cho quản trị viên.',
        ],
    ];

    /**
     * @var array<string, array{title: string, description: string}>
     */
    private const LECTURER_PAGES = [
        'classes' => [
            'title' => 'Lớp được phân công',
            'description' => 'Danh sách lớp học phần giảng viên phụ trách.',
        ],
        'students' => [
            'title' => 'Danh sách sinh viên',
            'description' => 'Tra cứu sinh viên thuộc các lớp được phân công.',
        ],
        'timetable' => [
            'title' => 'Lịch dạy',
            'description' => 'Thời khóa biểu giảng dạy theo tuần và học kỳ.',
        ],
        'attendance' => [
            'title' => 'Điểm danh',
            'description' => 'Theo dõi chuyên cần của sinh viên trong từng buổi học.',
        ],
        'grades' => [
            'title' => 'Quản lý điểm',
            'description' => 'Các thành phần điểm của lớp học phần được phân công.',
        ],
        'announcements' => [
            'title' => 'Thông báo',
            'description' => 'Thông tin học vụ và thông báo dành cho lớp.',
        ],
        'reports' => [
            'title' => 'Báo cáo & xuất dữ liệu',
            'description' => 'Tổng quan lớp học và kết quả giảng dạy.',
        ],
    ];

    public function admin(Request $request): View
    {
        return $this->render($request, self::ADMIN_PAGES, 'Quản trị viên', 'admin.home');
    }

    public function lecturer(Request $request): View
    {
        return $this->render($request, self::LECTURER_PAGES, 'Giảng viên', 'lecturer.home');
    }

    /**
     * @param  array<string, array{title: string, description: string}>  $pages
     */
    private function render(Request $request, array $pages, string $roleLabel, string $dashboardRoute): View
    {
        $page = $request->route('page');

        abort_unless(is_string($page) && isset($pages[$page]), 404);

        return view('identity::workspace.index', [
            'page' => $pages[$page],
            'roleLabel' => $roleLabel,
            'dashboardRoute' => $dashboardRoute,
        ]);
    }
}
