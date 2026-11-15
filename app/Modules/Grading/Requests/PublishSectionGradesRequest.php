<?php

namespace App\Modules\Grading\Requests;

use App\Modules\Grading\Policies\GradePolicy;
use App\Modules\Identity\Models\User;
use App\Modules\Teaching\Models\ClassSection;
use App\Shared\Enums\ClassSectionStatus;
use Illuminate\Foundation\Http\FormRequest;

class PublishSectionGradesRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var User|null $user */
        $user = $this->user();
        $section = $this->route('classSection');

        if ($user === null || ! $section instanceof ClassSection) {
            return false;
        }

        return app(GradePolicy::class)->publish($user, $section)
            && $section->status !== ClassSectionStatus::GradesPublished;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
