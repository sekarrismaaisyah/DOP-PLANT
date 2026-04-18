<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeerPressureSbsKelompokRequest extends FormRequest
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
            'level_grup' => ['required', 'string', 'max:255'],
            'nama_kelompok' => ['required', 'string', 'max:255'],
            'nama_bapak_asuh' => ['required', 'string', 'max:255'],
            'sid_bapak_asuh' => ['required', 'string', 'max:32'],

            'anggota' => ['nullable', 'array'],
            'anggota.*.sid' => ['nullable', 'string', 'max:32'],
            'anggota.*.nama' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function kelompokAttributes(): array
    {
        $v = $this->validated();

        return [
            'site' => filled($v['site'] ?? null) ? trim((string) $v['site']) : null,
            'perusahaan' => filled($v['perusahaan'] ?? null) ? trim((string) $v['perusahaan']) : null,
            'level_grup' => $v['level_grup'],
            'nama_kelompok' => $v['nama_kelompok'],
            'nama_bapak_asuh' => $v['nama_bapak_asuh'],
            'sid_bapak_asuh' => trim((string) $v['sid_bapak_asuh']),
        ];
    }

    /**
     * @return list<array{sid: string, nama: string, urutan: int}>
     */
    public function anggotaPayload(): array
    {
        $v = $this->validated();
        $out = [];
        $urutan = 0;

        foreach ($v['anggota'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $sid = trim((string) ($row['sid'] ?? ''));
            $nama = trim((string) ($row['nama'] ?? ''));
            if ($sid === '' && $nama === '') {
                continue;
            }
            $out[] = [
                'sid' => $sid !== '' ? $sid : '-',
                'nama' => $nama !== '' ? $nama : '-',
                'urutan' => $urutan++,
            ];
        }

        return $out;
    }
}
