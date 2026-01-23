<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Dokumentasi Sistem Hazard Detection - Berau Coal</title>

  <style>
    /* ===========================
       PRINT-FIRST, FORMAL REPORT
       - Monochrome (black + grayscale)
       - Serif font (research-like)
       - Clean margins, header/footer, page breaks
       =========================== */

    * { margin: 0; padding: 0; box-sizing: border-box; }

    :root{
      --ink: #000;
      --gray-1: #111;
      --gray-2: #333;
      --gray-3: #666;
      --line: #000;
      --soft: #f2f2f2; /* grayscale only */
      --soft-2: #fafafa;
    }

    @page {
      size: A4;
      margin: 2.5cm 2.2cm 2.8cm 2.2cm;
    }

    html, body { background: #fff; color: var(--ink); }
    body{
      font-family: "Times New Roman", Times, serif;
      font-size: 12pt;
      line-height: 1.45;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Container */
    .document-container{
      max-width: 900px;
      margin: 0 auto;
      padding: 0;
    }

    /* ====== PRINT HEADER / FOOTER (works in browser print-to-pdf) ====== */
    .print-header,
    .print-footer{
      display: none;
    }

    @media print{
      .print-header,
      .print-footer{
        display: block;
        position: fixed;
        left: 0;
        right: 0;
        color: var(--ink);
        font-size: 10pt;
      }

      .print-header{
        top: 0;
        padding: 0.6cm 2.2cm 0.3cm 2.2cm;
        border-bottom: 1px solid var(--line);
        background: #fff;
      }

      .print-footer{
        bottom: 0;
        padding: 0.3cm 2.2cm 0.6cm 2.2cm;
        border-top: 1px solid var(--line);
        background: #fff;
      }

      /* Create space so content doesn't overlap fixed header/footer */
      body{
        padding-top: 2.0cm;
        padding-bottom: 2.0cm;
      }
    }

    .header-row{
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      gap: 16px;
      white-space: nowrap;
    }
    .header-title{
      font-weight: 700;
      letter-spacing: 0.2px;
    }
    .header-meta{
      font-size: 10pt;
    }

    .footer-row{
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      gap: 16px;
      font-size: 10pt;
    }

    /* ====== TYPOGRAPHY ====== */
    h1, h2, h3, h4{
      color: var(--ink);
      font-weight: 700;
    }

    h1{
      font-size: 16pt;
      margin: 18pt 0 10pt 0;
      padding-bottom: 6pt;
      border-bottom: 2px solid var(--line);
      page-break-before: always;
    }
    h1:first-of-type{ page-break-before: auto; }

    h2{
      font-size: 13pt;
      margin: 14pt 0 8pt 0;
    }

    h3{
      font-size: 12pt;
      margin: 12pt 0 6pt 0;
    }

    h4{
      font-size: 11pt;
      margin: 10pt 0 6pt 0;
      font-style: italic;
    }

    p{
      margin: 8pt 0;
      text-align: justify;
      hyphens: auto;
    }

    /* Lists */
    ul, ol{
      margin: 8pt 0 10pt 18pt;
    }
    li{ margin: 4pt 0; }

    /* Section spacing */
    .section{
      margin: 10pt 0 14pt 0;
      page-break-inside: avoid;
    }

    /* ====== COVER PAGE ====== */
    .cover-page{
      page-break-after: always;
      padding: 52mm 18mm 40mm 18mm;
      border: 1.5px solid var(--line);
      text-align: center;
    }

    .cover-page h1{
      border: none;
      margin: 0 0 10pt 0;
      padding: 0;
      font-size: 20pt;
      letter-spacing: 0.5px;
      page-break-before: auto;
    }

    .cover-page h2{
      font-size: 14pt;
      margin: 6pt 0;
      font-weight: 700;
    }

    .cover-meta{
      margin-top: 22pt;
      font-size: 11pt;
      color: var(--gray-2);
    }
    .cover-meta p{
      text-align: center;
      margin: 4pt 0;
    }

    .cover-divider{
      margin: 18pt auto 14pt auto;
      width: 70%;
      border-top: 1px solid var(--line);
    }

    .cover-subtitle{
      margin-top: 8pt;
      font-size: 11pt;
      color: var(--gray-2);
    }
    .cover-subtitle p{
      text-align: center;
      margin: 2pt 0;
    }

    /* ====== TOC ====== */
    .toc{
      border: 1px solid var(--line);
      padding: 12pt 14pt;
      margin: 12pt 0 18pt 0;
      page-break-inside: avoid;
    }
    .toc h2{
      margin-top: 0;
      margin-bottom: 8pt;
      font-size: 13pt;
    }
    .toc ul{
      list-style: none;
      margin: 0;
      padding: 0;
    }
    .toc li{
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding: 4pt 0;
      border-bottom: 1px dotted #999; /* grayscale */
    }
    .toc li:last-child{ border-bottom: none; }
    .toc a{
      color: var(--ink);
      text-decoration: none;
    }
    .toc a:hover{ text-decoration: underline; }

    /* ====== TABLES ====== */
    table{
      width: 100%;
      border-collapse: collapse;
      margin: 10pt 0 12pt 0;
      page-break-inside: avoid;
      font-size: 11pt;
    }
    th, td{
      border: 1px solid var(--line);
      padding: 7pt 8pt;
      vertical-align: top;
    }
    th{
      font-weight: 700;
      text-align: left;
      background: var(--soft-2); /* grayscale only */
    }

    /* ====== CODE / PRE ====== */
    .code-block{
      border: 1px solid var(--line);
      background: var(--soft-2);
      padding: 10pt;
      margin: 10pt 0 12pt 0;
      font-family: "Courier New", Courier, monospace;
      font-size: 10pt;
      line-height: 1.35;
      overflow-x: auto;
      white-space: pre;
      page-break-inside: avoid;
    }

    /* ====== CALLOUT BOXES (monochrome) ====== */
    .info-box, .warning-box, .success-box{
      border: 1px solid var(--line);
      padding: 10pt 12pt;
      margin: 10pt 0 12pt 0;
      background: #fff;
      page-break-inside: avoid;
    }
    .info-box strong,
    .warning-box strong,
    .success-box strong{
      display: inline-block;
      margin-bottom: 4pt;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      font-size: 10pt;
    }

    /* ====== DIAGRAM BOX (ASCII diagrams) ====== */
    .diagram-box{
      border: 1px solid var(--line);
      padding: 12pt;
      margin: 10pt 0 12pt 0;
      background: #fff;
      page-break-inside: avoid;
    }
    .diagram-box h3{
      margin-top: 0;
      margin-bottom: 8pt;
      font-size: 12pt;
    }
    .diagram-box pre{
      font-family: "Courier New", Courier, monospace;
      font-size: 10pt;
      line-height: 1.35;
      white-space: pre;
      overflow-x: auto;
    }

    /* ====== BADGES (keep monochrome) ====== */
    .badge{
      display: inline-block;
      padding: 2pt 6pt;
      border: 1px solid var(--line);
      border-radius: 2pt;
      font-size: 9.5pt;
      font-weight: 700;
    }

    /* ====== FOOTER SECTION (in-document) ====== */
    .footer{
      margin-top: 18pt;
      padding-top: 10pt;
      border-top: 1px solid var(--line);
      font-size: 10.5pt;
      color: var(--gray-2);
      text-align: center;
      page-break-inside: avoid;
    }
    .footer p{ text-align: center; margin: 3pt 0; }

    /* ====== PRINT BEHAVIOR ====== */
    @media print{
      a{ color: var(--ink); text-decoration: none; }
      h1, h2, h3{ page-break-after: avoid; }
      table, .code-block, .diagram-box, .info-box, .warning-box, .success-box{
        break-inside: avoid;
      }
    }
  </style>
</head>

<body>
  <!-- PRINT HEADER/FOOTER -->
  <div class="print-header">
    <div class="header-row">
      <div class="header-title">Hazard In Motion — Dokumentasi Teknis</div>
      <div class="header-meta">Berau Coal · Versi 1.0</div>
    </div>
  </div>

  <div class="print-footer">
    <div class="footer-row">
      <div>Dokumentasi Sistem — Berau Coal</div>
      <div>Halaman</div>
    </div>
  </div>

  <div class="document-container">
    <!-- COVER PAGE -->
    <div class="cover-page">
      <h1>DOKUMENTASI SISTEM</h1>
      <h2>Hazard In Motion</h2>
      <h2>Berau Coal</h2>

      <div class="cover-divider"></div>

      <div class="cover-subtitle">
        <p>Functional Specification Document (FSD)</p>
        <p>Technical Specification Document (TSD)</p>
        <p>Data Dictionary</p>
        <p>Operational Handbook</p>
        <p>System Architecture Diagram</p>
      </div>

      <div class="cover-meta">
        <p><strong>Versi:</strong> 1.0</p>
        <p><strong>Tanggal:</strong> <strong>21 Jan 2026</strong></p>
      </div>
    </div>

    <!-- TABLE OF CONTENTS -->
    <div class="toc">
      <h2>Daftar Isi</h2>
      <ul>
        <li><a href="#fsd">1. Functional Specification Document (FSD)</a></li>
        <li><a href="#tsd">2. Technical Specification Document (TSD)</a></li>
        <li><a href="#data-dictionary">3. Data Dictionary</a></li>
        <li><a href="#operational-handbook">4. Operational Handbook</a></li>
        <li><a href="#system-architecture">5. System Architecture Diagram</a></li>
      </ul>
    </div>

    <!-- FSD SECTION -->
    <h1 id="fsd">1. FUNCTIONAL SPECIFICATION DOCUMENT (FSD)</h1>

    <div class="section">
      <h2>1.1. Pendahuluan</h2>
      <p>Sistem Hazard Detection &amp; Monitoring adalah aplikasi web berbasis Laravel yang dirancang untuk memantau dan mendeteksi potensi bahaya di area operasional Berau Coal. Sistem ini mengintegrasikan data CCTV, GPS tracking, area kerja geofencing, dan insiden untuk memberikan dashboard monitoring yang komprehensif.</p>
    </div>

    <div class="section">
      <h2>1.2. Tujuan Sistem</h2>
      <ul>
        <li>Memantau kondisi CCTV secara real-time di seluruh area operasional</li>
        <li>Mendeteksi potensi bahaya melalui analisis spasial dan temporal</li>
        <li>Mengelola area kerja dan area CCTV melalui geofencing</li>
        <li>Melacak lokasi karyawan dan unit kendaraan melalui GPS</li>
        <li>Mengelola task list untuk kesiapan operasional dan intervensi</li>
        <li>Menyediakan dashboard monitoring untuk Control Room</li>
      </ul>
    </div>

    <div class="section">
      <h2>1.3. Stakeholder</h2>
      <table>
        <tr>
          <th>Stakeholder</th>
          <th>Peran</th>
          <th>Akses</th>
        </tr>
        <tr>
          <td>Admin Hazard Motion</td>
          <td>Administrator sistem dengan akses penuh</td>
          <td>Semua fitur</td>
        </tr>
        <tr>
          <td>Pengawas Control Room</td>
          <td>Memantau CCTV dan area kerja</td>
          <td>Dashboard Readiness, Smart Alert Maps, Task List</td>
        </tr>
      </table>
    </div>

    <div class="section">
      <h2>1.4. Fitur Utama</h2>

      <h3>1.4.1. Dashboard Readiness (mapBase.blade.php)</h3>
      <p>Dashboard utama untuk monitoring kesiapan operasional dengan peta interaktif.</p>
      <ul>
        <li><strong>Peta Interaktif:</strong> Menampilkan peta dengan berbagai layer (CCTV, SAP, GR, Insiden, Unit, GPS)</li>
        <li><strong>Filter Data:</strong> Filter berdasarkan Site, Company, Control Room, dan kategori lainnya</li>
        <li><strong>Sidebar Panel:</strong> Panel informasi untuk CCTV, SAP, Insiden, Unit, GPS, Control Room, dan PJA</li>
        <li><strong>Layer Toggle:</strong> Mengaktifkan/nonaktifkan layer peta sesuai kebutuhan</li>
        <li><strong>WMS Integration:</strong> Integrasi dengan Web Map Service untuk basemap</li>
        <li><strong>Geofencing:</strong> Visualisasi area kerja dan area CCTV</li>
        <li><strong>Risk Matrix:</strong> Analisis risiko berdasarkan lokasi</li>
        <li><strong>Statistics:</strong> Statistik kesiapan alat, coverage CCTV, dan indikator lainnya</li>
      </ul>

      <h3>1.4.2. Smart Alert Maps (fullMaps.blade.php)</h3>
      <p>Peta fullscreen dengan interface Google Maps style untuk monitoring real-time.</p>
      <ul>
        <li><strong>Fullscreen Map:</strong> Peta fullscreen dengan kontrol navigasi</li>
        <li><strong>Search Functionality:</strong> Pencarian CCTV, lokasi, dan data lainnya</li>
        <li><strong>Left Sidebar:</strong> Panel kontrol dengan filter dan informasi</li>
        <li><strong>Right Sidebar:</strong> Panel detail untuk item yang dipilih</li>
        <li><strong>Real-time Updates:</strong> Update data secara real-time</li>
        <li><strong>Alert System:</strong> Sistem alert untuk potensi bahaya</li>
        <li><strong>Control Room Filter:</strong> Filter berdasarkan Control Room yang diawasi</li>
      </ul>

      <h3>1.4.3. Sidebar Navigation (sidebarWmsAdmin.blade.php)</h3>
      <p>Navigasi sidebar untuk akses cepat ke berbagai modul sistem.</p>
      <ul>
        <li><strong>Dashboard:</strong> Akses ke dashboard utama</li>
        <li><strong>CCTV Management:</strong> Manajemen database CCTV, PJA Dedicated, Coverage, dan Control Room</li>
        <li><strong>Spasial:</strong> Manajemen WMS, Area Kerja, Area CCTV, dan Boundary Monitoring</li>
        <li><strong>DOP:</strong> Daily Operation Plan</li>
        <li><strong>Control Room:</strong> Dashboard Readiness dan Smart Alert Maps</li>
        <li><strong>Tasklist Issue:</strong> Task List Readiness dan Task List Operasi</li>
        <li><strong>On Off CCTV:</strong> Kontrol on/off CCTV</li>
      </ul>
    </div>

    <div class="section">
      <h2>1.5. Use Cases</h2>

      <h3>Use Case 1: Monitoring CCTV</h3>
      <table>
        <tr><th>Aspek</th><th>Deskripsi</th></tr>
        <tr><td>Actor</td><td>Pengawas Control Room</td></tr>
        <tr><td>Precondition</td><td>User sudah login dan memiliki akses ke Dashboard Readiness</td></tr>
        <tr>
          <td>Main Flow</td>
          <td>
            1. User membuka Dashboard Readiness<br/>
            2. Sistem menampilkan peta dengan marker CCTV<br/>
            3. User mengklik marker CCTV<br/>
            4. Sistem menampilkan informasi detail CCTV<br/>
            5. User dapat melihat status, kondisi, dan link akses CCTV
          </td>
        </tr>
        <tr><td>Postcondition</td><td>User berhasil melihat informasi CCTV</td></tr>
      </table>

      <h3>Use Case 2: Filter Data Berdasarkan Control Room</h3>
      <table>
        <tr><th>Aspek</th><th>Deskripsi</th></tr>
        <tr><td>Actor</td><td>Pengawas Control Room</td></tr>
        <tr><td>Precondition</td><td>User sudah login dan memiliki Control Room yang diawasi</td></tr>
        <tr>
          <td>Main Flow</td>
          <td>
            1. User membuka Smart Alert Maps<br/>
            2. Sistem otomatis memfilter data berdasarkan Control Room yang diawasi<br/>
            3. User dapat melihat hanya CCTV yang terkait dengan Control Room-nya<br/>
            4. User dapat mengubah filter melalui dropdown Control Room
          </td>
        </tr>
        <tr><td>Postcondition</td><td>Data yang ditampilkan sesuai dengan Control Room yang dipilih</td></tr>
      </table>

      <h3>Use Case 3: Geofencing Area Kerja</h3>
      <table>
        <tr><th>Aspek</th><th>Deskripsi</th></tr>
        <tr><td>Actor</td><td>Admin Hazard Motion</td></tr>
        <tr><td>Precondition</td><td>User memiliki akses admin dan data GeoJSON area kerja tersedia</td></tr>
        <tr>
          <td>Main Flow</td>
          <td>
            1. User membuka modul Spasial &gt; Area Kerja<br/>
            2. User mengupload atau memilih GeoJSON area kerja<br/>
            3. Sistem menampilkan area kerja di peta<br/>
            4. User dapat melihat overlap dengan area CCTV<br/>
            5. Sistem menghitung coverage area kerja oleh CCTV
          </td>
        </tr>
        <tr><td>Postcondition</td><td>Area kerja berhasil ditampilkan dan dianalisis</td></tr>
      </table>
    </div>

    <div class="section">
      <h2>1.6. Requirement Non-Fungsional</h2>
      <ul>
        <li><strong>Performance:</strong> Sistem harus dapat menampilkan peta dengan 100+ marker tanpa lag</li>
        <li><strong>Security:</strong> Autentikasi dan autorisasi berbasis role</li>
        <li><strong>Usability:</strong> Interface yang intuitif dan mudah digunakan</li>
        <li><strong>Compatibility:</strong> Kompatibel dengan browser modern (Chrome, Firefox, Edge)</li>
        <li><strong>Scalability:</strong> Dapat menangani pertumbuhan data CCTV dan pengguna</li>
        <li><strong>Availability:</strong> Sistem harus tersedia 24/7 dengan downtime minimal</li>
      </ul>
    </div>

    <!-- TSD SECTION -->
    <h1 id="tsd">2. TECHNICAL SPECIFICATION DOCUMENT (TSD)</h1>

    <div class="section">
      <h2>2.1. Arsitektur Teknologi</h2>

      <h3>2.1.1. Backend</h3>
      <ul>
        <li><strong>Framework:</strong> Laravel (PHP)</li>
        <li><strong>Database:</strong> MySQL/MariaDB</li>
        <li><strong>ORM:</strong> Eloquent</li>
        <li><strong>API:</strong> RESTful API</li>
      </ul>

      <h3>2.1.2. Frontend</h3>
      <ul>
        <li><strong>Template Engine:</strong> Blade (Laravel)</li>
        <li><strong>JavaScript:</strong> Vanilla JavaScript, jQuery</li>
        <li><strong>Mapping Library:</strong> Leaflet.js</li>
        <li><strong>UI Framework:</strong> Bootstrap 4/5, Material Icons</li>
        <li><strong>Charts:</strong> Chart.js </li>
      </ul>

      <h3>2.1.3. External Services &amp; Data Platform</h3>
      <ul>
        <li><strong>WMS Server:</strong> Web Map Service untuk basemap</li>

        <li><strong>Source Databases (Read/Write Operasional):</strong>
          <ul>
            <li><strong>HSE Automation DB:</strong> sumber data HSE (hazard/observasi/oak)</li>
            <li><strong>BeSIGMA DB:</strong> sumber data operasional (unit, orang)</li>
          </ul>
        </li>

        <li><strong>ClickHouse (Mirrored Analytics Warehouse):</strong> database analitik untuk query cepat &amp; read-intensive (peta, layer, agregasi, historis)</li>
        <li><strong>Mirroring/CDC/ETL Layer:</strong> proses replikasi dari DB sumber ke ClickHouse (CDC/ETL terjadwal; di luar aplikasi Laravel)</li>

        <li><strong>Telegram Bot:</strong> notifikasi alert </li>
        <li><strong>Qwen AI:</strong> AI service untuk analisis </li>
      </ul>
    </div>

    <!-- NEW: DATA MIRRORING STRATEGY -->
    <div class="section">
      <h2>2.1.4. Mekanisme Pengambilan Data (Tanpa Hit Langsung ke Server Sumber)</h2>

      <p>
        Prinsip desain data pada sistem ini adalah <strong>tidak melakukan query langsung (direct hit)</strong> dari aplikasi
        ke database operasional sumber (HSE Automation DB / BeSIGMA DB). Untuk menjaga performa sistem sumber dan menghindari
        beban query peta/analytics, seluruh data yang dibutuhkan untuk monitoring dan analitik
        <strong>di-mirror (direplikasi)</strong> ke <strong>ClickHouse</strong>.
      </p>

      <div class="info-box">
        <strong>Konsep Utama</strong>
        <ul>
          <li><strong>Source DB</strong> tetap menjadi sistem operasional (transaksional).</li>
          <li><strong>ClickHouse</strong> menjadi <strong>read replica / analytics warehouse</strong> untuk peta, layer, agregasi, dan historis.</li>
          <li>Proses <strong>Mirroring/CDC/ETL</strong> berjalan <strong>di luar</strong> Laravel </li>
          <li>Laravel <strong>hanya membaca</strong> dari ClickHouse untuk kebutuhan dashboard/monitoring (read-intensive).</li>
        </ul>
      </div>

      <h3>2.1.4.1. Pola Mirroring (CDC/ETL)</h3>
      <ul>
        <li><strong>CDC (Change Data Capture):</strong> untuk tabel yang berubah sering (posisi unit/orang, event SAP). Target: near real-time.</li>
        <li><strong>Materialized Views (ClickHouse):</strong> untuk mempercepat query peta (agregasi per site/control room, per menit/jam, ring buffer last known position).</li>
      </ul>

      <h3>2.1.4.2. Kapan Mengambil Data (Cadence/Refresh)</h3>
      <p>
        data diambil setiap 1 jam sekali dari data source
      </p>

      <table>
        <tr>
          <th>Domain Data</th>
          <th>Sumber</th>
          <th>Metode Mirroring</th>
          <th>Kapan Diambil (Cadence)</th>
          <th>Tujuan di ClickHouse</th>
          <th>Dipakai oleh Modul</th>
        </tr>

       

        <tr>
          <td><strong>Data Unit (fleet, posisi, status)</strong></td>
          <td>BeSIGMA DB / telematics</td>
          <td>CDC / incremental</td>
          <td>
            <ul>
              <li><strong>Setiap 1 jam</strong> </li>
              <li></li>
            </ul>
          </td>
          <td>fact_unit_position_rt, fact_unit_status, mv_last_unit_position</td>
          <td>Smart Alert Maps, layer Unit, geofencing check</td>
        </tr>

        <tr>
          <td><strong>Data Orang (RFID check-in)</strong></td>
          <td>HSE Automation DB </td>
          <td>CDC / incremental</td>
          <td>
            <ul>
              <li><strong>Setiap 1 jam </strong> </li>
              <li>Event-based untuk melihat kesiapan orang</li>
            </ul>
          </td>
          <td>fact_people_position, fact_rfid_event, mv_last_people_position</td>
          <td>Layer GPS/People, check “in work area”, proximity monitoring</td>
        </tr>


        <tr>
          <td><strong>Hazard/SAP</strong></td>
          <td>HSE Automation DB</td>
          <td>CDC / incremental</td>
          <td>
            <ul>
              <li><strong>Near real-time</strong> untuk event hazard/insiden</li>
              <li>Agregasi per jam/hari untuk trend</li>
            </ul>
          </td>
          <td>fact_hazard_event, fact_incident, dim_gr, mv_risk_hotspot</td>
          <td>Risk layer, alert logic, analitik hotspot</td>
        </tr>
      </table>

      <div class="warning-box">
        <strong>Catatan Implementasi</strong>
        <p>
          Aplikasi Laravel <strong>tidak melakukan query ke Source DB</strong> untuk kebutuhan monitoring. Semua query peta dan analitik
          dibebankan ke ClickHouse. Source DB hanya diakses oleh proses mirroring (CDC/ETL) yang terkontrol.
        </p>
      </div>

      <h3>2.1.4.3. Contoh Kontrak Data (High-Level)</h3>
      <div class="code-block">Sumber (HSE Automation / BeSIGMA)
  └─(CDC/ETL, incremental/batch)→ ClickHouse (analytics warehouse)
      ├─ fact_* (event/position/status)
      ├─ dim_*  (master/reference)
      └─ mv_*   (materialized views: last_position, hotspot, uptime)

Laravel (Read Model)
  └─ Query ClickHouse untuk map layers, agregasi, historis, dan alert context</div>
    </div>

    <div class="section">
      <h2>2.2. Struktur File</h2>

      <h3>2.2.1. Backend Structure</h3>
      <div class="code-block">app/
├── Http/
│   └── Controllers/
│       └── HazardMotion/
│           ├── MapBaseController.php
│           └── fullMapsController.php
├── Models/
│   ├── CctvData.php
│   ├── CctvCoverage.php
│   ├── CctvControlRoomPengawas.php
│   ├── GeojsonArea.php
│   ├── InsidenTabel.php
│   ├── GrTable.php
│   └── ...
└── Services/
    ├── BesigmaDbService.php
    ├── ClickHouseService.php
    └── TelegramBotService.php</div>

      <h3>2.2.2. Frontend Structure</h3>
      <div class="code-block">resources/
└── views/
    ├── layouts/
    │   ├── sidebarWmsAdmin.blade.php
    │   └── masterMotionHazardAdmin.blade.php
    └── HazardMotion/
        ├── mapBase.blade.php
        └── admin/
            └── fullMaps.blade.php</div>

      <h3>2.2.3. Data Mirroring (Out-of-App)</h3>
      <div class="code-block">DataPipeline/
├── cdc/
│   ├── debezium-connectors/          ()
│   └── kafka-topics/                 ()
├── etl/
│   ├── sync_sap_dop_to_clickhouse    (per shift / harian)
│   ├── sync_master_reference         (harian)
│   └── incremental_hazard_incident   (per jam)
└── observability/
    ├── lag_monitoring
    └── data_quality_checks</div>
    </div>

    <div class="section">
      <h2>2.3. Controller Specifications</h2>

      <h3>2.3.1. MapBaseController</h3>
      <table>
        <tr><th>Method</th><th>Route</th><th>Deskripsi</th></tr>
        <tr><td>index()</td><td>GET /maps</td><td>Menampilkan halaman Dashboard Readiness dengan data CCTV, SAP, GR, Insiden, Unit, dan GPS</td></tr>
        <tr><td>getFilteredMapData()</td><td>GET /maps/api/filtered-data</td><td>Mengembalikan data peta yang sudah difilter berdasarkan parameter</td></tr>
        <tr><td>getUserGps()</td><td>GET /maps/api/user-gps</td><td>Mengambil data GPS user untuk ditampilkan di peta</td></tr>
        <tr><td>getWorkAreas()</td><td>GET /maps/api/work-areas</td><td>Mengambil data area kerja dari GeoJSON</td></tr>
        <tr><td>checkGpsInWorkArea()</td><td>POST /maps/api/check-work-area</td><td>Mengecek apakah GPS user berada dalam area kerja</td></tr>
      </table>

      <h3>2.3.2. fullMapsController</h3>
      <table>
        <tr><th>Method</th><th>Route</th><th>Deskripsi</th></tr>
        <tr><td>index()</td><td>GET /fullmaps</td><td>Menampilkan halaman Smart Alert Maps dengan interface Google Maps style</td></tr>
      </table>
    </div>

    <div class="section">
      <h2>2.4. API Endpoints</h2>

      <h3>2.4.1. Map Data API</h3>
      <table>
        <tr><th>Endpoint</th><th>Method</th><th>Parameter</th><th>Response</th></tr>
        <tr><td>/maps/api/filtered-data</td><td>GET</td><td>site, company, control_room, type</td><td>JSON dengan data CCTV, SAP, GR, Insiden, Unit, GPS</td></tr>
        <tr><td>/maps/api/user-gps</td><td>GET</td><td>-</td><td>JSON dengan data GPS user</td></tr>
        <tr><td>/maps/api/work-areas</td><td>GET</td><td>week, year, type</td><td>GeoJSON dengan data area kerja</td></tr>
        <tr><td>/maps/api/check-work-area</td><td>POST</td><td>latitude, longitude</td><td>JSON dengan status apakah GPS dalam area kerja</td></tr>
      </table>
    </div>

    <div class="section">
      <h2>2.5. Database Schema</h2>

      <h3>2.5.1. Tabel Utama</h3>
      <ul>
        <li><strong>cctv_data_bmo2:</strong> Data CCTV dengan koordinat dan informasi detail</li>
        <li><strong>geojson_areas:</strong> Data area kerja dan area CCTV dalam format GeoJSON</li>
        <li><strong>insiden_tabel:</strong> Data insiden/kecelakaan</li>
        <li><strong>gr_table:</strong> Data Golden Rules</li>
        <li><strong>cctv_coverage:</strong> Data coverage CCTV</li>
        <li><strong>cctv_control_room_pengawas:</strong> Relasi pengawas dengan Control Room</li>
      </ul>

      <h3>2.5.2. ClickHouse Schema (Mirroring / Analytics)</h3>
      <ul>
        <li><strong>dim_cctv:</strong> master CCTV (id, lokasi, control room, koordinat, metadata)</li>
        <li><strong>fact_cctv_health:</strong> status/health CCTV time-series (uptime, last_seen, kondisi)</li>
        <li><strong>fact_unit_position_rt:</strong> posisi unit time-series (lat, lon, heading, speed, timestamp)</li>
        <li><strong>fact_people_position:</strong> posisi orang time-series (lat, lon, timestamp, sumber GPS/RFID)</li>
        <li><strong>fact_hazard_event:</strong> event hazard/temuan (waktu, lokasi, kategori, severity)</li>
        <li><strong>fact_incident:</strong> event insiden (waktu, lokasi, kategori, ringkasan)</li>
        <li><strong>mv_last_unit_position:</strong> materialized view posisi terakhir unit untuk rendering peta cepat</li>
        <li><strong>mv_last_people_position:</strong> materialized view posisi terakhir orang untuk rendering peta cepat</li>
        <li><strong>mv_risk_hotspot:</strong> agregasi hotspot risiko per grid/time window</li>
      </ul>
    </div>

    <div class="section">
      <h2>2.6. Security</h2>

      <h3>2.6.1. Authentication</h3>
      <ul>
        <li>Menggunakan Laravel Auth untuk autentikasi</li>
        <li>Session-based authentication</li>
        <li>Password hashing menggunakan bcrypt</li>
      </ul>

      <h3>2.6.2. Authorization</h3>
      <ul>
        <li>Role-based access control (RBAC)</li>
        <li>Middleware untuk proteksi route</li>
        <li>Check role di view: <code>Auth::user()->hasRole('admin-hazard-motion')</code></li>
      </ul>

      <h3>2.6.3. Data Filtering</h3>
      <ul>
        <li>Pengawas hanya melihat data Control Room yang diawasi</li>
        <li>Filter berdasarkan company dan site untuk user tertentu</li>
        <li>Validasi input untuk mencegah SQL injection</li>
      </ul>
    </div>

    <div class="section">
      <h2>2.7. Performance Optimization</h2>
      <ul>
        <li><strong>Lazy Loading:</strong> Layer peta dimuat sesuai kebutuhan</li>
        <li><strong>Data Pagination:</strong> Pagination untuk data besar</li>
        <li><strong>Caching:</strong> Cache untuk data statis</li>
        <li><strong>Database Indexing:</strong> Index pada kolom yang sering di-query</li>
        <li><strong>AJAX Loading:</strong> Load data secara asinkron</li>
      </ul>
    </div>

    <!-- DATA DICTIONARY SECTION -->
    <h1 id="data-dictionary">3. DATA DICTIONARY</h1>

    <div class="section">
      <h2>3.1. Tabel: cctv_data_bmo2</h2>
      <table>
        <tr><th>Kolom</th><th>Tipe Data</th><th>Deskripsi</th><th>Constraint</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary key</td><td>NOT NULL, AUTO_INCREMENT</td></tr>
        <tr><td>site</td><td>VARCHAR</td><td>Nama site lokasi CCTV</td><td>NULL</td></tr>
        <tr><td>perusahaan</td><td>VARCHAR</td><td>Nama perusahaan</td><td>NULL</td></tr>
        <tr><td>no_cctv</td><td>VARCHAR</td><td>Nomor identifikasi CCTV</td><td>NULL</td></tr>
        <tr><td>nama_cctv</td><td>VARCHAR</td><td>Nama CCTV</td><td>NULL</td></tr>
        <tr><td>lokasi_pemasangan</td><td>VARCHAR</td><td>Lokasi pemasangan CCTV</td><td>NULL</td></tr>
        <tr><td>control_room</td><td>VARCHAR</td><td>Control Room yang mengelola CCTV</td><td>NULL</td></tr>
        <tr><td>status</td><td>VARCHAR</td><td>Status CCTV (Aktif/Nonaktif)</td><td>NULL</td></tr>
        <tr><td>kondisi</td><td>VARCHAR</td><td>Kondisi CCTV (Baik/Rusak)</td><td>NULL</td></tr>
        <tr><td>latitude</td><td>DECIMAL(10,8)</td><td>Koordinat latitude</td><td>NULL</td></tr>
        <tr><td>longitude</td><td>DECIMAL(11,8)</td><td>Koordinat longitude</td><td>NULL</td></tr>
        <tr><td>link_akses</td><td>VARCHAR</td><td>URL akses CCTV</td><td>NULL</td></tr>
        <tr><td>fitur_auto_alert</td><td>VARCHAR</td><td>Status fitur auto alert</td><td>NULL</td></tr>
      </table>
    </div>

    <div class="section">
      <h2>3.2. Tabel: geojson_areas</h2>
      <table>
        <tr><th>Kolom</th><th>Tipe Data</th><th>Deskripsi</th><th>Constraint</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary key</td><td>NOT NULL, AUTO_INCREMENT</td></tr>
        <tr><td>name</td><td>VARCHAR</td><td>Nama area</td><td>NULL</td></tr>
        <tr><td>type</td><td>VARCHAR</td><td>Tipe area (area_kerja, area_cctv)</td><td>NULL</td></tr>
        <tr><td>geojson_data</td><td>JSON</td><td>Data GeoJSON polygon/linestring</td><td>NULL</td></tr>
        <tr><td>week</td><td>INT</td><td>Minggu ke-</td><td>NULL</td></tr>
        <tr><td>year</td><td>INT</td><td>Tahun</td><td>NULL</td></tr>
        <tr><td>description</td><td>TEXT</td><td>Deskripsi area</td><td>NULL</td></tr>
      </table>
    </div>

    <div class="section">
      <h2>3.3. Tabel: insiden_tabel</h2>
      <table>
        <tr><th>Kolom</th><th>Tipe Data</th><th>Deskripsi</th><th>Constraint</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary key</td><td>NOT NULL, AUTO_INCREMENT</td></tr>
        <tr><td>no_kecelakaan</td><td>VARCHAR</td><td>Nomor identifikasi kecelakaan</td><td>NULL</td></tr>
        <tr><td>tanggal</td><td>DATE</td><td>Tanggal kejadian</td><td>NULL</td></tr>
        <tr><td>perusahaan</td><td>VARCHAR</td><td>Nama perusahaan</td><td>NULL</td></tr>
        <tr><td>site</td><td>VARCHAR</td><td>Nama site</td><td>NULL</td></tr>
        <tr><td>latitude</td><td>DECIMAL(10,8)</td><td>Koordinat latitude lokasi insiden</td><td>NULL</td></tr>
        <tr><td>longitude</td><td>DECIMAL(11,8)</td><td>Koordinat longitude lokasi insiden</td><td>NULL</td></tr>
        <tr><td>kategori</td><td>VARCHAR</td><td>Kategori insiden</td><td>NULL</td></tr>
        <tr><td>kronologis</td><td>TEXT</td><td>Kronologi kejadian</td><td>NULL</td></tr>
      </table>
    </div>

    <div class="section">
      <h2>3.4. Tabel: gr_table</h2>
      <table>
        <tr><th>Kolom</th><th>Tipe Data</th><th>Deskripsi</th><th>Constraint</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary key</td><td>NOT NULL, AUTO_INCREMENT</td></tr>
        <tr><td>tasklist</td><td>VARCHAR</td><td>Task list terkait</td><td>NULL</td></tr>
        <tr><td>gr</td><td>VARCHAR</td><td>Golden Rule</td><td>NULL</td></tr>
        <tr><td>catatan</td><td>TEXT</td><td>Catatan tambahan</td><td>NULL</td></tr>
      </table>
    </div>

    <div class="section">
      <h2>3.5. Tabel: cctv_control_room_pengawas</h2>
      <table>
        <tr><th>Kolom</th><th>Tipe Data</th><th>Deskripsi</th><th>Constraint</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary key</td><td>NOT NULL, AUTO_INCREMENT</td></tr>
        <tr><td>nama_pengawas</td><td>VARCHAR</td><td>Nama pengawas Control Room</td><td>NULL</td></tr>
        <tr><td>control_room</td><td>VARCHAR</td><td>Nama Control Room yang diawasi</td><td>NULL</td></tr>
      </table>
    </div>

    <!-- OPERATIONAL HANDBOOK SECTION -->
    <h1 id="operational-handbook">4. OPERATIONAL HANDBOOK</h1>

    <div class="section">
      <h2>4.1. Panduan Akses Sistem</h2>

      <h3>4.1.1. Login</h3>
      <ol>
        <li>Buka browser dan akses URL sistem</li>
        <li>Masukkan username dan password</li>
        <li>Klik tombol "Login"</li>
        <li>Sistem akan mengarahkan ke dashboard sesuai role</li>
      </ol>

      <div class="info-box">
        <strong>Info</strong>
        <p>Pastikan browser yang digunakan adalah versi terbaru (Chrome, Firefox, atau Edge) untuk performa optimal.</p>
      </div>

      <h3>4.1.2. Logout</h3>
      <ol>
        <li>Klik menu profil di pojok kanan atas</li>
        <li>Pilih opsi "Logout"</li>
        <li>Konfirmasi logout jika diperlukan</li>
      </ol>
    </div>

    <div class="section">
      <h2>4.2. Panduan Dashboard Readiness</h2>

      <h3>4.2.1. Membuka Dashboard</h3>
      <ol>
        <li>Setelah login, klik menu "Control Room" di sidebar</li>
        <li>Pilih "Dashboard Readiness"</li>
        <li>Tunggu hingga peta dan data dimuat</li>
      </ol>

      <h3>4.2.2. Menggunakan Filter</h3>
      <ol>
        <li>Klik dropdown filter di bagian atas peta</li>
        <li>Pilih filter yang diinginkan (Site, Company, Control Room)</li>
        <li>Data di peta akan otomatis terfilter</li>
        <li>Sidebar panel juga akan menampilkan data yang sudah difilter</li>
      </ol>

      <h3>4.2.3. Mengaktifkan Layer</h3>
      <ol>
        <li>Klik tombol layer toggle di bagian atas peta</li>
        <li>Pilih layer yang ingin ditampilkan (CCTV, SAP, GR, Insiden, Unit, GPS)</li>
        <li>Layer yang aktif akan ditandai dengan status aktif</li>
        <li>Layer yang tidak aktif akan disembunyikan</li>
      </ol>

      <h3>4.2.4. Melihat Detail CCTV</h3>
      <ol>
        <li>Klik marker CCTV di peta</li>
        <li>Popup akan menampilkan informasi dasar CCTV</li>
        <li>Untuk informasi lengkap, buka tab "CCTV" di sidebar panel</li>
        <li>Klik CCTV yang diinginkan untuk melihat detail lengkap</li>
      </ol>

      <h3>4.2.5. Menggunakan Sidebar Panel</h3>
      <ol>
        <li>Sidebar panel berada di sisi kanan peta</li>
        <li>Klik tab untuk melihat kategori data (CCTV, SAP, Insiden, Unit, GPS, Control Room, PJA)</li>
        <li>Gunakan search box untuk mencari data spesifik</li>
        <li>Klik item untuk melihat detail atau zoom ke lokasi</li>
        <li>Gunakan tombol collapse untuk menyembunyikan/menampilkan sidebar</li>
      </ol>
    </div>

    <div class="section">
      <h2>4.3. Panduan Smart Alert Maps</h2>

      <h3>4.3.1. Membuka Smart Alert Maps</h3>
      <ol>
        <li>Klik menu "Control Room" di sidebar</li>
        <li>Pilih "Smart Alert Maps"</li>
        <li>Tunggu hingga peta fullscreen dimuat</li>
      </ol>

      <h3>4.3.2. Menggunakan Search</h3>
      <ol>
        <li>Klik search box di bagian atas</li>
        <li>Ketik nama CCTV, lokasi, atau data lainnya</li>
        <li>Hasil pencarian akan muncul di dropdown</li>
        <li>Klik hasil untuk zoom ke lokasi</li>
      </ol>

      <h3>4.3.3. Menggunakan Left Sidebar</h3>
      <ol>
        <li>Left sidebar berisi filter dan kontrol</li>
        <li>Gunakan filter Control Room untuk memfilter CCTV</li>
        <li>Toggle layer untuk menampilkan/menyembunyikan layer</li>
        <li>Gunakan kontrol zoom dan navigasi</li>
      </ol>

      <h3>4.3.4. Menggunakan Right Sidebar</h3>
      <ol>
        <li>Right sidebar menampilkan detail item yang dipilih</li>
        <li>Klik marker atau item untuk melihat detail</li>
        <li>Informasi lengkap akan ditampilkan di right sidebar</li>
        <li>Gunakan tombol close untuk menutup sidebar</li>
      </ol>
    </div>

    <div class="section">
      <h2>4.4. Troubleshooting</h2>

      <h3>4.4.1. Peta Tidak Muncul</h3>
      <ul>
        <li>Periksa koneksi internet</li>
        <li>Clear cache browser</li>
        <li>Refresh halaman (F5 atau Ctrl+R)</li>
        <li>Periksa console browser untuk error (F12)</li>
      </ul>

      <h3>4.4.2. Data Tidak Terfilter</h3>
      <ul>
        <li>Pastikan filter sudah dipilih dengan benar</li>
        <li>Refresh halaman</li>
        <li>Periksa apakah user memiliki akses ke data tersebut</li>
        <li>Hubungi administrator jika masalah berlanjut</li>
      </ul>

      <h3>4.4.3. Sidebar Tidak Responsif</h3>
      <ul>
        <li>Refresh halaman</li>
        <li>Periksa apakah JavaScript diaktifkan</li>
        <li>Clear cache browser</li>
        <li>Coba browser lain</li>
      </ul>

      <h3>4.4.4. CCTV Tidak Muncul di Peta</h3>
      <ul>
        <li>Pastikan CCTV memiliki koordinat (latitude dan longitude)</li>
        <li>Periksa filter yang aktif</li>
        <li>Pastikan layer CCTV diaktifkan</li>
        <li>Periksa apakah CCTV dalam area yang terlihat di peta</li>
      </ul>
    </div>

    <div class="section">
      <h2>4.5. Best Practices</h2>

      <h3>4.5.1. Performa Optimal</h3>
      <ul>
        <li>Nonaktifkan layer yang tidak diperlukan untuk meningkatkan performa</li>
        <li>Gunakan filter untuk membatasi data yang ditampilkan</li>
        <li>Tutup sidebar jika tidak digunakan</li>
        <li>Refresh halaman secara berkala jika menggunakan sistem dalam waktu lama</li>
      </ul>

      <h3>4.5.2. Keamanan</h3>
      <ul>
        <li>Jangan share kredensial login</li>
        <li>Logout setelah selesai menggunakan sistem</li>
        <li>Jangan akses sistem dari perangkat yang tidak aman</li>
        <li>Laporkan aktivitas mencurigakan ke administrator</li>
      </ul>

      <h3>4.5.3. Penggunaan Data</h3>
      <ul>
        <li>Pastikan data yang digunakan adalah data terbaru</li>
        <li>Verifikasi koordinat CCTV sebelum digunakan untuk analisis</li>
        <li>Gunakan filter yang tepat untuk mendapatkan data yang relevan</li>
        <li>Simpan screenshot atau export data jika diperlukan untuk dokumentasi</li>
      </ul>
    </div>

    <!-- SYSTEM ARCHITECTURE SECTION -->
    <h1 id="system-architecture">5. SYSTEM ARCHITECTURE DIAGRAM</h1>

    <div class="section">
      <h2>5.1. Arsitektur Sistem</h2>

      <div class="diagram-box">
        <h3>High-Level Architecture</h3>
        <pre>┌─────────────────────────────────────────────────────────────┐
│                    CLIENT LAYER (Browser)                   │
├─────────────────────────────────────────────────────────────┤
│  • Dashboard Readiness (mapBase.blade.php)                  │
│  • Smart Alert Maps (fullMaps.blade.php)                    │
│  • Leaflet.js, jQuery, Bootstrap                            │
└───────────────────────┬─────────────────────────────────────┘
                        │ HTTP/HTTPS
                        │ REST API
┌───────────────────────▼─────────────────────────────────────┐
│                 APPLICATION LAYER (Laravel)                 │
├─────────────────────────────────────────────────────────────┤
│  Controllers / Services:                                     │
│  • MapBaseController / fullMapsController                    │
│  • ClickHouseService (READ analytics & map layers)           │
│  • TelegramBotService (optional)                             │
│                                                              │
│  NOTE: Laravel tidak query langsung ke DB sumber operasional │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        │ READ-ONLY (Analytics Queries)
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              CLICKHOUSE (MIRRORED DATA WAREHOUSE)           │
├─────────────────────────────────────────────────────────────┤
│  • fact_unit_position_rt / fact_people_position              │
│  • fact_hazard_event / fact_incident                         │
│  • dim_cctv / fact_cctv_health                                │
│  • materialized views (last_position, hotspot, uptime)        │
└───────────────────────┬─────────────────────────────────────┘
                        │
         ┌──────────────┼─────────────────────────────────────┐
         │              │
┌────────▼────────┐  ┌──▼─────────────────────────────────────┐
│ MIRRORING LAYER  │  │ SOURCE DATABASES (OPERASIONAL)         │
│ (CDC / ETL JOB)  │  │  • HSE Automation DB                   │
│  • CDC Streaming │  │  • BeSIGMA DB / SAP staging            │
│  • Batch ETL     │  │                                       │
└──────────────────┘  └───────────────────────────────────────┘

External:
• WMS Server (Basemap) • Telegram (Notif)</pre>
      </div>
    </div>

    <div class="section">
      <h2>5.2. Data Flow Diagram</h2>

      <div class="diagram-box">
        <h3>Data Flow - Dashboard Readiness</h3>
        <pre>User Request
    │
    ▼
MapBaseController::index()
    │
    ├──► Query ClickHouse: dim_cctv + fact_cctv_health (filtered by role/control room)
    ├──► Query ClickHouse: mv_last_unit_position (unit terakhir untuk rendering cepat)
    ├──► Query ClickHouse: mv_last_people_position / fact_rfid_event (orang/GPS)
    ├──► Query ClickHouse: fact_hazard_event / fact_incident (risiko & historis)
    ├──► Query MySQL : konfigurasi admin (mapping control room, metadata non-analytics)
    │
    ▼
View: mapBase.blade.php
    │
    ├──► Render Map (Leaflet)
    ├──► Render Sidebar Panel
    ├──► Initialize JavaScript
    │
    ▼
Frontend JavaScript
    │
    ├──► Load Layers via AJAX (READ dari ClickHouse)
    ├──► Render markers/polygons
    ├──► Filter Change → AJAX → Query ClickHouse → Update map
    └──► Alert Context → Query agregasi/last_position/hotspot dari ClickHouse</pre>
      </div>
    </div>

    <div class="section">
      <h2>5.3. Component Diagram</h2>

      <div class="diagram-box">
        <h3>Frontend Components</h3>
        <pre>mapBase.blade.php
├── Map Container
│   ├── Leaflet Map Instance
│   ├── WMS Layer
│   ├── CCTV Layer (Markers)
│   ├── SAP Layer (Markers)
│   ├── GR Layer (Markers)
│   ├── Insiden Layer (Markers)
│   ├── Unit Layer (Markers)
│   ├── GPS Layer (Markers)
│   └── Geofencing Layer (Polygons)
│
├── Filter Controls
│   ├── Site Filter
│   ├── Company Filter
│   ├── Control Room Filter
│   └── Type Filter
│
├── Layer Toggle Controls
│   ├── CCTV Toggle
│   ├── SAP Toggle
│   ├── GR Toggle
│   ├── Insiden Toggle
│   ├── Unit Toggle
│   └── GPS Toggle
│
└── Sidebar Panel
    ├── Tab Navigation
    ├── Search Box
    ├── Data List
    └── Detail View

fullMaps.blade.php
├── Fullscreen Map Container
│   └── Google Maps Style Interface
│
├── Header
│   ├── Menu Button
│   ├── Search Box
│   └── User Controls
│
├── Left Sidebar
│   ├── Filter Controls
│   ├── Layer Controls
│   └── Navigation Controls
│
└── Right Sidebar
    └── Detail Panel</pre>
      </div>
    </div>

    <div class="section">
      <h2>5.4. Database Relationship Diagram</h2>

      <div class="diagram-box">
        <h3>Entity Relationship</h3>
        <pre>cctv_data_bmo2
    │
    ├──► cctv_coverage (1:N)
    │       └──► Coverage details
    │
    └──► cctv_control_room_pengawas (M:N via control_room)
            └──► Pengawas assignment

geojson_areas
    │
    └──► Used by MapBaseController
            └──► Area kerja & Area CCTV

insiden_tabel
    │
    └──► Standalone table
            └──► Incident records

gr_table
    │
    └──► Standalone table
            └──► Golden Rules

cctv_control_room_pengawas
    │
    ├──► Links pengawas to control_room
    └──► Used for filtering CCTV data</pre>
      </div>
    </div>

    <div class="section">
      <h2>5.5. Security Architecture</h2>

      <div class="diagram-box">
        <h3>Security Layers</h3>
        <pre>┌─────────────────────────────────────┐
│     Authentication Layer            │
│  • Laravel Auth                     │
│  • Session Management               │
│  • Password Hashing (bcrypt)        │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│     Authorization Layer             │
│  • Role-Based Access Control        │
│  • Middleware Protection            │
│  • View-Level Checks                │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│     Data Filtering Layer            │
│  • Role-Based Data Filtering        │
│  • Control Room Filtering           │
│  • Company/Site Filtering           │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│     Application Layer               │
│  • Input Validation                 │
│  • SQL Injection Prevention         │
│  • XSS Protection                   │
└─────────────────────────────────────┘</pre>
      </div>
    </div>

    <div class="section">
      <h2>5.6. Deployment Architecture</h2>

      <div class="diagram-box">
        <h3>Deployment Structure</h3>
        <pre>┌─────────────────────────────────────┐
│         Web Server (Apache/Nginx)   │
│  • Serves static files              │
│  • Routes to PHP-FPM                │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│         PHP-FPM (Laravel)           │
│  • Processes PHP requests           │
│  • Executes Laravel application     │
└──────────────┬──────────────────────┘
               │
┌──────────────▼──────────────────────┐
│         MySQL Database              │
│  • Primary data storage             │
│  • Transaction support              │
└──────────────┬──────────────────────┘

External Services:
├── WMS Server (Basemap)
├── ClickHouse (Analytics)
├── Besigma DB (External Data)
└── Telegram Bot (Notifications)</pre>
      </div>
    </div>

    <!-- FOOTER (in-document) -->
    <div class="footer">
      <p><strong>Dokumentasi Sistem Hazard Detection &amp; Monitoring</strong></p>
      <p>Berau Coal — Versi 1.0</p>
      <p>Dokumen ini dibuat untuk keperluan dokumentasi teknis dan operasional sistem.</p>
    </div>
  </div>
</body>
</html>
