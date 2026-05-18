<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialCoefficientCreateRequest extends FormRequest
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
            'coefficient' => str_replace(',', '.', $this->coefficient),
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
            'keyword' => 'required',
            'coefficient' => 'required',

        ];
    }

}
