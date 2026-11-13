<?php

namespace App\Shared\Enums;

/**
 * Loại vi phạm dẫn đến cảnh báo học vụ theo Thông tư 08/2021/TT-BGDĐT.
 */
enum ViolationType: string
{
    /** Điểm trung bình học kỳ (ĐTBHK) dưới ngưỡng quy định */
    case LowSemesterGpa = 'low_semester_gpa';

    /** Điểm trung bình chung tích lũy (ĐTBCTL) dưới ngưỡng theo năm học */
    case LowCumulativeGpa = 'low_cumulative_gpa';

    /** Rớt hơn 50% số tín chỉ đăng ký trong học kỳ */
    case FailedHalfCredits = 'failed_half_credits';

    /** Tổng số tín chỉ nợ tích lũy từ đầu khóa vượt quá 24 tín chỉ */
    case ExceededDebtCredits = 'exceeded_debt_credits';

    /** Nhãn tiếng Việt hiển thị trên giao diện */
    public function label(): string
    {
        return match ($this) {
            self::LowSemesterGpa => 'ĐTBHK dưới ngưỡng',
            self::LowCumulativeGpa => 'ĐTBCTL dưới ngưỡng tích lũy',
            self::FailedHalfCredits => 'Rớt trên 50% tín chỉ đăng ký',
            self::ExceededDebtCredits => 'Nợ tích lũy vượt 24 tín chỉ',
        };
    }

    /**
     * Điều khoản TT08/2021 tương ứng (dùng cho trường reason).
     */
    public function regulation(): string
    {
        return match ($this) {
            self::LowSemesterGpa => 'Khoản 1 Điều 16 TT08/2021',
            self::LowCumulativeGpa => 'Khoản 2 Điều 16 TT08/2021',
            self::FailedHalfCredits => 'Khoản 3 Điều 16 TT08/2021',
            self::ExceededDebtCredits => 'Khoản 3 Điều 16 TT08/2021',
        };
    }
}
