<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeerPressureSpeakUpFatigueRequest extends FormRequest
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
            'site' => ['nullable', 'string', 'max:255'],
            'perusahaan' => ['nullable', 'string', 'max:255'],
            'sid' => ['required', 'string', 'max:32'],
            'nama' => ['required', 'string', 'max:255'],
            'tanggal' => ['required', 'date'],
            'waktu' => ['required', 'date_format:H:i'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesPayload(): array
    {
        $v = $this->validated();
        $waktu = (string) $v['waktu'];

        return [
            'site' => filled($v['site'] ?? null) ? trim((string) $v['site']) : null,
            'perusahaan' => filled($v['perusahaan'] ?? null) ? trim((string) $v['perusahaan']) : null,
            'sid' => trim((string) $v['sid']),
            'nama' => trim((string) $v['nama']),
            'tanggal' => $v['tanggal'],
            'waktu' => strlen($waktu) === 5 ? $waktu . ':00' : $waktu,
        ];
    }
}
