<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateMemberProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = Auth::guard('member')->id();

        return [
            'email' => ['required', 'email', 'max:100', Rule::unique('members', 'email')->ignore($id)],
            'phone' => ['required', 'string', 'max:20',  Rule::unique('members', 'phone')->ignore($id)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email 為必填',
            'email.email'    => 'Email 格式不正確',
            'email.unique'   => '此 Email 已被使用',
            'phone.required' => '手機號碼為必填',
            'phone.unique'   => '此手機號碼已被使用',
        ];
    }
}
