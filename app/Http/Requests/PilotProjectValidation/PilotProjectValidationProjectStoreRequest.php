<?php

declare(strict_types=1);

namespace App\Http\Requests\PilotProjectValidation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PilotProjectValidationProjectStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'project_name' => ['required', 'string', 'max:255', Rule::unique('pilot_project_validation_projects', 'project_name')],
            'subtitle' => ['nullable', 'string'],
            'pilot_area' => ['nullable', 'string', 'max:512'],
            'support' => ['nullable', 'string'],
            'current_phase' => ['nullable', 'string', 'max:255'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'current_period' => ['nullable', 'string', 'max:255'],
            'next_milestone' => ['nullable', 'string'],
        ];
    }
}
