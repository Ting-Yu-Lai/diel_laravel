<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTreatmentRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id'       => 'required|integer|exists:customers,id',
            'record_date'       => 'required|date',
            'notes'             => 'nullable|string|max:1000',
            'doctor_ids'        => 'nullable|array',
            'doctor_ids.*'      => 'integer|exists:staff,id',
            'nurse_ids'         => 'nullable|array',
            'nurse_ids.*'       => 'integer|exists:staff,id',
            'consultant_id'     => 'nullable|integer|exists:staff,id',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => '請選擇客戶',
            'customer_id.exists'   => '所選客戶不存在',
            'record_date.required' => '來診日期為必填',
            'record_date.date'     => '來診日期格式錯誤',
        ];
    }
}
