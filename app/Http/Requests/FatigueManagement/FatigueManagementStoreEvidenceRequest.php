<?php

declare(strict_types=1);

namespace App\Http\Requests\FatigueManagement;

use App\Enums\FatigueManagementEvaluationStatus;
use App\Services\FatigueManagement\FatigueManagementPartnerAccessService;
use App\Support\FatigueManagement\FatigueManagementCompanyResolver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FatigueManagementStoreEvidenceRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $access = app(FatigueManagementPartnerAccessService::class)->contextForUser($this->user());

        if ($access->isLocked()) {
            $this->merge(['partner_key' => $access->partnerKey]);
        }
    }

    public function authorize(): bool
    {
        $partnerKey = strtoupper((string) $this->input('partner_key', ''));

        if ($partnerKey === '' || ! FatigueManagementCompanyResolver::isKnownPartnerKey($partnerKey)) {
            return false;
        }

        try {
            app(FatigueManagementPartnerAccessService::class)
                ->contextForUser($this->user())
                ->assertCanAccessPartner($partnerKey);
        } catch (\Throwable) {
            return false;
        }

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
            'frequency_slot' => ['required', 'string', 'max:32', 'regex:/^[a-z0-9\-]+$/'],
            'evidence_file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx,zip'],
            'evidence_notes' => ['nullable', 'string', 'max:2000'],
            'pic_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
