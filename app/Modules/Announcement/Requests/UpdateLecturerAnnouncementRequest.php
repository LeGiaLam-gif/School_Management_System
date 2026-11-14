<?php

namespace App\Modules\Announcement\Requests;

use App\Modules\Announcement\Models\Announcement;
use App\Modules\Announcement\Policies\AnnouncementPolicy;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLecturerAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        $announcement = $this->route('announcement');

        if ($user === null || ! $announcement instanceof Announcement) {
            return false;
        }

        if (! app(AnnouncementPolicy::class)->update($user, $announcement)) {
            return false;
        }

        $rawSectionId = $this->input('class_section_id');
        if (! is_numeric($rawSectionId)) {
            return true;
        }

        $section = ClassSection::query()->find((int) $rawSectionId);
        if ($section === null) {
            return true;
        }

        return $section->isTaughtBy($user);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'class_section_id' => ['required', 'integer', 'exists:class_sections,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'is_pinned' => ['nullable', 'boolean'],
            'publish_mode' => ['nullable', 'in:now,schedule'],
            'published_at' => ['nullable', 'required_if:publish_mode,schedule', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'class_section_id.required' => 'Vui lòng chọn lớp học phần nhận thông báo.',
            'title.required' => 'Tiêu đề thông báo không được để trống.',
            'body.required' => 'Nội dung thông báo không được để trống.',
            'published_at.required_if' => 'Vui lòng chọn thời điểm hẹn giờ đăng.',
        ];
    }
}
