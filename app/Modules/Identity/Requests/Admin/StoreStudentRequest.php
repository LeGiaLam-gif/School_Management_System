<?php

namespace App\Modules\Identity\Requests\Admin;

use App\Shared\Enums\Gender;
use App\Shared\Enums\StudentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('student_code')) {
            $this->merge([
                'student_code' => strtoupper(trim((string) $this->input('student_code'))),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'student_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('students', 'student_code'),
                Rule::unique('users', 'code'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'password' => ['required', 'string', 'min:8'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'class_id' => ['nullable', 'integer', 'exists:administrative_classes,id'],
            'cohort_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', Rule::enum(Gender::class)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', Rule::enum(StudentStatus::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Họ và tên sinh viên là bắt buộc.',
            'student_code.required' => 'Mã sinh viên (MSSV) là bắt buộc.',
            'student_code.unique' => 'Mã sinh viên này đã tồn tại trong hệ thống.',
            'email.required' => 'Địa chỉ email là bắt buộc.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng cho một tài khoản khác.',
            'password.required' => 'Mật khẩu khởi tạo là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'program_id.required' => 'Vui lòng chọn ngành đào tạo.',
            'program_id.exists' => 'Ngành đào tạo không hợp lệ.',
            'class_id.exists' => 'Lớp hành chính không hợp lệ.',
            'cohort_year.required' => 'Khóa tuyển sinh là bắt buộc.',
            'cohort_year.integer' => 'Khóa tuyển sinh phải là số năm hợp lệ.',
            'date_of_birth.before' => 'Ngày sinh phải trước ngày hiện tại.',
        ];
    }
}
