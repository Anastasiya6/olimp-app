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
            'quantity' => ['required'],
            'category_code' => ['required'],
            'designation_entry_designation' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'order_name_id.required' => 'Виберіть замовлення',
            'quantity.required' => 'Введіть кількість деталей',
            'category_code.required' => 'Введіть шифр',
            'designation_entry_designation.required' => 'Введіть номер деталі в поле "Що"',
        ];
    }
}
