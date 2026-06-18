<?php

declare(strict_types=1);

namespace App\Http\Requests\FatigueManagement;

use App\Enums\FatigueManagementEvaluationStatus;
use App\Models\FatigueManagementProgramMonitoring;
use App\Services\FatigueManagement\FatigueManagementPartnerAccessService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FatigueManagementStoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $accessService = app(FatigueManagementPartnerAccessService::class);

        if (! $accessService->isGmoViewer($user)) {
            return false;
        }

        $record = FatigueManagementProgramMonitoring::query()->find($this->route('id'));
        if ($record === null) {
            return false;
        }

        try {
            $accessService->contextForUser($user)->assertCanAccessPartner((string) $record->partner_key);
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
            'evaluation_status' => ['required', 'string', Rule::enum(FatigueManagementEvaluationStatus::class)],
            'evaluation_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'evaluation_notes' => ['nullable', 'string', 'max:3000'],
            'evaluated_by' => ['nullable', 'string', 'max:255'],
        ];
    }
}
