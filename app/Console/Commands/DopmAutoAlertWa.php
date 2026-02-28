<?php

namespace App\Console\Commands;

use App\Models\DopmAlertIntervensi;
use App\Models\DopmAlertPerIkk;
use App\Models\DopmWaNotificationLog;
use App\Models\IpkIkk;
use App\Services\WwebjsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DopmAutoAlertWa extends Command
{
    protected $signature = 'dopm:auto-alert-wa
                            {--date= : Tanggal (Y-m-d), default hari ini WITA}
                            {--dry-run : Simulasi tanpa mengirim WA}
                            {--test-phone= : Mode testing - kirim semua WA ke nomor ini saja (contoh: 081919898656)}
                            {--limit=0 : Batas jumlah WA yang dikirim (0 = unlimited)}';

    protected $description = 'Kirim auto alert WA via whatsapp-web.js untuk IKK yang belum ada IPK';

    private const TZ = 'Asia/Makassar';

    private ?string $testPhone = null;
    private int $limit = 0;
    private int $totalSent = 0;
    private WwebjsService $waService;

    public function handle(): int
    {
        $dateOpt = $this->option('date');
        $dryRun = (bool) $this->option('dry-run');
        $testPhoneOpt = $this->option('test-phone');
        $this->limit = (int) $this->option('limit');
        $this->totalSent = 0;
        $tz = self::TZ;

        $date = $dateOpt !== null && $dateOpt !== ''
            ? $dateOpt
            : Carbon::now($tz)->toDateString();

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('Format tanggal harus Y-m-d.');
            return self::FAILURE;
        }

        if ($testPhoneOpt !== null && $testPhoneOpt !== '') {
            $this->testPhone = $this->normalizePhone($testPhoneOpt);
            if ($this->testPhone === '') {
                $this->error('Nomor test tidak valid.');
                return self::FAILURE;
            }
        }

        $this->info('[' . Carbon::now($tz)->format('Y-m-d H:i:s') . '] Mulai auto alert WA untuk tanggal: ' . $date);

        if ($dryRun) {
            $this->warn('Mode DRY-RUN aktif - tidak ada WA yang akan dikirim.');
        }

        if ($this->testPhone !== null) {
            $this->warn("Mode TESTING aktif - semua WA akan dikirim ke: {$this->testPhone}");
        }

        if ($this->limit > 0) {
            $this->warn("Limit aktif - maksimal {$this->limit} WA yang akan dikirim.");
        }

        $this->waService = new WwebjsService();
        if (! $this->waService->isReady()) {
            $this->error('WA Service belum siap. Pastikan Node.js service sudah jalan dan scan QR.');
            $this->info('Jalankan: cd wa-service && npm install && node index.js');
            return self::FAILURE;
        }
        $this->info("Provider: whatsapp-web.js");

        try {
            $alerts = $this->getUnnotifiedAlerts($date);

            if ($alerts->isEmpty()) {
                $this->info('Tidak ada alert yang perlu dikirim notifikasi WA.');
                return self::SUCCESS;
            }

            $this->info('Ditemukan ' . $alerts->count() . ' alert yang perlu diproses.');

            $sentCount = 0;
            $failedCount = 0;

            foreach ($alerts as $alert) {
                if ($this->limit > 0 && $this->totalSent >= $this->limit) {
                    $this->warn("Limit tercapai ({$this->limit}), menghentikan proses.");
                    break;
                }

                $result = $this->processAlert($alert, $date, $dryRun);
                $sentCount += $result['sent'];
                $failedCount += $result['failed'];
            }

            $this->info("Selesai. Terkirim: {$sentCount}, Gagal: {$failedCount}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('DopmAutoAlertWa: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return self::FAILURE;
        }
    }

    /**
     * Ambil alert yang belum dinotifikasi dan belum ada IPK.
     */
    private function getUnnotifiedAlerts(string $date)
    {
        $alerts = DopmAlertPerIkk::where('tanggal', $date)
            ->orderBy('kode_ikk')
            ->orderBy('alert_level')
            ->get();

        return $alerts->filter(function ($alert) use ($date) {
            $hasIpk = IpkIkk::where('kode_ikk', $alert->kode_ikk)
                ->whereDate('ts', $date)
                ->exists();

            if ($hasIpk) {
                return false;
            }

            $isIntervensi = DopmAlertIntervensi::where('tanggal', $date)
                ->where('kode_ikk', $alert->kode_ikk)
                ->where('alert_level', $alert->alert_level)
                ->exists();

            if ($isIntervensi) {
                return false;
            }

            return true;
        });
    }

    /**
     * Proses satu alert: ambil PIC dan kirim WA.
     */
    private function processAlert($alert, string $date, bool $dryRun): array
    {
        $sent = 0;
        $failed = 0;

        $snapshot = $alert->ikk_snapshot ?? [];
        $kodeIkk = $alert->kode_ikk;
        $alertLevel = $alert->alert_level;

        $layersToNotify = $this->getLayersToNotify($alertLevel);

        foreach ($layersToNotify as $layer) {
            if ($this->limit > 0 && $this->totalSent >= $this->limit) {
                break;
            }

            $sidKey = "sid_layer_{$layer}";
            $namaKey = "nama_layer_{$layer}";

            $sid = $snapshot[$sidKey] ?? null;
            $nama = $snapshot[$namaKey] ?? null;

            if (empty($sid) && empty($nama)) {
                continue;
            }

            $users = $this->getUsersForLayer($sid, $nama);

            // Cek apakah sudah ada notifikasi untuk IKK + Alert Level + Layer ini
            $alreadyNotifiedForLayer = DopmWaNotificationLog::where('tanggal', $date)
                ->where('kode_ikk', $kodeIkk)
                ->where('alert_level', $alertLevel)
                ->where('layer', $layer)
                ->where('fonnte_status', 'success')
                ->exists();

            if ($alreadyNotifiedForLayer) {
                $this->line("  [SKIP] {$kodeIkk} Alert {$alertLevel} Layer {$layer} (sudah ada notifikasi terkirim)");
                continue;
            }

            // Ambil 1 user pertama yang memiliki nomor telepon valid
            $selectedUser = null;
            foreach ($users as $user) {
                $phone = $this->normalizePhone($user['selular'] ?? '');
                if ($phone !== '') {
                    $selectedUser = $user;
                    break;
                }
            }

            if ($selectedUser === null) {
                $this->line("  [SKIP] {$kodeIkk} Alert {$alertLevel} Layer {$layer} (tidak ada user dengan nomor valid)");
                continue;
            }

            if ($this->limit > 0 && $this->totalSent >= $this->limit) {
                break;
            }

            $originalPhone = $this->normalizePhone($selectedUser['selular'] ?? '');
            $actualPhone = $this->testPhone ?? $originalPhone;

            $message = $this->buildMessage($snapshot, $alertLevel, $layer, $selectedUser['nama'] ?? '', $this->testPhone !== null ? $originalPhone : null);

            if ($dryRun) {
                $targetInfo = $this->testPhone !== null ? "{$this->testPhone} (asli: {$originalPhone})" : $originalPhone;
                $this->info("  [DRY-RUN] {$kodeIkk} Alert {$alertLevel} Layer {$layer} -> {$targetInfo} ({$selectedUser['nama']})");
                $sent++;
                $this->totalSent++;
                continue;
            }

            $result = $this->waService->sendMessage($actualPhone, $message);

            DopmWaNotificationLog::create([
                'tanggal' => $date,
                'kode_ikk' => $kodeIkk,
                'alert_level' => $alertLevel,
                'layer' => $layer,
                'phone_number' => $this->testPhone !== null ? "TEST:{$this->testPhone}|ORIG:{$originalPhone}" : $originalPhone,
                'recipient_name' => $selectedUser['nama'] ?? null,
                'recipient_sid' => $selectedUser['username'] ?? null,
                'message' => $message,
                'fonnte_status' => $result['status'],
                'fonnte_id' => is_array($result['id']) ? ($result['id'][0] ?? null) : $result['id'],
                'fonnte_response' => json_encode($result['response']),
                'sent_at' => now(),
                'provider' => 'wwebjs',
            ]);

            $targetInfo = $this->testPhone !== null ? "{$actualPhone} (asli: {$originalPhone})" : $actualPhone;

            if ($result['success']) {
                $this->info("  [OK] {$kodeIkk} Alert {$alertLevel} Layer {$layer} -> {$targetInfo} ({$selectedUser['nama']})");
                $sent++;
            } else {
                $this->error("  [FAIL] {$kodeIkk} Alert {$alertLevel} Layer {$layer} -> {$targetInfo}");
                $failed++;
            }

            $this->totalSent++;

            sleep(120); // 2 menit delay antar pengiriman untuk menghindari ban
        }

        return ['sent' => $sent, 'failed' => $failed];
    }

    /**
     * Tentukan layer mana yang harus dinotifikasi berdasarkan alert level.
     * Alert 1 -> Layer 1
     * Alert 2 -> Layer 1, 2
     * Alert 3 -> Layer 1, 2, 3, 4
     */
    private function getLayersToNotify(int $alertLevel): array
    {
        return match ($alertLevel) {
            1 => [1],
            2 => [1, 2],
            3 => [1, 2, 3, 4],
            default => [1],
        };
    }

    /**
     * Ambil user dari vw_user berdasarkan SID atau nama.
     */
    private function getUsersForLayer(?string $sid, ?string $nama): array
    {
        $query = DB::table('vw_user')
            ->where('is_active', 1)
            ->whereNotNull('username')
            ->where('username', '!=', '')
            ->whereNotNull('selular')
            ->where('selular', '!=', '');

        if (! empty($sid)) {
            $sids = array_map('trim', preg_split('/[\s,;]+/', $sid, -1, PREG_SPLIT_NO_EMPTY));
            $sids = array_unique(array_filter($sids));
            if (! empty($sids)) {
                $query->whereIn('username', $sids);
            }
        } elseif (! empty($nama)) {
            $query->where(function ($q) use ($nama) {
                $q->where('nama', 'LIKE', '%' . $nama . '%')
                    ->orWhere('username', 'LIKE', '%' . $nama . '%');
            });
        } else {
            return [];
        }

        return $query->select('id', 'username', 'nama', 'email', 'selular')
            ->limit(20)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->toArray();
    }

    /**
     * Normalize nomor telepon ke format 62xxx.
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-\(\)]+/', '', trim($phone));

        if ($phone === '') {
            return '';
        }

        if (str_starts_with($phone, '+62')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '62')) {
            return $phone;
        }

        if (preg_match('/^8\d{8,12}$/', $phone)) {
            return '62' . $phone;
        }

        return $phone;
    }

    /**
     * Buat pesan WA berdasarkan data alert.
     */
    private function buildMessage(array $snapshot, int $alertLevel, int $layer, string $recipientName, ?string $originalPhoneForTest = null): string
    {
        $kodeIkk = $snapshot['code'] ?? '-';
        $namaPekerjaan = $snapshot['nama_pekerjaan'] ?? '-';
        $tanggalMulai = $snapshot['start_date_tanggal'] ?? '-';
        $jamMulai = $snapshot['start_date_jam'] ?? '';
        $lokasi = $snapshot['location_name'] ?? '-';
        $detailLokasi = $snapshot['location_detail_name'] ?? '-';
        $site = $snapshot['site'] ?? '-';

        $greeting = $recipientName !== '' ? "Yth. {$recipientName}," : 'Yth. Bapak/Ibu,';

        $alertText = match ($alertLevel) {
            1 => 'ALERT 1 (Jam ke-1)',
            2 => 'ALERT 2 (Jam ke-2)',
            3 => 'ALERT 3 (Jam ke-3)',
            default => "ALERT {$alertLevel}",
        };

        $message = '';

        if ($originalPhoneForTest !== null) {
            $message .= "*[MODE TESTING]*\n";
            $message .= "Pesan ini seharusnya dikirim ke: {$originalPhoneForTest}\n";
            $message .= "Penerima asli: {$recipientName}\n";
            $message .= "---\n\n";
        }

        $message .= "{$greeting}\n\n";
        $message .= "*{$alertText} - BELUM ADA IPK*\n\n";
        $message .= "Mohon segera mengisi INSPEKSI PRA KERJA (IPK) untuk pekerjaan berikut:\n\n";
        $message .= "Kode IKK: *{$kodeIkk}*\n";
        $message .= "Nama Pekerjaan: {$namaPekerjaan}\n";
        $message .= "Site: {$site}\n";
        $message .= "Tanggal Mulai: {$tanggalMulai} {$jamMulai}\n";
        $message .= "Lokasi: {$lokasi}\n";
        $message .= "Detail Lokasi: {$detailLokasi}\n\n";
        $message .= "Layer {$layer} yang menerima pesan ini.\n\n";
        $message .= "Silakan akses: https://beikk.beraucoal.co.id/monitoring-ipk\n\n";
        $message .= "Terima kasih.\n";
        $message .= "_Pesan otomatis dari Sistem DOPM_";

        return $message;
    }
}
