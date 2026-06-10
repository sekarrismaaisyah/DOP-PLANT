<?php

namespace App\Http\Requests\PembatasanLV;

use Illuminate\Foundation\Http\FormRequest;

class PembatasanLVControlRoomPengawasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'control_room' => ['required', 'string', 'max:255'],
            'nama_pengawas' => ['required', 'string', 'max:255'],
            'email_pengawas' => ['nullable', 'email', 'max:255'],
            'no_hp_pengawas' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'control_room' => 'control room',
            'nama_pengawas' => 'nama pengawas',
            'email_pengawas' => 'email',
            'no_hp_pengawas' => 'no. HP',
            'keterangan' => 'keterangan',
        ];
    }
}
