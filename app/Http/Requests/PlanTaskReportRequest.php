<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanTaskReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'section_id' => ['nullable', 'integer', Rule::exists('sections', 'id')
                ->where('department_id', $this->route('sender_department') ?? $this->route('department'))],
        ];
    }

    public function sectionId(): ?int
    {
        $sectionId = $this->validated('section_id');

        return $sectionId === null ? null : (int) $sectionId;
    }
}
