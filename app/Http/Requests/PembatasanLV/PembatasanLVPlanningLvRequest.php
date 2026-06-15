<?php

declare(strict_types=1);

namespace App\Http\Requests\PembatasanLV;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PembatasanLVPlanningLvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_plan' => ['required', 'date'],
            'shift' => ['required', 'integer', Rule::in([1, 2])],
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

    public function attributes(): array
    {
        return [
            'tanggal_plan' => 'tanggal planning',
            'shift' => 'shift',
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
