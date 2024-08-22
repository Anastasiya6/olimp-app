<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseCreateRequest extends FormRequest
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
            'designation_entry' => 'required',
            'designation' => 'required',
            'quantity' => 'required|numeric',
        ];
        //|different:designation_designation
    }

    public function messages()
    {
        return [
            'designation_entry.required' => 'Заповніть номер - Що',
            'designation.required' => 'Заповніть номер - Куди',
            'quantity.required' => 'Введіть кількість',
            //'designation_entry_designation.different' => 'Що не може бути таким же як Куди',
            'quantity.numeric' => 'Кількість повинна бути числовим значенням',

        ];
    }
}
