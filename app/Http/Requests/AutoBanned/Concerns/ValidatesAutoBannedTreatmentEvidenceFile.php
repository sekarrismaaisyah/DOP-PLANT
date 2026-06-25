<?php

declare(strict_types=1);

namespace App\Http\Requests\AutoBanned\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Validator;

trait ValidatesAutoBannedTreatmentEvidenceFile
{
    /**
     * @return array<int, string>
     */
    protected function treatmentEvidenceFileRules(): array
    {
        return [
            function (string $attribute, mixed $value, \Closure $fail): void {
                $this->validateTreatmentEvidenceUpload($value, $fail);
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function treatmentEvidenceFileMessages(): array
    {
        return [
            'evidence_file.required' => 'Lampirkan file bukti (foto atau dokumen).',
            'evidence_file.uploaded' => 'Gagal mengupload file. Pastikan ukuran maks. 10 MB dan koneksi stabil, lalu coba lagi.',
        ];
    }

    protected function validateTreatmentEvidenceUpload(mixed $value, \Closure $fail): void
    {
        if ($value === null || $value === '') {
            $contentLength = (int) request()->server('CONTENT_LENGTH', 0);
            $postMax = $this->parseIniSize((string) ini_get('post_max_size'));

            if ($contentLength > 0 && $postMax > 0 && $contentLength > $postMax) {
                $fail('Ukuran unggahan melebihi batas server (post_max_size). Kurangi ukuran file atau kompres foto.');

                return;
            }

            $fail('Lampirkan file bukti (foto atau dokumen).');

            return;
        }

        if (! $value instanceof UploadedFile) {
            $fail('Pilih file bukti terlebih dahulu.');

            return;
        }

        if (! $value->isValid()) {
            $fail($this->resolveUploadErrorMessage($value));

            return;
        }

        $maxBytes = (int) config('auto_banned.treatment.max_upload_kb', 10240) * 1024;
        $size = (int) $value->getSize();

        if ($size <= 0) {
            $fail('File kosong atau rusak. Pilih file lain.');

            return;
        }

        if ($size > $maxBytes) {
            $maxMb = (int) ceil($maxBytes / 1024 / 1024);
            $fail("Ukuran file terlalu besar. Maksimal {$maxMb} MB.");

            return;
        }

        $extension = strtolower($value->getClientOriginalExtension() ?: $value->extension() ?: '');
        $allowedExtensions = array_map(
            static fn (string $ext): string => strtolower(ltrim($ext, '.')),
            config('auto_banned.treatment.allowed_mimes', [])
        );

        $mime = strtolower((string) $value->getMimeType());
        $allowedMimes = config('auto_banned.treatment.allowed_mime_types', []);

        $extensionOk = $extension !== '' && in_array($extension, $allowedExtensions, true);
        $mimeOk = $mime !== '' && (
            in_array($mime, $allowedMimes, true)
            || str_starts_with($mime, 'image/')
            || $mime === 'application/pdf'
            || str_contains($mime, 'officedocument')
            || str_contains($mime, 'msword')
            || str_contains($mime, 'spreadsheet')
            || $mime === 'application/zip'
        );

        if (! $extensionOk && ! $mimeOk) {
            $fail('Format file tidak didukung. Gunakan PDF, foto (JPG/PNG), Word, atau Excel.');
        }
    }

    protected function resolveUploadErrorMessage(UploadedFile $file): string
    {
        return match ($file->getError()) {
            UPLOAD_ERR_INI_SIZE => 'File melebihi batas upload server (upload_max_filesize). Kurangi ukuran file atau hubungi admin.',
            UPLOAD_ERR_FORM_SIZE => 'File melebihi batas formulir (post_max_size). Kurangi ukuran file.',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian. Silakan coba lagi.',
            UPLOAD_ERR_NO_FILE => 'Pilih file bukti terlebih dahulu.',
            UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION => 'Server tidak dapat menerima file saat ini. Hubungi admin.',
            default => 'Gagal mengupload file. Pastikan ukuran maks. 10 MB dan koneksi stabil, lalu coba lagi.',
        };
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->has('evidence_file')) {
                return;
            }

            $contentLength = (int) $this->server('CONTENT_LENGTH', 0);
            $postMax = $this->parseIniSize((string) ini_get('post_max_size'));

            if ($contentLength > 0 && $postMax > 0 && $contentLength > $postMax && ! $this->hasFile('evidence_file')) {
                $validator->errors()->add(
                    'evidence_file',
                    'Ukuran unggahan melebihi batas server (post_max_size). Kurangi ukuran file.'
                );
            }
        });
    }

    private function parseIniSize(string $value): int
    {
        $value = trim($value);
        if ($value === '' || $value === '-1') {
            return 0;
        }

        $unit = strtolower(substr($value, -1));
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
