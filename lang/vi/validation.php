<?php

/*
| Ban dich tieng Viet cho cac rule thuong dung. Rule chua dich se tu dong
| roi ve lang/en (fallback_locale) cua framework.
*/

return [
    'accepted' => ':attribute phải được chấp nhận.',
    'boolean' => ':attribute phải là true hoặc false.',
    'confirmed' => 'Xác nhận :attribute không khớp.',
    'date' => ':attribute không phải là ngày hợp lệ.',
    'email' => ':attribute phải là địa chỉ email hợp lệ.',
    'exists' => ':attribute đã chọn không hợp lệ.',
    'integer' => ':attribute phải là số nguyên.',
    'max' => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'string' => ':attribute không được dài quá :max ký tự.',
    ],
    'min' => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :min.',
        'string' => ':attribute phải có ít nhất :min ký tự.',
    ],
    'numeric' => ':attribute phải là số.',
    'required' => 'Vui lòng nhập :attribute.',
    'string' => ':attribute phải là chuỗi ký tự.',
    'unique' => ':attribute đã tồn tại.',

    'attributes' => [
        'email' => 'email',
        'password' => 'mật khẩu',
        'name' => 'họ tên',
        'code' => 'mã số',
    ],
];
