<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daily Banned — Notifikasi</title>
  <style>
    body { margin: 0; padding: 0; background: #ffffff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; color: #1e293b; }
    .wrapper { max-width: 680px; margin: 0 auto; padding: 24px 16px; }
    .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
    .header { background: #ffffff; color: #0f172a; padding: 24px 24px 16px; border-bottom: 1px solid #e2e8f0; }
    .header h1 { margin: 0; font-size: 18px; font-weight: 700; color: #0f172a; }
    .header p { margin: 8px 0 0; font-size: 12px; color: #64748b; }
    .body { padding: 24px; font-size: 14px; line-height: 1.6; }
    .intro { margin: 0 0 16px; font-size: 15px; color: #0f172a; }
    .stats { display: flex; gap: 12px; margin: 16px 0; flex-wrap: wrap; }
    .stat { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; min-width: 120px; }
    .stat strong { display: block; font-size: 20px; color: #0f172a; }
    .stat span { font-size: 11px; color: #64748b; text-transform: uppercase; }
    .section-title { margin: 24px 0 8px; font-size: 13px; font-weight: 700; color: #0f172a; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 8px; }
    th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; }
    th { background: #f8fafc; color: #475569; font-weight: 600; }
    td.count { text-align: right; font-weight: 700; color: #0f172a; white-space: nowrap; }
    .footer { padding: 16px 24px; background: #ffffff; font-size: 11px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; border: 1px solid #e2e8f0; background: #fef2f2; color: #b91c1c; }
    .attachment-note { margin: 20px 0 0; padding: 12px 14px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #475569; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="header">
      <h1>Daily Banned — Notifikasi Karyawan Banned</h1>
      <p>{{ $filterDate }} · {{ $filterShift }} · Data scrape {{ $scrapedAt }} WITA</p>
    </div>
    <div class="body">
      <span class="badge">DAILY BANNED</span>
      <p class="intro" style="margin-top:14px">
        <strong>Terdapat {{ $totalBanned }} karyawan yang terdeteksi banned</strong> pada periode
        <strong>{{ $filterDate }}</strong> ({{ $filterShift }}).<br/>
        Berikut ringkasan per Perusahaan &amp; Site. Detail lengkap tersedia di lampiran Excel.
      </p>

      <div class="stats">
        <div class="stat"><strong>{{ $totalBanned }}</strong><span>Total Banned</span></div>
        <div class="stat"><strong>{{ $perusahaanCount }}</strong><span>Perusahaan</span></div>
        <div class="stat"><strong>{{ $siteCount }}</strong><span>Site</span></div>
      </div>

      <p class="section-title">Ringkasan per Perusahaan &amp; Site</p>
      <table>
        <thead>
          <tr>
            <th>Perusahaan</th>
            <th>Site</th>
            <th style="text-align:right;width:120px;">Jumlah Karyawan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($summaryRows as $row)
          <tr>
            <td>{{ $row['perusahaan'] }}</td>
            <td>{{ $row['site'] }}</td>
            <td class="count">{{ $row['count'] }} orang</td>
          </tr>
          @empty
          <tr>
            <td colspan="3">—</td>
          </tr>
          @endforelse
        </tbody>
      </table>

      <p class="attachment-note">
        <strong>Detail lengkap di lampiran Excel</strong> — file <strong>{{ $excelFilename }}</strong>
        berisi {{ $totalBanned }} baris data lengkap:
        <em>SID, NIK, Nama, Perusahaan, Site, Status Banned, Alasan Banned,</em> dan metrik HZR/INS/OBS/RFID/SAP.
      </p>

      @if(filled($dashboardUrl ?? null))
      <p class="attachment-note">
        <strong>Link monitoring:</strong>
        <a href="{{ $dashboardUrl }}" style="color:#3952bc;">{{ $dashboardUrl }}</a>
      </p>
      @endif
    </div>
    <div class="footer">
      Email otomatis dari sistem Monitoring Daily Banned PT. Berau Coal.
      Sumber data: <em>scr_daily_banned</em> (Tableau Daily Banned).
    </div>
  </div>
</div>
</body>
</html>
