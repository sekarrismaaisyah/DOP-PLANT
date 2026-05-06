<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Event;
use App\Services\SidMeeting\SidMeetingWpKaryawanNitipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SidMeetingAttendanceController extends Controller
{
    public function form(string $qrToken): View
    {
        $event = Event::query()->with(['site', 'meetingType'])->where('qr_token', $qrToken)->firstOrFail();
        if (!$event->isOpenForAttendance()) {
            return view('sid-meeting.attendance-closed', ['event' => $event]);
        }

        return view('sid-meeting.attendance-form', ['event' => $event]);
    }

    public function lookup(Request $request, string $qrToken, SidMeetingWpKaryawanNitipService $nitip): JsonResponse
    {
        try {
            $request->validate([
                'kode_sid' => 'required|string|max:128',
            ]);

            $event = Event::query()->where('qr_token', $qrToken)->firstOrFail();
            if (! $event->isOpenForAttendance()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Absensi untuk event ini tidak dibuka.',
                ], 403);
            }

            $row = $nitip->findByKodeSid($request->query('kode_sid', ''));
            if ($row === null) {
                if (! $nitip->isNitipConnected()) {
                    return response()->json([
                        'ok' => false,
                        'message' => 'Layanan data sementara tidak tersedia. Coba lagi nanti.',
                    ], 503);
                }

                return response()->json([
                    'ok' => false,
                    'message' => 'Kode SID tidak ditemukan pada data WP Karyawan (Nitip).',
                ], 404);
            }

            return response()->json([
                'ok' => true,
                'data' => $row,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'ok' => false,
                'message' => 'Tautan absensi tidak valid atau event tidak ditemukan.',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'ok' => false,
                'message' => 'Terjadi kesalahan saat memuat data. Coba lagi.',
            ], 500);
        }
    }

    public function photoProxy(Request $request, string $qrToken): Response
    {
        try {
            $request->validate([
                'foto' => 'required|string|max:2048',
            ]);

            Event::query()->where('qr_token', $qrToken)->firstOrFail();

            $fotoUrl = trim((string) $request->query('foto', ''));
            $normalizedFotoUrl = str_replace(' ', '%20', $fotoUrl);
            if ($normalizedFotoUrl === '' || ! filter_var($normalizedFotoUrl, FILTER_VALIDATE_URL)) {
                return response('URL foto tidak valid.', 422, ['Content-Type' => 'text/plain; charset=UTF-8']);
            }

            $scheme = strtolower((string) parse_url($normalizedFotoUrl, PHP_URL_SCHEME));
            if (! in_array($scheme, ['http', 'https'], true)) {
                return response('Skema URL foto tidak didukung.', 422, ['Content-Type' => 'text/plain; charset=UTF-8']);
            }

            $remote = Http::timeout(12)->retry(1, 150)->get($normalizedFotoUrl);
            if (! $remote->successful()) {
                return response('Foto referensi tidak dapat diakses.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
            }

            $contentType = (string) $remote->header('Content-Type', 'application/octet-stream');
            if ($remote->body() === '') {
                return response('Foto referensi kosong.', 422, ['Content-Type' => 'text/plain; charset=UTF-8']);
            }
            if (stripos($contentType, 'image/') !== 0) {
                $contentType = 'image/jpeg';
            }

            return response($remote->body(), 200, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=600',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response('Event tidak ditemukan.', 404, ['Content-Type' => 'text/plain; charset=UTF-8']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return response('Gagal memuat foto referensi.', 500, ['Content-Type' => 'text/plain; charset=UTF-8']);
        }
    }

    public function submit(Request $request, string $qrToken, SidMeetingWpKaryawanNitipService $nitip): RedirectResponse
    {
        $request->validate([
            'kode_sid' => 'nullable|string|max:128',
            'no_sid' => 'nullable|boolean',
            'face_verified' => 'nullable|boolean',
            'face_distance' => 'nullable|numeric|min:0|max:2',
            'manual_nama' => 'nullable|string|max:255',
            'manual_perusahaan' => 'nullable|string|max:255',
            'manual_divisi' => 'nullable|string|max:255',
            'manual_departemen' => 'nullable|string|max:255',
            'manual_jabatan' => 'nullable|string|max:255',
        ]);
        $event = Event::query()->where('qr_token', $qrToken)->firstOrFail();

        if (!$event->isOpenForAttendance()) {
            return back()->with('error', 'Absensi sudah ditutup.');
        }

        $hasNoSid = $request->boolean('no_sid');
        $employee = null;

        if (! $hasNoSid) {
            $kodeSid = trim((string) $request->input('kode_sid', ''));
            if ($kodeSid === '') {
                return back()->withInput()->with('error', 'Kode SID wajib diisi atau pilih opsi "Tidak mempunyai SID".');
            }

            if (! $request->boolean('face_verified')) {
                return back()->withInput()->with('error', 'Verifikasi wajah wajib dilakukan sebelum absensi.');
            }

            $employee = $this->resolveEmployeeForAttendance($kodeSid, $nitip);
        }

        if (! $employee) {
            $employee = $this->createManualEmployeeFromRequest($request);
        }

        if (! $employee) {
            return back()
                ->withInput()
                ->with('error', 'SID tidak ditemukan. Lengkapi form manual untuk melanjutkan absensi.');
        }

        $exists = Attendance::query()->where('event_id', $event->id)->where('employee_id', $employee->id)->exists();
        if ($exists) {
            return back()->with('error', 'SID sudah melakukan absensi pada event ini.');
        }

        Attendance::query()->create([
            'event_id' => $event->id,
            'employee_id' => $employee->id,
            'kode_sid' => $employee->kode_sid,
            'nama_snapshot' => $employee->nama,
            'perusahaan_snapshot' => $employee->company->name,
            'jabatan_struktural_snapshot' => $employee->jabatan_struktural,
            'jabatan_fungsional_snapshot' => $employee->jabatan_fungsional,
            'attended_at' => now(),
            'input_method' => 'qr',
        ]);

        return back()->with('success', 'Absensi berhasil. Terima kasih.');
    }

    private function createManualEmployeeFromRequest(Request $request): ?Employee
    {
        $nama = trim((string) $request->input('manual_nama', ''));
        $perusahaan = trim((string) $request->input('manual_perusahaan', ''));
        $divisi = trim((string) $request->input('manual_divisi', ''));
        $departemen = trim((string) $request->input('manual_departemen', ''));
        $jabatan = trim((string) $request->input('manual_jabatan', ''));
        $kodeSid = trim((string) $request->input('kode_sid', ''));
        $hasNoSid = $request->boolean('no_sid');

        if ($nama === '' || $perusahaan === '' || $jabatan === '') {
            return null;
        }

        if ($kodeSid === '') {
            if (! $hasNoSid) {
                return null;
            }

            do {
                $kodeSid = 'MANUAL-' . Str::upper(Str::random(8));
            } while (Employee::query()->where('kode_sid', $kodeSid)->exists());
        }

        $company = Company::query()->firstOrCreate(
            ['name' => $perusahaan],
            ['is_active' => true]
        );

        $jabatanStruktural = collect([
            $divisi !== '' ? 'Divisi: ' . $divisi : null,
            $departemen !== '' ? 'Departemen: ' . $departemen : null,
        ])->filter()->implode(' | ');

        return Employee::query()->updateOrCreate(
            ['kode_sid' => $kodeSid],
            [
                'nama' => $nama,
                'company_id' => $company->id,
                'jabatan_struktural' => $jabatanStruktural !== '' ? $jabatanStruktural : null,
                'jabatan_fungsional' => $jabatan,
                'is_active' => true,
            ]
        )->load('company');
    }

    private function resolveEmployeeForAttendance(string $inputKodeSid, SidMeetingWpKaryawanNitipService $nitip): ?Employee
    {
        $normalized = trim($inputKodeSid);
        if ($normalized === '') {
            return null;
        }

        $employee = Employee::query()
            ->with('company')
            ->whereRaw('LOWER(kode_sid) = ?', [Str::lower($normalized)])
            ->first();

        if ($employee) {
            if (! $employee->is_active) {
                $employee->update(['is_active' => true]);
            }

            return $employee;
        }

        $row = $nitip->findByKodeSid($normalized);
        if ($row === null || empty($row['nama'])) {
            return null;
        }

        $canonicalSid = $row['kode_sid'] ?? $normalized;
        if ($canonicalSid === null || trim($canonicalSid) === '') {
            $canonicalSid = $normalized;
        }
        $canonicalSid = trim($canonicalSid);

        $perusahaan = $row['nama_perusahaan'] ?? 'Tidak diketahui';
        if (trim($perusahaan) === '') {
            $perusahaan = 'Tidak diketahui';
        }

        $company = Company::query()->firstOrCreate(
            ['name' => $perusahaan],
            ['is_active' => true]
        );

        return Employee::query()->updateOrCreate(
            ['kode_sid' => $canonicalSid],
            [
                'nama' => $row['nama'],
                'company_id' => $company->id,
                'jabatan_struktural' => $row['jabatan_struktural'],
                'jabatan_fungsional' => $row['jabatan_fungsional'],
                'is_active' => true,
            ]
        )->load('company');
    }
}
