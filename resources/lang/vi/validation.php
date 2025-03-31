<?php

return [
    'required' => ':attribute không được để trống.',
    'email' => ':attribute không đúng định dạng.',
    'unique' => ':attribute đã tồn tại.',
    'min' => ':attribute phải có ít nhất :min ký tự.',
    
    'confirmed' => ':attribute xác nhận không khớp.',
    'regex' => ':attribute không đúng định dạng.',
    'in' => ':attribute không hợp lệ.',
   
    'string' => ':attribute phải là chuỗi ký tự.',
    'numeric' => ':attribute phải là số.',
    'array' => ':attribute phải là danh sách.',
    'max' => [
        'numeric' => ':attribute không được lớn hơn :max.',
        'file' => ':attribute không được lớn hơn :max KB.',
        'string' => ':attribute không được dài quá :max ký tự.',
        'array' => ':attribute không được nhiều hơn :max mục.',
    ],
    'image' => ':attribute phải là hình ảnh.',
    'mimes' => ':attribute phải có định dạng: :values.',

    'attributes' => [
        'name' => 'Họ tên',
        'email' => 'Email',
        'password' => 'Mật khẩu',
        'gender' => 'Giới tính',
    ],
];
