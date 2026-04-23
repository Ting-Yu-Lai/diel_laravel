<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('admin');

        return [
            'username'  => "required|string|max:50|unique:admins,username,{$id}",
            'password'  => ['nullable', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&^_\-]/'],
            'full_name' => 'nullable|string|max:100',
            'power'     => 'required|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '帳號為必填',
            'username.unique'   => '此帳號已被使用',
            'password.min'      => '密碼至少 8 個字元',
            'password.regex'    => '密碼須包含大寫字母、小寫字母、數字及特殊符號（@$!%*#?&^_-）',
            'power.required'    => '請選擇權限',
            'power.in'          => '權限值無效',
        ];
    }
}
