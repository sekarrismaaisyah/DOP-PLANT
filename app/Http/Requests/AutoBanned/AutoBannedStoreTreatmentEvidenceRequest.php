<?php

declare(strict_types=1);

namespace App\Http\Requests\AutoBanned;

use Illuminate\Foundation\Http\FormRequest;

class AutoBannedStoreTreatmentEvidenceRequest extends FormRequest
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
        $maxKb = (int) config('auto_banned.treatment.max_upload_kb', 10240);
        $mimes = config('auto_banned.treatment.allowed_mimes', ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xlsx', 'xls']);

        return [
            'sid' => ['required', 'string', 'max:64'],
            'week' => ['required', 'string', 'max:8', 'regex:/^W\d{1,2}$/i'],
            'year' => ['required', 'string', 'max:8'],
            'alasan_pengajuan' => ['required', 'string', 'max:2000'],
            'evidence_file' => ['required', 'file', 'max:'.$maxKb, 'mimes:'.implode(',', $mimes)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sid.required' => 'SID wajib diisi.',
            'week.required' => 'Minggu periode wajib diisi.',
            'year.required' => 'Tahun periode wajib diisi.',
            'alasan_pengajuan.required' => 'Ringkasan treatment wajib diisi.',
            'evidence_file.required' => 'File evidence treatment wajib diupload.',
            'evidence_file.mimes' => 'Format file tidak didukung.',
            'evidence_file.max' => 'Ukuran file melebihi batas yang diizinkan.',
        ];
    }
}
