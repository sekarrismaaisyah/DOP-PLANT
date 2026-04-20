<?php

declare(strict_types=1);

namespace App\Http\Requests\PilotProjectValidation;

use Illuminate\Foundation\Http\FormRequest;

class PilotProjectValidationPortfolioSaveRequest extends FormRequest
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
            /* `required` menolak array kosong — menyebabkan gagal simpan saat portfolio dikosongkan */
            'projects' => ['present', 'array'],
            'historySnapshots' => ['sometimes', 'array'],
        ];
    }
}
