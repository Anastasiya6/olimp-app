<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesignationMaterialCreateRequest extends FormRequest
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
            'designation_id' => 'required',
            'material_id' => 'required',

        ];
    }

    public function messages()
    {
        return [
            'designation_id.required' => 'Виберіть деталь',
            'material_id.required' => 'Виберіть матеріал',
            'norm.required' => 'Заповніть норму',
            'norm.numeric' => 'Норма повинна бути числовим значенням',
            'norm.min' => 'Норма повинна бути більше нуля',
        ];
    }
}
