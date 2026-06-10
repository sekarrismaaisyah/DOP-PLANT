<?php

namespace App\Http\Requests\PembatasanLV;

use Illuminate\Foundation\Http\FormRequest;

class PembatasanBatasLvPerLokasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string', 'max:2000'],
            'batas_lv' => ['required', 'integer', 'min:0', 'max:999999'],
        ];
    }

    public function attributes(): array
    {
        return [
            'site' => 'site',
            'lokasi' => 'lokasi',
            'detail_lokasi' => 'detail lokasi',
            'batas_lv' => 'batas LV',
        ];
    }
}
