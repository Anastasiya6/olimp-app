<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanTaskCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_name_id' => ['required'],
            'quantity_total' => ['required'],
            // 'category_code' => ['required'],
            'designation_designation' => ['required'],
            'receiver_department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages()
    {
        return [
            'order_name_id.required' => 'Виберіть замовлення',
            'quantity_total.required' => 'Введіть кількість деталей',
           // 'category_code.required' => 'Введіть шифр',
            'designation_designation.required' => 'Введіть номер деталі в поле "Що"',
            'receiver_department_id.required' => 'Оберіть цех отримувач',
            'receiver_department_id.integer' => 'Оберіть коректний цех отримувач',
            'receiver_department_id.exists' => 'Оберіть коректний цех отримувач',
        ];
    }
}
