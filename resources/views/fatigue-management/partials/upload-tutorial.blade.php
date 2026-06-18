{{-- Tutorial upload evidence — panduan proses upload saja --}}
@php
   $isoWeekTutorial = $isoWeek ?? ($filters['isoWeek'] ?? '');
@endphp

<details class="fm-upload-tutorial group" id="fm-upload-tutorial">
   <summary class="fm-upload-tutorial__toggle">
      <span class="fm-upload-tutorial__toggle-icon">
         <span class="material-symbols-outlined text-xl">school</span>
      </span>
      <span class="flex-1 min-w-0 text-left">
         <span class="block text-[10px] font-bold uppercase tracking-wider text-primary">Panduan</span>
         <span class="block text-sm font-bold text-on-background mt-0.5">Tutorial Upload Evidence</span>
         <span class="block text-xs text-on-surface-variant font-normal mt-0.5 group-open:hidden">
            Klik untuk ringkasan singkat ·
            <a href="{{ route('fatigue-management.upload.tutorial', request()->only(['year', 'iso_week', 'partner'])) }}" class="text-primary font-semibold hover:underline" onclick="event.stopPropagation()">buka panduan lengkap</a>
         </span>
      </span>
      <span class="material-symbols-outlined text-on-surface-variant transition-transform group-open:rotate-180">expand_more</span>
   </summary>

   <div class="fm-upload-tutorial__body">
      <ol class="fm-tutorial-steps">
         <li class="fm-tutorial-step">
            <span class="fm-tutorial-step__num">1</span>
            <div>
               <p class="fm-tutorial-step__title">Atur filter minggu</p>
               <p class="fm-tutorial-step__desc">
                  Pilih <strong>Tahun</strong> dan <strong>Minggu ISO</strong> (contoh: {{ $isoWeekTutorial ?: 'W25' }}),
                  lalu klik <strong>Terapkan</strong>.
                  @if($isPartnerLocked ?? false)
                  Akun mitra otomatis menampilkan program perusahaan Anda saja.
                  @else
                  Filter <strong>Mitra</strong> opsional untuk mempersempit daftar program.
                  @endif
               </p>
            </div>
         </li>
         <li class="fm-tutorial-step">
            <span class="fm-tutorial-step__num">2</span>
            <div>
               <p class="fm-tutorial-step__title">Temukan program di tabel</p>
               <p class="fm-tutorial-step__desc">
                  Program dikelompokkan per frekuensi: <strong>Shift</strong>, <strong>Harian</strong>, dan <strong>Mingguan</strong>.
                  Cari baris program yang akan diupload, perhatikan kolom <em>Progress</em> (contoh: 0/2 slot).
               </p>
            </div>
         </li>
         <li class="fm-tutorial-step">
            <span class="fm-tutorial-step__num">3</span>
            <div>
               <p class="fm-tutorial-step__title">Klik slot yang aktif</p>
               <p class="fm-tutorial-step__desc">
                  Di kolom <strong>Slot Upload</strong>, klik tombol berwarna biru (ikon upload).
                  Slot abu-abu dengan gembok belum bisa diupload — tunggu jadwal shift/hari yang sesuai.
               </p>
               <div class="fm-tutorial-slot-legend">
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
               <p class="fm-tutorial-legend-labels">
                  <span><strong class="text-primary">Biru</strong> = siap upload</span>
                  <span><strong class="text-emerald-700">Hijau</strong> = sudah upload (klik untuk ganti)</span>
                  <span><strong class="text-slate-500">Abu</strong> = belum waktunya</span>
               </p>
            </div>
         </li>
         <li class="fm-tutorial-step">
            <span class="fm-tutorial-step__num">4</span>
            <div>
               <p class="fm-tutorial-step__title">Isi form &amp; unggah file</p>
               <p class="fm-tutorial-step__desc">
                  Modal upload akan terbuka. Pilih <strong>File Evidence</strong> (wajib),
                  isi <strong>PIC Upload</strong> dan <strong>Catatan</strong> bila perlu, lalu klik <strong>Simpan Evidence</strong>.
               </p>
               <ul class="fm-tutorial-file-list">
                  <li>Format: PDF, JPG, PNG, Excel (.xlsx/.xls), Word (.doc/.docx), ZIP</li>
                  <li>Ukuran maksimal <strong>10 MB</strong> per file</li>
                  <li>Evidence = form pemeriksaan, foto kegiatan, log/rekap, atau dokumen pendukung program</li>
               </ul>
            </div>
         </li>
         <li class="fm-tutorial-step">
            <span class="fm-tutorial-step__num">5</span>
            <div>
               <p class="fm-tutorial-step__title">Cek progress checklist</p>
               <p class="fm-tutorial-step__desc">
                  Setelah berhasil, slot berubah hijau dengan centang.
                  Program dianggap <strong>lengkap</strong> jika semua slot frekuensinya sudah terupload pada minggu {{ $isoWeekTutorial ?: 'terpilih' }}.
               </p>
            </div>
         </li>
      </ol>

      <div class="fm-tutorial-rules">
         <p class="text-[10px] font-bold uppercase tracking-wider text-primary mb-2">Aturan jadwal slot</p>
         <div class="fm-tutorial-rules__grid">
            <div class="fm-tutorial-rules__item">
               <span class="material-symbols-outlined text-amber-600">schedule</span>
               <div>
                  <strong>Shift (2×/hari)</strong>
                  <p>Shift 1: <strong>06:00–18:00</strong> · Shift 2: <strong>18:00–06:00</strong>. Hanya shift yang sedang berjalan yang bisa diupload.</p>
               </div>
            </div>
            <div class="fm-tutorial-rules__item">
               <span class="material-symbols-outlined text-emerald-600">today</span>
               <div>
                  <strong>Harian</strong>
                  <p>Satu slot per hari. Hanya <strong>hari ini</strong> yang muncul dan bisa diupload.</p>
               </div>
            </div>
            <div class="fm-tutorial-rules__item">
               <span class="material-symbols-outlined text-primary">date_range</span>
               <div>
                  <strong>Mingguan</strong>
                  <p>Bebas upload <strong>Senin–Minggu</strong> dalam minggu ISO yang dipilih.</p>
               </div>
            </div>
         </div>
         <p class="fm-tutorial-rules__note">
            <span class="material-symbols-outlined text-sm align-middle">info</span>
            Minggu lampau: semua slot terbuka untuk pelengkapan · Minggu mendatang: upload belum dibuka · Waktu mengikuti server ({{ $uploadPageContext['timezone'] ?? 'Asia/Makassar' }}).
         </p>
      </div>
   </div>
</details>
