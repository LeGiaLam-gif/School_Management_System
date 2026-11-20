<?php

namespace App\Modules\Academic\Requests\Admin;

use App\Modules\Academic\Models\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->can('academic.manage');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Department $department */
        $department = $this->route('department');
        $deptId = $department instanceof Department ? $department->id : $department;

        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('departments', 'code')->ignore($deptId)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => 'mã khoa',
            'name' => 'tên khoa',
            'description' => 'mô tả',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Vui lòng nhập mã khoa.',
            'code.unique' => 'Mã khoa này đã tồn tại trong hệ thống.',
            'name.required' => 'Vui lòng nhập tên đầy đủ của khoa.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge([
                'code' => strtoupper(trim((string) $this->input('code'))),
            ]);
        }
    }
}
