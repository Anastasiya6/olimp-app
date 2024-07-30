<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'document_number' => 'required',
            'document_date' => 'required',
            'quantity' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'designation.required' => 'Заповніть номер - У графі "Пошук деталі"',
            'document_number.required' => 'Заповніть номер документу',
            'quantity.required' => 'Заповніть кількість',
        ];
    }
}
