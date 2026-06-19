<?php

declare(strict_types=1);

namespace App\Http\Requests\AutoBanned;

use Illuminate\Foundation\Http\FormRequest;

class AutoBannedPublicStoreTreatmentEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (! (bool) config('auto_banned.treatment.public_form_enabled', true)) {
            return false;
        }

        return trim((string) $this->input('website', '')) === '';
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
            'week' => ['required', 'string', 'max:8'],
            'year' => ['required', 'string', 'max:8'],
            'nama_pengirim' => ['required', 'string', 'max:255'],
            'alasan_pengajuan' => ['required', 'string', 'max:2000'],
            'evidence_file' => ['required', 'file', 'max:'.$maxKb, 'mimes:'.implode(',', $mimes)],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sid.required' => 'Masukkan SID Anda terlebih dahulu.',
            'nama_pengirim.required' => 'Masukkan nama lengkap Anda.',
            'alasan_pengajuan.required' => 'Ceritakan singkat tindakan perbaikan yang sudah dilakukan.',
            'evidence_file.required' => 'Lampirkan file bukti (foto atau dokumen).',
            'evidence_file.mimes' => 'File harus berupa PDF, foto, Word, atau Excel.',
            'evidence_file.max' => 'Ukuran file terlalu besar. Maksimal 10 MB.',
        ];
    }
}
