<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('customer');

        return [
            'name'              => 'required|string|max:50',
            'gender'            => 'nullable|in:M,F,other',
            'birth_date'        => 'nullable|date',
            'phone'             => "required|string|max:20|unique:customers,phone,{$id}",
            'email'             => 'nullable|email|max:100',
            'id_number'         => "nullable|string|max:20|unique:customers,id_number,{$id}",
            'address'           => 'nullable|string|max:200',
            'occupation'        => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:50',
            'emergency_phone'   => 'nullable|string|max:20',
            'blood_type'        => 'nullable|in:A,B,AB,O,unknown',
            'allergies'         => 'nullable|string',
            'medical_history'   => 'nullable|string',
            'source'            => 'nullable|in:walk_in,referral,online,other',
            'notes'             => 'nullable|string',
            'is_active'         => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => '姓名為必填',
            'phone.required'   => '手機為必填',
            'phone.unique'     => '此手機號碼已被其他客戶使用',
            'id_number.unique' => '此身分證字號已被其他客戶使用',
            'email.email'      => 'Email 格式不正確',
        ];
    }
}
