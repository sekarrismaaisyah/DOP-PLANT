<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Services\QwenAIService;
use Illuminate\Support\Facades\Log;

/**
 * Menyusun ringkasan "Highlight Issue & Rekomendasi" dari snapshot data dashboard Peer Pressure
 * menggunakan AI (Gemini via QwenAIService), dengan fallback deterministik jika AI gagal.
 */
final class GeneratePeerPressureDashboardHighlightIssueRecommendationAction
{
    public function __construct(
        private readonly GetPeerPressureDashboardKpiStatsAction $kpiStats,
        private readonly GetPeerPressureDashboardWeeklyTrendAction $weeklyTrend,
        private readonly GetPeerPressureDashboardEvaluationSummaryAction $evaluationSummary,
        private readonly GetPeerPressureDashboardInsightCardsAction $insightCards,
        private readonly GetPeerPressureDashboardComplianceBreakdownAction $complianceBreakdown,
    ) {}

    /**
     * @return array{
     *   rows: list<array{judul: string, issue: string, rekomendasi: string}>,
     *   generated_at: string,
     *   ai_used: bool,
     *   period_label: string
     * }
     */
    public function __invoke(?int $year = null, ?int $month = null): array
    {
        $kpi = ($this->kpiStats)($year, $month);
        $wt = ($this->weeklyTrend)($year, $month);
        $es = ($this->evaluationSummary)($year, $month);
        $ic = ($this->insightCards)($year, $month);
        $cb = ($this->complianceBreakdown)($year, $month, 1, 10);

        $periodLabel = $cb['period_caption'] ?? ($es['repeat_period_caption'] ?? 'Seluruh data');

        $snapshot = [
            'period_label' => $periodLabel,
            'kpi' => $kpi,
            'weekly_trend' => $this->simplifyWeeklyTrend($wt),
            'evaluation_summary' => $this->simplifyEvaluationSummary($es),
            'insight_cards' => $this->simplifyInsightCards($ic),
            'compliance' => $this->simplifyCompliance($cb),
        ];

        $jsonPayload = json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        if ($jsonPayload === false) {
            $jsonPayload = '{}';
        }

        $prompt = <<<PROMPT
Anda adalah analis K3 / manajemen risiko di tambang. Berikut DATA JSON aktual dashboard Peer Pressure (satu sumber kebenaran — jangan mengada-ngada fakta di luar data ini).

DATA:
{$jsonPayload}

TUGAS:
Buat 5–7 baris laporan bergaya "Highlight Issue & Rekomendasi" (mirip ringkasan eksekutif).
Untuk setiap baris:
- "judul": judul tema singkat (beberapa kata, Bahasa Indonesia).
- "issue": 1–4 kalimat narasi deskriptif yang MERANGKUM temuan dari data (sebut angka, persentase, nama lokasi/pelanggar jika ada di data). Bahasa profesional dan menarik dibaca.
- "rekomendasi": 1–3 kalimat tindakan konkret yang berpasangan dengan issue baris yang sama.

Liputi secara vertikal alur dashboard: volume/tren, evaluasi aturan (baris evaluasi), deviasi & lokasi, comply/BeRecord, profiling pelanggar berulang — sesuai apa yang terlihat di data.

FORMAT OUTPUT — HANYA JSON array valid (tanpa markdown, tanpa teks lain):
[
  {"judul":"...","issue":"...","rekomendasi":"..."}
]
PROMPT;

        $aiUsed = false;
        $rows = [];

        try {
            $aiService = new QwenAIService();
            $raw = $aiService->chat($prompt, []);
            $text = $this->extractChatText($raw);
            if ($text !== '') {
                $parsed = $this->parseHighlightRows($text);
                if (count($parsed) > 0) {
                    $rows = $parsed;
                    $aiUsed = true;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Peer Pressure highlight AI gagal', [
                'message' => $e->getMessage(),
            ]);
        }

        if ($rows === []) {
            $rows = $this->fallbackRows($snapshot);
        }

        return [
            'rows' => $rows,
            'generated_at' => now()->format('d M Y, H:i'),
            'ai_used' => $aiUsed,
            'period_label' => $periodLabel,
        ];
    }

    /**
     * @param array<string, mixed> $wt
     * @return array<string, mixed>
     */
    private function simplifyWeeklyTrend(array $wt): array
    {
        $weeks = [];
        foreach (array_slice($wt['weeks'] ?? [], 0, 32) as $w) {
            $weeks[] = [
                'label' => $w['label'] ?? '',
                'count' => (int) ($w['count'] ?? 0),
            ];
        }

        return [
            'period_caption' => $wt['period_caption'] ?? '',
            'chart_granularity' => $wt['chart_granularity'] ?? '',
            'max_count' => (int) ($wt['max_count'] ?? 0),
            'avg_count' => (float) ($wt['avg_count'] ?? 0),
            'weeks' => $weeks,
        ];
    }

    /**
     * @param array<string, mixed> $es
     * @return array<string, mixed>
     */
    private function simplifyEvaluationSummary(array $es): array
    {
        $rows = [];
        foreach ($es['rows'] ?? [] as $row) {
            $rows[] = [
                'metric' => $row['metric'] ?? '',
                'description' => $row['description'] ?? '',
                'status' => $row['status'] ?? '',
                'action_threshold' => $row['action_threshold'] ?? '',
                'detail_bullets' => array_slice($row['detail_bullets'] ?? [], 0, 6),
            ];
        }

        return [
            'narrative' => $es['narrative'] ?? '',
            'total_kejadian' => (int) ($es['total_kejadian'] ?? 0),
            'rows' => $rows,
        ];
    }

    /**
     * @param array<string, mixed> $ic
     * @return array<string, mixed>
     */
    private function simplifyInsightCards(array $ic): array
    {
        $devTop = [];
        foreach (array_slice($ic['deviation']['categories'] ?? [], 0, 10) as $c) {
            $devTop[] = [
                'kategori_deviasi' => $c['kategori_deviasi'] ?? '',
                'jumlah' => (int) ($c['jumlah'] ?? 0),
                'pct' => (float) ($c['pct'] ?? 0),
            ];
        }

        $locs = [];
        foreach (array_slice($ic['locations'] ?? [], 0, 12) as $loc) {
            $locs[] = [
                'name' => $loc['name'] ?? '',
                'count' => (int) ($loc['count'] ?? 0),
            ];
        }

        $prof = [];
        foreach (array_slice($ic['profiling_pelanggar'] ?? [], 0, 10) as $p) {
            $prof[] = [
                'nama' => $p['nama'] ?? '',
                'sid' => $p['sid'] ?? '',
                'kasus' => (int) ($p['kasus'] ?? 0),
                'insiden_share_pct' => (float) ($p['insiden_share_pct'] ?? 0),
            ];
        }

        return [
            'deviation_total' => (int) (($ic['deviation']['total'] ?? 0)),
            'deviation_top' => $devTop,
            'compliance_radar' => $ic['compliance'] ?? [],
            'locations_top' => $locs,
            'profiling_top' => $prof,
        ];
    }

    /**
     * @param array<string, mixed> $cb
     * @return array<string, mixed>
     */
    private function simplifyCompliance(array $cb): array
    {
        $recs = [];
        foreach (array_slice($cb['recommendations'] ?? [], 0, 10) as $r) {
            $recs[] = [
                'code' => $r['code'] ?? '',
                'judul' => $r['judul'] ?? '',
                'jumlah' => (int) ($r['jumlah'] ?? 0),
            ];
        }

        return [
            'peer_pressure_compliance_pct' => (float) ($cb['peer_pressure_compliance_pct'] ?? 0),
            'peer_pressure_compliance_total' => (int) ($cb['peer_pressure_compliance_total'] ?? 0),
            'peer_pressure_compliance_comply' => (int) ($cb['peer_pressure_compliance_comply'] ?? 0),
            'recommendation_groups' => $recs,
        ];
    }

    private function extractChatText(mixed $raw): string
    {
        if (is_string($raw)) {
            return $raw;
        }
        if (is_array($raw) && isset($raw['message']) && is_string($raw['message'])) {
            return $raw['message'];
        }

        return '';
    }

    /**
     * @return list<array{judul: string, issue: string, rekomendasi: string}>
     */
    private function parseHighlightRows(string $text): array
    {
        $cleaned = preg_replace('/```json\s*/i', '', $text);
        $cleaned = preg_replace('/```\s*/', '', (string) $cleaned);
        $cleaned = trim((string) $cleaned);

        $decoded = null;
        if (preg_match('/\[[\s\S]*\]/', $cleaned, $m)) {
            $decoded = json_decode($m[0], true);
        } else {
            $decoded = json_decode($cleaned, true);
        }

        if (! is_array($decoded)) {
            return [];
        }

        $out = [];
        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }
            $judul = trim((string) ($row['judul'] ?? ''));
            $issue = trim((string) ($row['issue'] ?? ''));
            $rek = trim((string) ($row['rekomendasi'] ?? $row['recommendation'] ?? ''));

            if ($judul === '' && $issue === '') {
                continue;
            }
            if ($judul === '') {
                $judul = 'Temuan';
            }
            if ($rek === '') {
                $rek = 'Lakukan review bersama PIC edukasi dan tutup gap sesuai kategori deviasi.';
            }

            $out[] = [
                'judul' => $judul,
                'issue' => $issue !== '' ? $issue : 'Lihat narasi evaluasi dan metrik KPI pada periode ini.',
                'rekomendasi' => $rek,
            ];
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $snapshot
     * @return list<array{judul: string, issue: string, rekomendasi: string}>
     */
    private function fallbackRows(array $snapshot): array
    {
        $kpi = $snapshot['kpi'] ?? [];
        $es = $snapshot['evaluation_summary'] ?? [];
        $ic = $snapshot['insight_cards'] ?? [];
        $cb = $snapshot['compliance'] ?? [];
        $wt = $snapshot['weekly_trend'] ?? [];
        $period = $snapshot['period_label'] ?? 'periode ini';

        $narrative = trim((string) ($es['narrative'] ?? ''));
        if (mb_strlen($narrative) > 420) {
            $narrative = mb_substr($narrative, 0, 417).'…';
        }

        $dev1 = $ic['deviation_top'][0] ?? null;
        $loc1 = $ic['locations_top'][0] ?? null;
        $prof1 = $ic['profiling_top'][0] ?? null;

        $rows = [];

        $rows[] = [
            'judul' => 'Kinerja pelaksanaan & comply',
            'issue' => sprintf(
                'Pada %s tercatat %d kejadian dengan tingkat penyelesaian (CLOSED/SELESAI) sekitar %.1f%%. Pelaksanaan comply %.1f%% (%d dari %d kejadian terlacak). %s',
                $period,
                (int) ($kpi['total_cases'] ?? 0),
                (float) ($kpi['completion_rate'] ?? 0),
                (float) ($kpi['peer_pressure_compliance_pct'] ?? 0),
                (int) ($kpi['peer_pressure_compliance_comply'] ?? 0),
                (int) ($kpi['peer_pressure_compliance_total'] ?? 0),
                $narrative !== '' ? 'Ringkasan sistem: '.$narrative : 'Periksa detail kartu evaluasi untuk konteks aturan.'
            ),
            'rekomendasi' => 'Prioritaskan penutupan status pelaksanaan, lengkapi id BeRecord untuk kategori yang membutuhkannya, dan koordinasikan tindak lanjut lintas departemen.',
        ];

        $sumWeeks = 0;
        foreach ($wt['weeks'] ?? [] as $w) {
            $sumWeeks += (int) ($w['count'] ?? 0);
        }
        $rows[] = [
            'judul' => 'Pola tren pelanggaran',
            'issue' => sprintf(
                'Agregasi trend menunjukkan variasi per %s (maks %d kejadian per periode waktu pada chart). Total titik waktu terhitung: %d kejadian.',
                ($wt['chart_granularity'] ?? '') === 'week' ? 'minggu dalam bulan' : 'bulan',
                (int) ($wt['max_count'] ?? 0),
                $sumWeeks
            ),
            'rekomendasi' => 'Alihkan sumber daya edukasi ke periode atau lokasi dengan puncak kejadian; gunakan chart untuk briefing mingguan pengawas.',
        ];

        if (is_array($dev1)) {
            $rows[] = [
                'judul' => 'Dominasi kategori deviasi',
                'issue' => sprintf(
                    'Dari total deviasi terklasifikasi, kategori "%s" menyumbang %d kejadian (sekitar %.1f%% dari insiden).',
                    (string) ($dev1['kategori_deviasi'] ?? '—'),
                    (int) ($dev1['jumlah'] ?? 0),
                    (float) ($dev1['pct'] ?? 0)
                ),
                'rekomendasi' => 'Susun materi peer pressure khusus untuk kategori tersebut dan pantau penurunan insiden bulan berikutnya.',
            ];
        }

        if (is_array($loc1)) {
            $rows[] = [
                'judul' => 'Fokus lokasi',
                'issue' => sprintf(
                    'Lokasi "%s" memuncak dengan %d kejadian dalam cakupan data — indikasi perlu penguatan pengawasan lapangan.',
                    (string) ($loc1['name'] ?? '—'),
                    (int) ($loc1['count'] ?? 0)
                ),
                'rekomendasi' => 'Tambahkan observasi terarah dan toolbox talk di area tersebut; libatkan pemimpin edukasi setempat.',
            ];
        }

        if (is_array($prof1)) {
            $rows[] = [
                'judul' => 'Pelanggar berulang',
                'issue' => sprintf(
                    '%s (%s) tercatat %d kasus dengan porsi %.1f%% terhadap insiden pada periode yang sama.',
                    (string) ($prof1['nama'] ?? '—'),
                    (string) ($prof1['sid'] ?? '—'),
                    (int) ($prof1['kasus'] ?? 0),
                    (float) ($prof1['insiden_share_pct'] ?? 0)
                ),
                'rekomendasi' => 'Lakukan coaching lanjutan, dokumentasi tindak lanjut HR/line manager, dan verifikasi efektivitas edukasi sebelumnya.',
            ];
        }

        $topRec = $cb['recommendation_groups'][0] ?? null;
        if (is_array($topRec) && ((int) ($topRec['jumlah'] ?? 0)) > 0) {
            $rows[] = [
                'judul' => 'Gap Pelaksanaan Comply',
                'issue' => sprintf(
                    'Kelompok temuan "%s" (%s) masih memiliki %d kejadian yang perlu perbaikan alur administrasi/proses.',
                    (string) ($topRec['judul'] ?? '—'),
                    (string) ($topRec['code'] ?? ''),
                    (int) ($topRec['jumlah'] ?? 0)
                ),
                'rekomendasi' => 'Ikuti petunjuk rekomendasi per kode di modul comply: lengkapi field yang kurang dan tutup siklus verifikasi.',
            ];
        }

        return $rows;
    }
}
