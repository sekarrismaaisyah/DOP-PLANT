<?php

namespace App\Http\Requests\PembatasanLV;

use App\Services\PembatasanLV\PembatasanLVDriverOptionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PembatasanLVInputasiOrangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['schedule', 'unschedule'])],
            'sid' => ['required', 'string', 'max:64'],
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'string', 'max:64'],
            'nama_perusahaan' => ['nullable', 'string', 'max:255'],
            'site' => ['nullable', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string', 'max:2000'],
            'control_room' => ['required', 'string', 'max:255'],
            'aktivitas' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $karyawan = app(PembatasanLVDriverOptionService::class)->findBySid(
                (string) $this->input('sid', '')
            );

            if ($karyawan === null) {
                $validator->errors()->add('sid', 'SID tidak ditemukan di data karyawan.');

                return;
            }

            if (mb_strtolower(trim((string) $this->input('nama', ''))) !== mb_strtolower($karyawan['nama'])) {
                $validator->errors()->add('sid', 'Data karyawan tidak sesuai. Pilih ulang SID dari daftar.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'status' => 'status',
            'sid' => 'SID',
            'nama' => 'nama',
            'lokasi' => 'lokasi',
            'detail_lokasi' => 'detail lokasi',
            'control_room' => 'control room',
            'aktivitas' => 'aktivitas',
            'catatan' => 'catatan',
        ];
    }
}
