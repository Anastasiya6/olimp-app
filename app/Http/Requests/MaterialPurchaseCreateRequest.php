<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialPurchaseCreateRequest extends FormRequest
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
        // Заменяем запятые на точки для поля norm
        $this->merge([
            'norm' => str_replace(',', '.', $this->norm),
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
            'designation_entry' => 'required',
            'designation' => 'required',
            'norm' => 'required|numeric',
            'material_id' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'designation_entry.required' => 'Заповніть номер - Що',
            'designation.required' => 'Заповніть номер - Куди',
            'material_id.required' => 'Виберіть матеріал',
            'norm.required' => 'Заповніть норму',
            'norm.numeric' => 'Норма повинна бути числовим значенням',
        ];
    }
}
