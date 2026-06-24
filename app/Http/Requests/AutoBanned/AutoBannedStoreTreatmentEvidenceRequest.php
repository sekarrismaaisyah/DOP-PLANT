<?php

declare(strict_types=1);

namespace App\Http\Requests\AutoBanned;

use App\Http\Requests\AutoBanned\Concerns\ValidatesAutoBannedScrDailyBannedLink;
use App\Http\Requests\AutoBanned\Concerns\ValidatesAutoBannedTreatmentEvidenceFile;
use Illuminate\Foundation\Http\FormRequest;

class AutoBannedStoreTreatmentEvidenceRequest extends FormRequest
{
    use ValidatesAutoBannedScrDailyBannedLink;
    use ValidatesAutoBannedTreatmentEvidenceFile;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge([
            'sid' => ['required', 'string', 'max:64'],
            'week' => ['required', 'string', 'max:8'],
            'year' => ['required', 'string', 'max:8'],
            'alasan_pengajuan' => ['required', 'string', 'max:2000'],
            'evidence_file' => $this->treatmentEvidenceFileRules(),
        ], $this->scrDailyBannedIdRules());
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge($this->treatmentEvidenceFileMessages(), $this->scrDailyBannedIdMessages(), [
            'sid.required' => 'SID wajib diisi.',
            'week.required' => 'Minggu periode wajib diisi.',
            'year.required' => 'Tahun periode wajib diisi.',
            'alasan_pengajuan.required' => 'Ringkasan treatment wajib diisi.',
        ]);
    }
}
