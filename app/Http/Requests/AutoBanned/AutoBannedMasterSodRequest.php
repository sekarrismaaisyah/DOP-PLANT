<?php

declare(strict_types=1);

namespace App\Http\Requests\AutoBanned;

use Illuminate\Foundation\Http\FormRequest;

class AutoBannedMasterSodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'site' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:32'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama',
            'site' => 'site',
            'no_hp' => 'no. HP',
        ];
    }
}
