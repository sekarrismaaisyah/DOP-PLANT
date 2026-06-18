@extends('PembatasanLV.layouts.app')

@section('title', 'Tutorial Upload Evidence — Fatigue Management GMO')

@push('head')
@include('fatigue-management.partials.styles')
@endpush

@section('content')
@php
   $timezone = $uploadPageContext['timezone'] ?? 'Asia/Makassar';
   $isoWeek = $filters['isoWeek'] ?? date('W');
   $year = $filters['year'] ?? date('Y');
   $isPartnerLocked = (bool) ($partnerAccess['locked'] ?? false);
@endphp

<div class="fm-mon fm-tutorial-full -mt-2 space-y-6">

   {{-- Header --}}
   <header class="pb-5 border-b border-outline-variant/30">
      <nav class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.08em] text-on-surface-variant mb-2.5">
         <span>Fatigue Management GMO</span>
         <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
         <a href="{{ route('fatigue-management.upload') }}" class="hover:text-primary transition-colors">Upload Evidence</a>
         <span class="material-symbols-outlined text-[13px] opacity-60">chevron_right</span>
         <span class="text-primary">Tutorial</span>
      </nav>
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
         <div>
            <h1 class="font-headline font-extrabold text-3xl text-on-background tracking-tight">Tutorial Upload Evidence</h1>
            <p class="mt-1.5 text-sm text-on-surface-variant max-w-2xl">
               Panduan lengkap mengunggah bukti pelaksanaan program Fatigue Management GMO per minggu ISO.
               Ikuti langkah berikut dari awal hingga file tersimpan.
            </p>
         </div>
         <a href="{{ route('fatigue-management.upload', request()->only(['year', 'iso_week', 'partner'])) }}" class="fm-action-btn fm-action-btn--primary px-4 py-2.5 text-sm shrink-0">
            <span class="material-symbols-outlined text-lg">upload_file</span>
            Ke Halaman Upload
         </a>
      </div>
   </header>

   {{-- Ringkasan --}}
   <section class="fm-mon-card p-5 fm-tutorial-full__hero">
      <div class="flex flex-col md:flex-row md:items-center gap-4">
         <div class="fm-tutorial-full__hero-icon">
            <span class="material-symbols-outlined text-4xl text-primary">cloud_upload</span>
         </div>
         <div class="flex-1">
            <h2 class="font-headline font-bold text-lg text-on-background">Apa yang diupload?</h2>
            <p class="text-sm text-on-surface-variant mt-1 leading-relaxed">
               Setiap program fatigue memiliki <strong>slot waktu</strong> sesuai frekuensinya (Shift, Harian, atau Mingguan).
               Upload file evidence — form pemeriksaan, foto, log, rekap — pada slot yang <strong>aktif</strong> untuk minggu
               <strong>{{ $isoWeek }} {{ $year }}</strong>.
            </p>
         </div>
         <dl class="fm-tutorial-full__meta-grid shrink-0">
            <div>
               <dt>Timezone</dt>
               <dd>{{ $timezone }}</dd>
            </div>
            <div>
               <dt>Max file</dt>
               <dd>10 MB</dd>
            </div>
            <div>
               <dt>Format</dt>
               <dd>PDF, IMG, Office, ZIP</dd>
            </div>
         </dl>
      </div>
   </section>

   {{-- Alur 5 langkah --}}
   <section class="fm-mon-card overflow-hidden">
      <div class="px-5 py-4 border-b border-outline-variant/15 bg-[#fafbfc]">
         <h2 class="font-headline font-bold text-lg text-on-background flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">route</span>
            Alur Upload (5 Langkah)
         </h2>
      </div>
      <div class="p-5 space-y-0">
         <article class="fm-tutorial-full__step">
            <div class="fm-tutorial-full__step-badge">1</div>
            <div class="fm-tutorial-full__step-content">
               <h3>Atur filter Tahun &amp; Minggu ISO</h3>
               <p>
                  Di bagian atas halaman upload, pilih <strong>Tahun</strong> dan <strong>Minggu</strong> (format ISO, contoh: W25),
                  lalu klik tombol <strong>Terapkan</strong>. Daftar program akan menyesuaikan minggu yang dipilih.
               </p>
               @if($isPartnerLocked)
               <div class="fm-tutorial-full__callout fm-tutorial-full__callout--info">
                  <span class="material-symbols-outlined">business</span>
                  Akun mitra (<strong>{{ $partnerAccess['partner_key'] ?? '' }}</strong>) hanya menampilkan program perusahaan Anda — filter Mitra tidak perlu diubah.
               </div>
               @else
               <p class="text-xs text-on-surface-variant mt-2">Opsional: filter <strong>Mitra</strong> atau <strong>Tipe Program</strong> untuk mempersempit daftar.</p>
               @endif
               <div class="fm-tutorial-full__mock fm-tutorial-full__mock--filter">
                  <div class="fm-tutorial-full__mock-label">Contoh filter</div>
                  <div class="flex flex-wrap gap-2 items-end">
                     <div class="fm-tutorial-full__mock-field"><span>Tahun</span><strong>{{ $year }}</strong></div>
                     <div class="fm-tutorial-full__mock-field"><span>Minggu</span><strong>{{ $isoWeek }}</strong></div>
                     <div class="fm-tutorial-full__mock-btn">Terapkan</div>
                  </div>
               </div>
            </div>
         </article>

         <article class="fm-tutorial-full__step">
            <div class="fm-tutorial-full__step-badge">2</div>
            <div class="fm-tutorial-full__step-content">
               <h3>Cari program di tabel</h3>
               <p>
                  Program dikelompokkan dalam tiga section: <strong>Shift</strong>, <strong>Harian</strong>, dan <strong>Mingguan</strong>.
                  Setiap baris menampilkan nama program, mitra, frekuensi, dan progress slot (contoh: <code>1/2</code>).
               </p>
               <table class="fm-tutorial-full__table">
                  <thead>
                     <tr>
                        <th>No</th>
                        <th>Tipe</th>
                        <th>Program</th>
                        <th>Frekuensi</th>
                        <th>Progress</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>6</td>
                        <td><span class="fm-type-pill fm-type-pill--mandatory">M</span></td>
                        <td>Fatigue check / Fit to Work awal shift</td>
                        <td>Harian</td>
                        <td><span class="fm-status fm-status--blue">0/7</span></td>
                     </tr>
                     <tr>
                        <td>10</td>
                        <td><span class="fm-type-pill fm-type-pill--mandatory">M</span></td>
                        <td>Fatigue check in-shift jam kritis</td>
                        <td>Shift</td>
                        <td><span class="fm-status fm-status--gray">0/14</span></td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </article>

         <article class="fm-tutorial-full__step">
            <div class="fm-tutorial-full__step-badge">3</div>
            <div class="fm-tutorial-full__step-content">
               <h3>Klik slot upload yang aktif</h3>
               <p>
                  Di kolom <strong>Slot Upload</strong>, klik tombol slot. Hanya slot yang <strong>biru</strong> (ikon upload) yang bisa diklik saat jadwal aktif.
               </p>
               <div class="fm-tutorial-slot-legend fm-tutorial-full__slot-demo">
                  <span class="fm-slot-btn fm-slot-btn--active fm-tutorial-slot-demo">
                     <span class="fm-slot-btn__label">Sen · Shift 1</span>
                     <span class="fm-slot-btn__window">06:00–18:00</span>
                     <span class="material-symbols-outlined fm-slot-btn__icon">upload</span>
                  </span>
                  <span class="fm-slot-btn fm-slot-btn--done fm-tutorial-slot-demo">
                     <span class="fm-slot-btn__label">Sel · Shift 2</span>
                     <span class="material-symbols-outlined fm-slot-btn__icon">check_circle</span>
                  </span>
                  <span class="fm-slot-btn fm-slot-btn--locked fm-tutorial-slot-demo">
                     <span class="fm-slot-btn__label">Rab · Shift 1</span>
                     <span class="material-symbols-outlined fm-slot-btn__icon">lock</span>
                  </span>
               </div>
               <table class="fm-tutorial-full__table fm-tutorial-full__table--compact mt-4">
                  <thead>
                     <tr><th>Warna</th><th>Status</th><th>Aksi</th></tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td><span class="fm-tutorial-full__dot fm-tutorial-full__dot--blue"></span> Biru</td>
                        <td>Slot aktif — siap upload</td>
                        <td>Klik → modal upload terbuka</td>
                     </tr>
                     <tr>
                        <td><span class="fm-tutorial-full__dot fm-tutorial-full__dot--green"></span> Hijau</td>
                        <td>Sudah ada evidence</td>
                        <td>Klik untuk ganti file · ikon attach untuk download</td>
                     </tr>
                     <tr>
                        <td><span class="fm-tutorial-full__dot fm-tutorial-full__dot--gray"></span> Abu + gembok</td>
                        <td>Belum waktunya</td>
                        <td>Tunggu shift/hari yang sesuai</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </article>

         <article class="fm-tutorial-full__step">
            <div class="fm-tutorial-full__step-badge">4</div>
            <div class="fm-tutorial-full__step-content">
               <h3>Isi form &amp; simpan evidence</h3>
               <p>Modal upload akan muncul. Lengkapi form berikut lalu klik <strong>Simpan Evidence</strong>.</p>
               <div class="fm-tutorial-full__mock fm-tutorial-full__mock--modal">
                  <div class="fm-tutorial-full__mock-modal-header">
                     <div>
                        <p class="text-[10px] font-bold uppercase text-primary">Upload Evidence</p>
                        <p class="font-bold text-sm">PAMA · Mandatory (M)</p>
                        <p class="text-xs text-on-surface-variant">Fatigue check / Fit to Work awal shift</p>
                        <span class="inline-block mt-1 text-[11px] font-bold bg-primary/10 text-primary px-2 py-0.5 rounded">Slot: Sen · Shift 1</span>
                     </div>
                  </div>
                  <div class="fm-tutorial-full__mock-modal-body space-y-3">
                     <div>
                        <label class="fm-tutorial-full__mock-label-field">File Evidence *</label>
                        <div class="fm-tutorial-full__mock-file">Pilih file… (PDF, JPG, XLSX, DOC, ZIP)</div>
                     </div>
                     <div>
                        <label class="fm-tutorial-full__mock-label-field">PIC Upload</label>
                        <div class="fm-tutorial-full__mock-input">Nama pengupload</div>
                     </div>
                     <div>
                        <label class="fm-tutorial-full__mock-label-field">Catatan</label>
                        <div class="fm-tutorial-full__mock-textarea">Keterangan evidence…</div>
                     </div>
                     <div class="flex justify-end gap-2 pt-2">
                        <span class="fm-tutorial-full__mock-btn fm-tutorial-full__mock-btn--ghost">Batal</span>
                        <span class="fm-tutorial-full__mock-btn">Simpan Evidence</span>
                     </div>
                  </div>
               </div>
               <table class="fm-tutorial-full__table fm-tutorial-full__table--compact mt-4">
                  <thead>
                     <tr><th>Field</th><th>Wajib?</th><th>Keterangan</th></tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>File Evidence</td>
                        <td><strong>Ya</strong></td>
                        <td>PDF, JPG, PNG, XLSX, XLS, DOC, DOCX, ZIP — maks. 10 MB</td>
                     </tr>
                     <tr>
                        <td>PIC Upload</td>
                        <td>Tidak</td>
                        <td>Nama orang yang mengupload</td>
                     </tr>
                     <tr>
                        <td>Catatan</td>
                        <td>Tidak</td>
                        <td>Keterangan tambahan evidence</td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </article>

         <article class="fm-tutorial-full__step fm-tutorial-full__step--last">
            <div class="fm-tutorial-full__step-badge">5</div>
            <div class="fm-tutorial-full__step-content">
               <h3>Verifikasi progress</h3>
               <p>
                  Setelah berhasil, halaman akan refresh dan slot berubah <strong>hijau</strong> dengan ikon centang.
                  Program dianggap <strong>checklist lengkap</strong> jika semua slot frekuensi pada minggu {{ $isoWeek }} sudah terupload.
               </p>
               <div class="fm-tutorial-full__callout fm-tutorial-full__callout--success">
                  <span class="material-symbols-outlined">check_circle</span>
                  Contoh progress lengkap: <strong>7/7</strong> (harian) atau <strong>14/14</strong> (shift 2×/hari × 7 hari).
               </div>
            </div>
         </article>
      </div>
   </section>

   {{-- Aturan frekuensi --}}
   <section class="fm-mon-card overflow-hidden">
      <div class="px-5 py-4 border-b border-outline-variant/15 bg-[#fafbfc]">
         <h2 class="font-headline font-bold text-lg text-on-background flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">schedule</span>
            Aturan Jadwal Slot per Frekuensi
         </h2>
      </div>
      <div class="p-5">
         <table class="fm-tutorial-full__table">
            <thead>
               <tr>
                  <th>Grup</th>
                  <th>Frekuensi Program</th>
                  <th>Jendela Upload</th>
                  <th>Kapan Slot Muncul</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td><strong>Shift</strong></td>
                  <td>2× per hari (Shiftly)</td>
                  <td>Shift 1: <strong>06:00–18:00</strong><br>Shift 2: <strong>18:00–06:00</strong></td>
                  <td>Hanya shift yang sedang berjalan pada hari berjalan</td>
               </tr>
               <tr>
                  <td><strong>Harian</strong></td>
                  <td>1× per hari (Daily / Awal Shift)</td>
                  <td>00:00–23:59 hari tersebut</td>
                  <td>Hanya slot <strong>hari ini</strong> yang aktif</td>
               </tr>
               <tr>
                  <td><strong>Mingguan</strong></td>
                  <td>1× atau N× per minggu (Weekly)</td>
                  <td>Senin–Minggu (minggu ISO)</td>
                  <td>Semua slot terbuka sepanjang minggu berjalan</td>
               </tr>
            </tbody>
         </table>

         <h3 class="font-bold text-sm text-on-background mt-6 mb-3">Status minggu ISO</h3>
         <div class="fm-tutorial-rules__grid">
            <div class="fm-tutorial-rules__item">
               <span class="material-symbols-outlined text-slate-500">history</span>
               <div>
                  <strong>Minggu lampau</strong>
                  <p>Semua slot terbuka untuk <strong>pelengkapan</strong> evidence yang belum diupload.</p>
               </div>
            </div>
            <div class="fm-tutorial-rules__item">
               <span class="material-symbols-outlined text-emerald-600">play_circle</span>
               <div>
                  <strong>Minggu berjalan</strong>
                  <p>Slot mengikuti <strong>hari &amp; jam server</strong> ({{ $timezone }}).</p>
               </div>
            </div>
            <div class="fm-tutorial-rules__item">
               <span class="material-symbols-outlined text-amber-600">block</span>
               <div>
                  <strong>Minggu mendatang</strong>
                  <p>Upload <strong>belum dibuka</strong> — tunggu minggu dimulai.</p>
               </div>
            </div>
         </div>
      </div>
   </section>

   {{-- FAQ --}}
   <section class="fm-mon-card overflow-hidden">
      <div class="px-5 py-4 border-b border-outline-variant/15 bg-[#fafbfc]">
         <h2 class="font-headline font-bold text-lg text-on-background flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">help</span>
            Pertanyaan Umum
         </h2>
      </div>
      <div class="p-5 divide-y divide-outline-variant/15">
         <details class="fm-tutorial-full__faq">
            <summary>Slot tidak bisa diklik (abu-abu / gembok)?</summary>
            <p>Slot belum aktif. Untuk program Shift, pastikan Anda upload pada jam shift yang berjalan. Untuk Harian, hanya hari ini. Untuk Mingguan, pastikan minggu ISO sudah dimulai.</p>
         </details>
         <details class="fm-tutorial-full__faq">
            <summary>Upload gagal / file ditolak?</summary>
            <p>Pastikan format file didukung (PDF, JPG, PNG, Excel, Word, ZIP) dan ukuran tidak melebihi 10 MB. Coba kompres file atau gunakan format PDF.</p>
         </details>
         <details class="fm-tutorial-full__faq">
            <summary>Bisa ganti file setelah upload?</summary>
            <p>Ya. Klik slot hijau yang sudah terisi — modal upload terbuka kembali untuk mengganti evidence pada slot yang sama.</p>
         </details>
         <details class="fm-tutorial-full__faq">
            <summary>Hanya melihat program perusahaan sendiri?</summary>
            <p>Sistem mendeteksi mitra dari awalan email atau nama akun (contoh: pama@gmail.com → PAMA). Hubungi admin GMO jika perusahaan tidak terdeteksi benar.</p>
         </details>
         <details class="fm-tutorial-full__faq">
            <summary>Evidence apa yang harus diupload?</summary>
            <p>Sesuai program: form fatigue check, foto campaign, log sobriety/tensi, rekap harian, form coaching, foto door to door, dll. — dokumen yang membuktikan program benar-benar dijalankan pada slot waktu tersebut.</p>
         </details>
      </div>
   </section>

   {{-- Footer CTA --}}
   <footer class="fm-mon-card p-5 text-center border-dashed">
      <p class="text-sm text-on-surface-variant mb-4">Siap upload? Kembali ke halaman upload dan ikuti 5 langkah di atas.</p>
      <a href="{{ route('fatigue-management.upload', request()->only(['year', 'iso_week', 'partner'])) }}" class="fm-action-btn fm-action-btn--primary px-6 py-2.5 text-sm inline-flex">
         <span class="material-symbols-outlined text-lg">upload_file</span>
         Buka Halaman Upload
      </a>
   </footer>

</div>
@endsection
