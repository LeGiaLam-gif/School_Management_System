<?php

namespace App\Modules\Academic\Services;

use App\Modules\Academic\Models\Semester;
use App\Shared\Exceptions\BusinessRuleException;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class SemesterService
{
    /**
     * Tai moi thoi diem chi co mot hoc ky o trang thai current (muc 6.3).
     */
    public function setCurrent(Semester $semester): Semester
    {
        DB::transaction(function () use ($semester) {
            Semester::query()->where('is_current', true)->update(['is_current' => false]);
            $semester->update(['is_current' => true]);
        });

        return $semester->refresh();
    }

    /**
     * Dat khoang thoi gian dang ky; registration_open_at < registration_close_at.
     */
    public function scheduleRegistration(Semester $semester, CarbonInterface $openAt, CarbonInterface $closeAt): Semester
    {
        if ($openAt->greaterThanOrEqualTo($closeAt)) {
            throw new BusinessRuleException(
                'Thời điểm mở đăng ký phải trước thời điểm đóng đăng ký.',
                'ACAD-REG-WINDOW',
            );
        }

        $semester->update([
            'registration_open_at' => $openAt,
            'registration_close_at' => $closeAt,
        ]);

        return $semester;
    }

    public function closeRegistration(Semester $semester): Semester
    {
        if ($semester->isRegistrationOpen()) {
            $semester->update(['registration_close_at' => now()]);
        }

        return $semester;
    }
}
