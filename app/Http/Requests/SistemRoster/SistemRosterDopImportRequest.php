<?php

declare(strict_types=1);

namespace App\Http\Requests\SistemRoster;

use Illuminate\Foundation\Http\FormRequest;

class SistemRosterDopImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>|string>
     */
    public function rules(): array
    {
        return [
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'excel_file.required' => 'Pilih file Excel (.xlsx atau .xls) terlebih dahulu.',
            'excel_file.mimes' => 'Format file harus .xlsx atau .xls.',
            'excel_file.max' => 'Ukuran file maksimal 10 MB.',
        ];
    }
}
