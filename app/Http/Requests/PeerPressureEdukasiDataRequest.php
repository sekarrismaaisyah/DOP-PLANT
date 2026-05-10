<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeerPressureEdukasiDataRequest extends FormRequest
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
            'tanggal_temuan' => ['required', 'date'],
            'jam_temuan' => ['required', 'date_format:H:i'],
            'kelompok_lokasi_temuan' => ['required', 'string', 'max:255'],
            'lokasi_temuan' => ['required', 'string', 'max:255'],
            'kelompok_lokasi_edukasi' => ['required', 'string', 'max:255'],
            'lokasi_edukasi' => ['required', 'string', 'max:255'],
            'tanggal_edukasi' => ['required', 'date'],
            'jam_edukasi' => ['required', 'date_format:H:i'],
            'perusahaan' => ['required', 'string', 'max:255'],
            'site' => ['nullable', 'string', 'max:255'],
            'tasklist_temuan' => ['nullable', 'string', 'max:255'],
            'kronologi_temuan' => ['required', 'string'],
            'kategori_deviasi' => ['required', 'string', 'max:255'],
            'pemimpin_edukasi' => ['required', 'string', 'max:255'],
            'id_berecord' => ['nullable', 'string', 'max:64'],
            'jenis_kelompok_kerja' => ['nullable', 'string', 'max:255'],
            'kelompok_aktivitas_pekerjaan' => ['nullable', 'string', 'max:255'],
            'aktivitas_pekerjaan' => ['nullable', 'string', 'max:255'],
            'departemen' => ['nullable', 'string', 'max:100'],
            'evidence_url' => ['nullable', 'string', 'max:2048'],
            'durasi_edukasi_menit' => ['required', 'integer', 'min:0', 'max:65535'],
            'status_pelaksanaan_edukasi' => ['required', 'string', 'max:50'],

            'pelanggar' => ['nullable', 'array'],
            'pelanggar.*.sid' => ['nullable', 'string', 'max:32'],
            'pelanggar.*.nama' => ['nullable', 'string', 'max:255'],

            'peer' => ['nullable', 'array'],
            'peer.*.sid' => ['nullable', 'string', 'max:32'],
            'peer.*.nama' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Persistable attributes for kejadian (header) row.
     *
     * @return array<string, mixed>
     */
    public function kejadianAttributes(): array
    {
        $v = $this->validated();

        return [
            'tanggal_temuan' => $v['tanggal_temuan'],
            'jam_temuan' => $v['jam_temuan'] . (strlen((string) $v['jam_temuan']) === 5 ? ':00' : ''),
            'kelompok_lokasi_temuan' => $v['kelompok_lokasi_temuan'],
            'lokasi_temuan' => $v['lokasi_temuan'],
            'kelompok_lokasi_edukasi' => $v['kelompok_lokasi_edukasi'],
            'lokasi_edukasi' => $v['lokasi_edukasi'],
            'tanggal_edukasi' => $v['tanggal_edukasi'],
            'jam_edukasi' => $v['jam_edukasi'] . (strlen((string) $v['jam_edukasi']) === 5 ? ':00' : ''),
            'perusahaan' => $v['perusahaan'],
            'site' => filled($v['site'] ?? null) ? trim((string) $v['site']) : null,
            'tasklist_temuan' => $v['tasklist_temuan'] ?? null,
            'kronologi_temuan' => $v['kronologi_temuan'],
            'kategori_deviasi' => $v['kategori_deviasi'],
            'pemimpin_edukasi' => $v['pemimpin_edukasi'],
            'id_berecord' => $v['id_berecord'] ?? null,
            'jenis_kelompok_kerja' => $v['jenis_kelompok_kerja'] ?? null,
            'kelompok_aktivitas_pekerjaan' => $v['kelompok_aktivitas_pekerjaan'] ?? null,
            'aktivitas_pekerjaan' => $v['aktivitas_pekerjaan'] ?? null,
            'departemen' => $v['departemen'] ?? null,
            'evidence_url' => filled($v['evidence_url'] ?? null) ? trim((string) $v['evidence_url']) : null,
            'durasi_edukasi_menit' => (int) $v['durasi_edukasi_menit'],
            'status_pelaksanaan_edukasi' => $v['status_pelaksanaan_edukasi'],
        ];
    }

    /**
     * @return list<array{sid: string, nama: string, peran: 'pelanggar'|'peer', urutan: int}>
     */
    public function pesertaPayload(): array
    {
        $v = $this->validated();
        $out = [];
        $urutan = 0;

        foreach ($v['pelanggar'] ?? [] as $row) {
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
                'peran' => 'pelanggar',
                'urutan' => $urutan++,
            ];
        }

        foreach ($v['peer'] ?? [] as $row) {
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
                'peran' => 'peer',
                'urutan' => $urutan++,
            ];
        }

        return $out;
    }
}
