<?php

declare(strict_types=1);

namespace App\Http\Requests\PembatasanLV;

use Illuminate\Foundation\Http\FormRequest;

class PembatasanLVMasterAktivitasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site' => ['required', 'string', 'max:255'],
            'perusahaan' => ['required', 'string', 'max:255'],
            'departemen' => ['required', 'string', 'max:255'],
            'kategori_aktivitas_luar_kabin' => ['required', 'string', 'max:5000'],
            'detail_aktivitas_pengoperasian_lv' => ['required', 'string', 'max:5000'],
            'frekuensi_aktivitas_per_shift' => ['required', 'integer', 'min:0', 'max:9999'],
            'estimasi_jumlah_lv_per_shift' => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }

    public function attributes(): array
    {
        return [
            'site' => 'site',
            'perusahaan' => 'perusahaan',
            'departemen' => 'departemen',
            'kategori_aktivitas_luar_kabin' => 'kategori aktivitas pekerjaan di luar kabin',
            'detail_aktivitas_pengoperasian_lv' => 'detail aktivitas pengoperasian LV',
            'frekuensi_aktivitas_per_shift' => 'frekuensi aktivitas dalam 1 shift',
            'estimasi_jumlah_lv_per_shift' => 'estimasi jumlah LV beraktivitas dalam 1 shift',
        ];
    }
}
