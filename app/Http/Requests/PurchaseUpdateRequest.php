<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Можно оставить как true, если авторизация не требуется, требуется, как будет авторизация, изменить надо на true
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'purchase' => 'required',
            'quantity' => 'required|numeric',
        ];
        //|different:designation_designation
    }

    public function messages()
    {
        return [
            'purchase' => 'Заповніть назву покупної деталі',
            'quantity.required' => 'Введіть кількість',
            'quantity.numeric' => 'Кількість повинна бути числовим значенням',

        ];
    }
}
