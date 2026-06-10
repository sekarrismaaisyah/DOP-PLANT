<?php

namespace App\Http\Requests\PembatasanLV;

use App\Services\PembatasanLV\PembatasanLVKapasitasLokasiService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PembatasanLVInputasiLvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['schedule', 'unschedule'])],
            'nama_driver' => ['required', 'string', 'max:255'],
            'driver_ref' => ['nullable', 'string', 'max:64'],
            'no_lambung' => ['required', 'string', 'max:64'],
            'id_unit' => ['nullable', 'string', 'max:64'],
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

            $kapasitas = app(PembatasanLVKapasitasLokasiService::class)->check(
                (string) $this->input('lokasi', ''),
                $this->input('detail_lokasi')
            );

            if ($kapasitas['has_batas'] && ! $kapasitas['can_input']) {
                $validator->errors()->add('lokasi', $kapasitas['message'] ?? 'Kapasitas lokasi sudah penuh.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'status' => 'status',
            'nama_driver' => 'nama driver',
            'no_lambung' => 'no unit',
            'lokasi' => 'lokasi',
            'detail_lokasi' => 'detail lokasi',
            'control_room' => 'control room',
            'aktivitas' => 'aktivitas',
            'catatan' => 'catatan',
        ];
    }
}
