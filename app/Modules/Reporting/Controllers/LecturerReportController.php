<?php

namespace App\Modules\Reporting\Controllers;

use App\Modules\Academic\Models\Course;
use App\Modules\Identity\Models\User;
use App\Modules\Reporting\Services\LecturerReportService;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LecturerReportController
{
    public function __construct(
        private readonly LecturerReportService $reportService,
    ) {}

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $semesters = $this->reportService->getSemesters();
        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;
        $semesterId = $this->reportService->resolveSemesterId($requestedSemesterId);

        $classes = $this->reportService->getLecturerClasses($user, $semesterId);
        $allowedClassIds = $classes->pluck('id')->all();

        $requestedClassId = $request->filled('class_section_id') ? $request->integer('class_section_id') : null;
        $selectedClassId = ($requestedClassId !== null && in_array($requestedClassId, $allowedClassIds, true))
            ? $requestedClassId
            : null;

        $report = $this->reportService->buildReport($classes, $selectedClassId);

        return view('reporting::lecturer.index', [
            'semesters' => $semesters,
            'classes' => $classes,
            'report' => $report,
            'filters' => [
                'semester_id' => $semesterId,
                'class_section_id' => $selectedClassId,
            ],
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        /** @var User $user */
        $user = $request->user();

        $requestedSemesterId = $request->filled('semester_id') ? $request->integer('semester_id') : null;
        $semesterId = $this->reportService->resolveSemesterId($requestedSemesterId);

        $classes = $this->reportService->getLecturerClasses($user, $semesterId);
        $allowedClassIds = $classes->pluck('id')->all();

        $requestedClassId = $request->filled('class_section_id') ? $request->integer('class_section_id') : null;
        $selectedClassId = ($requestedClassId !== null && in_array($requestedClassId, $allowedClassIds, true))
            ? $requestedClassId
            : null;

        $report = $this->reportService->buildReport($classes, $selectedClassId);
        $filename = 'bao-cao-giang-day-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($report): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // BOM UTF-8 de Excel hien thi chuan tieng Viet
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Mã lớp',
                'Học phần',
                'Trạng thái',
                'Sĩ số',
                'Tiến độ buổi học',
                'Tỷ lệ chuyên cần (%)',
                'Số SV có điểm',
                'Tỷ lệ đạt (%)',
                'Điểm TB (hệ 10)',
                'SV cảnh báo vắng',
            ]);

            foreach ($report['classSummaries'] as $item) {
                $section = $item['section'];
                $course = $section->course;
                $courseName = $course->name;
                $status = $section->status;
                $statusLabel = $status instanceof ClassSectionStatus ? $status->label() : (string) $status;

                fputcsv($handle, [
                    $section->code,
                    $courseName,
                    $statusLabel,
                    $item['studentCount'],
                    "{$item['heldSessions']}/{$item['totalSessions']}",
                    $item['attendanceRate'] !== null ? (string) $item['attendanceRate'] : 'N/A',
                    $item['gradedCount'],
                    $item['passRate'] !== null ? (string) $item['passRate'] : 'N/A',
                    $item['averageScore'] !== null ? (string) $item['averageScore'] : 'N/A',
                    $item['warningCount'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
