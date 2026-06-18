<?php

declare(strict_types=1);

namespace App\Http\Requests\FatigueManagement;

use App\Enums\FatigueManagementEvaluationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FatigueManagementStoreEvidenceRequest extends FormRequest
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
            'program_key' => ['required', 'string', 'max:64'],
            'partner_key' => ['required', 'string', 'max:32'],
            'year' => ['required', 'integer', 'min:2024', 'max:2030'],
            'iso_week' => ['required', 'string', 'regex:/^W\d{2}$/'],
            'evidence_file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx,zip'],
            'evidence_notes' => ['nullable', 'string', 'max:2000'],
            'pic_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
