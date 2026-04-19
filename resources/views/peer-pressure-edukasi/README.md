# Dokumentasi evaluasi — Dashboard Peer Pressure (`dashboard-peer.blade.php`)

Dokumen ini menjelaskan **makna evaluasi** pada tiap fitur di halaman **Peer Pressure Evaluation** serta **cara data dan UI saling terhubung**. View utama: `resources/views/peer-pressure-edukasi/dashboard-peer.blade.php`.

---

## 1. Konteks halaman

| Item | Keterangan |
|------|------------|
| **Route** | `peer-pressure-edukasi.dashboard-peer` → `PeerPressureEdukasiController@dashboard` |
| **URL contoh** | `/peer-pressure-edukasi/dashboard-peer` (opsional query `?year=&month=` untuk filter periode) |
| **Controller** | `app/Http/Controllers/PeerPressureEdukasiController.php` method `dashboard()` |
| **Periode** | **Seluruh data** jika `year`+`month` tidak diisi; **satu bulan kalender** jika keduanya diisi (filter berdasarkan **tanggal temuan** kejadian peer pressure, konsisten dengan chart trend). |

Data awal di-render server-side; setelah pengguna memilih periode di popup dan menekan **Terapkan**, metrik di-refresh lewat endpoint JSON **`peer-pressure-edukasi.dashboard.weekly-trend`** (chart + KPI + insight + breakdown deviasi, dll.).

---

## 2. Baris KPI (empat kartu utama)

Setiap kartu menyajikan **indikator evaluasi** ringkas; beberapa membuka **modal detail**.

### 2.1 Total Deviasi Pelanggaran

| Aspek | Penjelasan |
|-------|------------|
| **Apa dievaluasi** | Agregat jumlah kejadian menurut **tiga jalur sumber**: BeRecord (PSPP/Golden/insiden), Validasi TBC blindspot, Speak Up Fatigue (tidak speak up), sesuai breakdown modal. |
| **Tampilan** | Satu angka total; klik membuka modal **Statistik kategori deviasi** (`peer-deviation-category-modal`). |
| **Data backend** | `GetPeerPressureDeviationModalBreakdownAction` → di view sebagai `$deviationModalBreakdown`. |
| **Detail dinamis** | Tab per kategori; daftar di-fetch lewat route `peer-pressure-edukasi.dashboard.deviation-modal-detail`. |

### 2.2 Pelaksanaan Peer Pressure

| Aspek | Penjelasan |
|-------|------------|
| **Apa dievaluasi** | Volume kejadian peer pressure + **trend** (bulan vs bulan lalu, atau jendela 30 hari vs 30 hari sebelumnya jika mode “seluruh data”). |
| **Tampilan** | Total kejadian + label trend; klik membuka modal **Pelaksanaan Peer Pressure** (`peer-pelaksanaan-detail-modal`). |
| **Data backend** | `GetPeerPressureDashboardKpiStatsAction`, `GetPeerPressureDashboardWeeklyTrendAction`. |
| **Isi modal** | Tab **sudah** vs **belum** pelaksanaan (status mengandung CLOSED/SELESAI vs tidak), agregat kelompok kerja, **SLA** (selisih hari tanggal temuan → tanggal pelaksanaan, per sumber BeRecord/blindspot/fatigue, grafik Chart.js), **matriks gap pelaksanaan vs kepatuhan** (bubble Chart.js per kelompok kerja; endpoint `peer-pressure-edukasi.dashboard.gap-matrix`), tabel kejadian paginasi. |

### 2.3 Pelaksanaan Comply

| Aspek | Penjelasan |
|-------|------------|
| **Apa dievaluasi** | Persentase kejadian **yang memenuhi aturan “comply”** untuk **lima kategori deviasi terlacak** (bukan “Lainnya”), dengan aturan berbeda untuk jalur BeRecord vs non-BeRecord (mis. blindspot/fatigue cukup status selesai; PSPP/Golden/insiden juga mempertimbangkan hubungan BeRecord). |
| **Tampilan** | Persentase + pembilang/penyebut (mis. `x/y` kejadian); klik membuka modal **Pelaksanaan Comply** (`peer-compliance-detail-modal`). |
| **Data backend** | `GetPeerPressureDashboardKpiStatsAction` (`computePelaksanaanCompliance`, `PelaksanaanComplianceEvaluator`). |
| **Detail dinamis** | Breakdown per baris di-fetch lewat `peer-pressure-edukasi.dashboard.compliance-breakdown`. |

### 2.4 Evaluasi Kelompok Kerja

| Aspek | Penjelasan |
|-------|------------|
| **Apa dievaluasi** | **Tingkat penyelesaian** global: kejadian dengan status pelaksanaan mengandung CLOSED/SELESAI dibanding total kejadian pada periode yang sama (**completion rate**). Ada **delta** vs periode pembanding (bulan lalu / 30 hari). |
| **Tampilan** | Persentase + bar progress + delta; klik membuka modal **Evaluasi Kelompok Kerja** (`peer-kk-eval-modal`). |
| **Data backend** | Sama dengan KPI: `completion_rate`, `completion_rate_delta_pp`, serta `pelaksanaan_kelompok_kerja_rows` (maks. 15 kelompok terbanyak). |
| **Logika modal** | **Kelompok “jalan”**: ≥50% kejadian di kelompok itu berstatus selesai; **“tidak jalan”**: sisanya. Persentase besar di atas = proporsi kelompok (dari daftar tersebut) yang “jalan”. |

---

## 3. Ringkasan evaluasi data & modal detail evaluasi

| Fitur | Lokasi di UI | Evaluasi apa |
|-------|----------------|--------------|
| **Ringkasan evaluasi data** | Panel teks/tabel di bawah KPI | Narasi aturan + agregat singkat (periode sama dengan filter chart). |
| **Lihat detail evaluasi** | Tombol → modal `peer-evaluation-summary-modal` | Ringkasan terstruktur dari `GetPeerPressureDashboardEvaluationSummaryAction` (`$evaluationSummary`). |

---

## 4. Insight cards (empat kartu visual)

Data dari **`GetPeerPressureDashboardInsightCardsAction`** (`$insightCards`), misalnya:

- **Deviasi** — distribusi kategori (donut / conic gradient).
- **Compliance visual** — indikator agregat (beberapa metrik persentase + representasi visual).
- Kartu lain sesuai struktur yang dikembalikan action (peer pressure, pelanggar, dll.).

Di-refresh bersama KPI saat **Terapkan** periode (`renderInsightCards` di JavaScript).

---

## 5. Trend pelanggaran (chart)

| Aspek | Penjelasan |
|-------|------------|
| **Evaluasi** | Tren volume kejadian peer pressure per **minggu** (dalam bulan dipilih) atau per **bulan** (mode agregat bulanan), dengan batang bertumpuk per **kategori deviasi**. |
| **Backend** | `GetPeerPressureDashboardWeeklyTrendAction` → `$weeklyTrend`. |
| **Frontend** | `renderWeeklyChart(wt)` — membangun batang HTML, bukan file chart terpisah. |

---

## 6. Highlight Issue & Rekomendasi

| Aspek | Penjelasan |
|-------|------------|
| **Evaluasi** | Narasi **issue + rekomendasi** berdasarkan agregat dashboard (AI/fallback). |
| **Endpoint** | `peer-pressure-edukasi.dashboard.highlight-issue-recommendation` |
| **Backend** | `GeneratePeerPressureDashboardHighlightIssueRecommendationAction` (dipanggil dari controller). |

---

## 7. Bagian tambahan (safety / site / profiling)

Halaman ini juga menampilkan evaluasi dari **JSON statis** (BeReport, TBC, area, Golden Rules, dll.) dan **profiling pelanggar** dengan modal terpisah:

- **Profiling** — fetch `peer-pressure-edukasi.dashboard.pelanggar-profiling`.
- Kartu/konten lain mengikuti pola: data dari controller (`peer*EvalFromJson`, dll.) + interaksi di Blade/JS.

---

## 8. Ringkasan backend (action utama)

| Action | Peran |
|--------|--------|
| `GetPeerPressureDashboardKpiStatsAction` | KPI: total, completion, comply, pelaksanaan, kelompok kerja, SLA, dll. |
| `GetPeerPressureDashboardGapMatrixAction` | Matriks gap: sumbu X = % pelaksanaan selesai per kelompok, sumbu Y = % comply (kategori terlacak); kuadran 🟢🟡🔴🔵; maks. 15 kelompok terbanyak. |
| `GetPeerPressureDashboardWeeklyTrendAction` | Data chart trend + metadata periode. |
| `GetPeerPressureDashboardInsightCardsAction` | Insight cards. |
| `GetPeerPressureDashboardEvaluationSummaryAction` | Ringkasan untuk modal evaluasi. |
| `GetPeerPressureDeviationModalBreakdownAction` | Angka kartu + konteks modal deviasi. |
| `PeerPressureResourcesDataAiSummaryService` | Ringkasan sumber daya / AI terkait KPI (`peerResourcesAiSummary`). |

---

## 9. Alur refresh periode (ringkas)

1. Pengguna membuka modal **Pilih periode chart** → memilih tahun/bulan atau **seluruh data** → **Terapkan**.
2. `fetch` ke **`weekly-trend`** dengan query `year`/`month` sesuai pilihan.
3. Response memuat `kpi`, `evaluation_summary`, `insight_cards`, `deviation_modal_breakdown`, `weekly trend` fields, dll.
4. JavaScript memanggil **`renderKpi`**, **`renderWeeklyChart`**, **`renderEvaluationSummary`**, **`renderInsightCards`**, **`fillDeviationModalBreakdownCards`**, serta sinkronisasi modal (`syncPelaksanaanModalFromKpi`, `syncComplianceModalFromKpi`, `syncKkEvalModalFromKpi`, dll.).

---

## 10. File terkait

| File | Keterangan |
|------|------------|
| `resources/views/peer-pressure-edukasi/dashboard-peer.blade.php` | View utama (HTML + JS besar). |
| `app/Http/Controllers/PeerPressureEdukasiController.php` | `dashboard()`, `weeklyTrendData()`, endpoint JSON lain. |
| `routes/web.php` | Prefix route `peer-pressure-edukasi/dashboard/...` |

---

*Dokumen ini menggambarkan perilaku sesuai struktur kode saat penulisan; jika logika bisnis di action berubah, sesuaikan penjelasan di bagian angka/definisi “selesai” / “comply” / “SLA” dengan implementasi terbaru.*
