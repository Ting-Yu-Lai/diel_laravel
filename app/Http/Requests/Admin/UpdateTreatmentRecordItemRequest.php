<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTreatmentRecordItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'treatment_id' => 'required|integer|exists:treatments,id',
            'price'        => 'required|integer|min:0',
            'cost'         => 'required|integer|min:0',
            'staff_id'     => 'nullable|integer|exists:staff,id',
            'notes'        => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'treatment_id.required' => '請選擇療程項目',
            'treatment_id.exists'   => '所選療程項目不存在',
            'price.required'        => '請填寫售價',
            'price.integer'         => '售價必須為整數',
            'price.min'             => '售價不得為負數',
            'cost.required'         => '請填寫成本',
            'cost.integer'          => '成本必須為整數',
            'cost.min'              => '成本不得為負數',
        ];
    }
}
