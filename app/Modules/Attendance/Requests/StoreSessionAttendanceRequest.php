<?php

namespace App\Modules\Attendance\Requests;

use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSession;
use App\Shared\Authorization\Permission;
use App\Shared\Enums\AttendanceStatus;
use App\Shared\Enums\SessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSessionAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        $session = $this->route('classSession');

        if ($user === null || ! $session instanceof ClassSession) {
            return false;
        }

        return $user->can(Permission::ATTENDANCE_MARK)
            && $session->classSection->isTaughtBy($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'session_status' => ['nullable', Rule::enum(SessionStatus::class)],
            'records' => ['required', 'array', 'min:1'],
            'records.*.status' => ['required', Rule::enum(AttendanceStatus::class)],
            'records.*.note' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'records.required' => 'Danh sách điểm danh không được để trống.',
            'records.*.status.required' => 'Vui lòng chọn trạng thái điểm danh cho từng sinh viên.',
            'records.*.note.max' => 'Ghi chú không được vượt quá 255 ký tự.',
        ];
    }
}
