<?php

/*
|--------------------------------------------------------------------------
| Cau hinh nghiep vu SMS
|--------------------------------------------------------------------------
|
| Moi nguong/quy tac nghiep vu (thang diem, gioi han tin chi, nguong vang...)
| nam o day thay vi hardcode trong Service. Tham chieu: docs/01-requirements.
|
*/

return [

    /*
    | Bat route /api/v1 cua cac module (Phase 2, Sanctum).
    */
    'api_enabled' => (bool) env('SMS_API_ENABLED', false),

    /*
    | Xac thuc (FR-AUTH-05): khoa tam sau N lan dang nhap sai.
    */
    'auth' => [
        'max_attempts' => (int) env('SMS_LOGIN_MAX_ATTEMPTS', 5),
        'decay_seconds' => (int) env('SMS_LOGIN_DECAY_SECONDS', 60),
    ],

    /*
    | Thang diem (muc 6.7). Thang duoc duyet tu tren xuong, dong dau tien co
    | `min` <= diem tong ket se duoc chon. Cau hinh duoc theo quy che truong.
    */
    'grading' => [
        'scale' => [
            ['min' => 8.5, 'letter' => 'A', 'point' => 4.0],
            ['min' => 8.0, 'letter' => 'B+', 'point' => 3.5],
            ['min' => 7.0, 'letter' => 'B', 'point' => 3.0],
            ['min' => 6.5, 'letter' => 'C+', 'point' => 2.5],
            ['min' => 5.5, 'letter' => 'C', 'point' => 2.0],
            ['min' => 5.0, 'letter' => 'D+', 'point' => 1.5],
            ['min' => 4.0, 'letter' => 'D', 'point' => 1.0],
            ['min' => 0.0, 'letter' => 'F', 'point' => 0.0],
        ],
        // Diem tong ket toi thieu de dat mon.
        'pass_min' => (float) env('SMS_GRADE_PASS_MIN', 4.0),
        // So chu so thap phan cua diem tong ket (lam tron truoc khi quy doi).
        'decimals' => 2,
        // Chinh sach hoc lai khi tinh GPA: highest (lan cao nhat) | latest (lan gan nhat).
        'retake_policy' => env('SMS_GRADE_RETAKE_POLICY', 'highest'),
    ],

    /*
    | Dang ky hoc phan (muc 6.5). Hoc ky co the ghi de bang min/max_credits.
    */
    'enrollment' => [
        'default_min_credits' => (int) env('SMS_MIN_CREDITS', 14),
        'default_max_credits' => (int) env('SMS_MAX_CREDITS', 24),
    ],

    /*
    | Diem danh (muc 6.6, BR-09).
    */
    'attendance' => [
        // Cua so cho phep diem danh quanh ngay buoi hoc (+/- ngay).
        'window_days' => (int) env('SMS_ATTENDANCE_WINDOW_DAYS', 7),
        // Ti le vang toi da (0.20 = 20%) truoc khi canh bao.
        'absence_threshold' => (float) env('SMS_ABSENCE_THRESHOLD', 0.20),
    ],

];
