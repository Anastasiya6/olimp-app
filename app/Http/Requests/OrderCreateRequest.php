<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_number' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'order_number.required' => 'Введіть назву або номер замовлення',
        ];
    }
}
