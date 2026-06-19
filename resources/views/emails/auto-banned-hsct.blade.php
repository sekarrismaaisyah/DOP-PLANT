<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Banned — Email HSECT</title>
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
    .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; border: 1px solid #e2e8f0; background: #f8fafc; color: #475569; }
    .attachment-note { margin: 20px 0 0; padding: 12px 14px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; color: #475569; }
    .summary-grid { display: block; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="header">
      <h1>Auto Banned — List HSECT</h1>
      <p>Periode {{ $week }} · {{ $isoYear }} · {{ now()->format('d M Y H:i') }} WITA</p>
    </div>
    <div class="body">
      @if($emailType === \App\Enums\AutoBannedHsctEmailType::Initial)
      <span class="badge">EMAIL AWAL</span>
      <p class="intro" style="margin-top:14px">
        <strong>List berikut harus di-banned.</strong><br/>
        Mohon proses banned di sistem HSECT sesuai ringkasan di bawah ini.
      </p>
      @else
      <span class="badge">REMINDER #{{ $reminderNumber }}</span>
      <p class="intro" style="margin-top:14px">
        <strong>List berikut masih harus di-banned.</strong><br/>
        Reminder untuk SID yang belum diproses banned oleh HSECT.
      </p>
      @endif

      <div class="stats">
        <div class="stat"><strong>{{ count($employees) }}</strong><span>Total harus di-banned</span></div>
        <div class="stat"><strong>{{ $perusahaanCount }}</strong><span>Perusahaan</span></div>
        <div class="stat"><strong>{{ $siteCount }}</strong><span>Site</span></div>
      </div>

      <p class="section-title">Ringkasan per Perusahaan &amp; Site</p>
      <table>
        <thead>
          <tr>
            <th>Perusahaan</th>
            <th>Site</th>
            <th style="text-align:right;width:120px;">Jumlah Orang</th>
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
        <strong>Detail lengkap di lampiran Excel</strong> — file <strong>{{ $excelFilename ?? 'auto-banned-hsct.xlsx' }}</strong>
        berisi {{ count($employees) }} baris data lengkap: <em>Nama, SID, Perusahaan, Site,</em> dan Alasan Banned.
        Gunakan file Excel sebagai referensi utama untuk proses banned di HSECT.
      </p>
    </div>
    <div class="footer">
      Email otomatis dari sistem Monitoring Auto Banned PT. Berau Coal.
      @if($emailType === \App\Enums\AutoBannedHsctEmailType::Reminder)
      Reminder akan terus dikirim setiap hari hingga semua SID dikonfirmasi banned.
      @endif
    </div>
  </div>
</div>
</body>
</html>
