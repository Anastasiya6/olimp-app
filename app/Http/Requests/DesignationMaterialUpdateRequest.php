<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DesignationMaterialUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'designation' => 'required',
            'material' => 'required',
            'norm' => 'required|numeric',

        ];
    }

    public function messages()
    {
        return [
            'designation.required' => 'Виберіть деталь',
            'material.required' => 'Виберіть матеріал',
            'norm.required' => 'Заповніть норму',
            'norm.numeric' => 'Норма повинна бути числовим значенням',
            //'norm.min' => 'Норма повинна бути більше нуля',
        ];
    }
}
