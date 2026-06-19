<?php

declare(strict_types=1);

namespace App\Http\Requests\AutoBanned;

use App\Enums\AutoBannedUnbanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AutoBannedReviewUnbanRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['approve', 'reject'])],
            'catatan_review' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Aksi verifikasi wajib dipilih.',
            'action.in' => 'Aksi verifikasi tidak valid.',
        ];
    }

    public function isApprove(): bool
    {
        return (string) $this->input('action') === 'approve';
    }

    public function isReject(): bool
    {
        return (string) $this->input('action') === 'reject';
    }
}
