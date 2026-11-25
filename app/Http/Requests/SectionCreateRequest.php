<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionCreateRequest extends FormRequest
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
            'number' => 'required|unique:sections,number',
            'chief_user_id' => ['required', 'integer', 'exists:users,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
        ];
    }

    public function messages()
    {
        return [
            'number.unique' => 'Така ділянка вже є в довіднику',
        ];
    }

    public function attributes(): array
    {
        return ['department_id' => 'цех', 'chief_user_id' => 'начальник ділянки'];
    }
}
