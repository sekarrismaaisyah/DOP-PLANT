<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Banned — Email HSECT</title>
  <style>
    body { margin: 0; padding: 0; background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    .wrapper { max-width: 680px; margin: 0 auto; padding: 24px 16px; }
    .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); overflow: hidden; }
    .header { background: #3952bc; color: #fff; padding: 20px 24px; }
    .header h1 { margin: 0; font-size: 18px; }
    .header p { margin: 6px 0 0; font-size: 12px; opacity: .9; }
    .body { padding: 24px; color: #334155; font-size: 14px; line-height: 1.6; }
    .stats { display: flex; gap: 12px; margin: 16px 0; flex-wrap: wrap; }
    .stat { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; min-width: 120px; }
    .stat strong { display: block; font-size: 20px; color: #0f172a; }
    .stat span { font-size: 11px; color: #64748b; text-transform: uppercase; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 16px; }
    th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; }
    th { background: #f8fafc; color: #475569; }
    .footer { padding: 16px 24px; background: #f8fafc; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .badge-initial { background: #dbeafe; color: #1d4ed8; }
    .badge-reminder { background: #fef3c7; color: #b45309; }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="header">
      <h1>Auto Banned — Notifikasi HSECT</h1>
      <p>Periode {{ $week }} · {{ $isoYear }} · {{ now()->format('d M Y H:i') }}</p>
    </div>
    <div class="body">
      @if($emailType === \App\Enums\AutoBannedHsctEmailType::Initial)
      <span class="badge badge-initial">EMAIL AWAL — SELASA</span>
      <p style="margin-top:12px">
        Berikut daftar <strong>seluruh karyawan/SID</strong> yang terdeteksi <strong>Not Passed / banned</strong>
        (tidak ada SPA) untuk periode ini. Mohon proses banned di sistem HSECT.
      </p>
      @else
      <span class="badge badge-reminder">REMINDER #{{ $reminderNumber }}</span>
      <p style="margin-top:12px">
        Reminder harian: berikut SID yang <strong>masih belum dikonfirmasi banned</strong> oleh HSECT
        dari list awal yang dikirim pada Selasa.
      </p>
      @endif

      <div class="stats">
        <div class="stat"><strong>{{ $totalInitial }}</strong><span>Total list awal</span></div>
        <div class="stat"><strong>{{ $confirmedCount }}</strong><span>Sudah banned</span></div>
        <div class="stat"><strong>{{ $pendingCount }}</strong><span>Belum banned</span></div>
      </div>

      <table>
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>SID</th>
            <th>Site</th>
            <th>Perusahaan</th>
            <th>Alasan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($employees as $i => $emp)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $emp['karyawan'] ?: '—' }}</td>
            <td><strong>{{ $emp['sid'] }}</strong></td>
            <td>{{ $emp['site'] ?: '—' }}</td>
            <td>{{ $emp['perusahaan'] ?: '—' }}</td>
            <td>{{ $emp['reason'] ?: '—' }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
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
