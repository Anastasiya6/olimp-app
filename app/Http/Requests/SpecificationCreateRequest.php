<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpecificationCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Можно оставить как true, если авторизация не требуется, требуется, как будет авторизация, изменить надо на true
    }
    protected function prepareForValidation()
    {
        // Заменяем запятые на точки для поля specification_quantity
        $this->merge([
            'specification_quantity' => str_replace(',', '.', $this->specification_quantity),
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'designation_entry_designation' => 'required|different:designation_designation',
            'designation_designation' => 'required',
            'specification_quantity' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'designation_entry_designation.required' => 'Заповніть номер - Що',
            'designation_designation.required' => 'Заповніть номер - Куди',
            'specification_quantity.required' => 'Введіть кількість',
            'designation_entry_designation.different' => 'Що не може бути таким же як Куди',
            'specification_quantity.numeric' => 'Кількість повинна бути числовим значенням',

        ];
    }
}
