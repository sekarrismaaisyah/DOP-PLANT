<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Summary Planning Roster</title>
  <style type="text/css">
    body, table, td { -webkit-text-size-adjust: 100%; }
    body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
    table { border-collapse: collapse; }
    .wrapper { max-width: 600px; margin: 0 auto; padding: 24px 16px; }
    .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); overflow: hidden; }
    .header { background: #0f172a; color: #fff; padding: 20px 24px; }
    .header h1 { margin: 0; font-size: 18px; font-weight: 600; }
    .header .meta { font-size: 12px; opacity: 0.9; margin-top: 6px; }
    .body { padding: 24px; }
    .body p { margin: 0 0 16px; color: #475569; font-size: 14px; line-height: 1.6; }
    .summary-table { width: 100%; font-size: 13px; border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; }
    .summary-table th { background: #f8fafc; color: #475569; font-weight: 600; text-align: left; padding: 10px 12px; border-bottom: 1px solid #e2e8f0; }
    .summary-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    .summary-table tr:last-child td { border-bottom: none; }
    .summary-table tr:nth-child(even) { background: #fafafa; }
    .karyawan-list { margin: 0; padding-left: 0; list-style: none; }
    .karyawan-list li { padding: 2px 0; color: #334155; }
    .footer { padding: 16px 24px; background: #f8fafc; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; }
  </style>
</head>
<body>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f1f5f9;">
<tr><td class="wrapper">

  <div class="card">
    <div class="header">
      <h1>Site Notice</h1>
      <div class="meta">{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y') }} &middot; {{ $siteLabel }}</div>
    </div>
    <div class="body">
      <p>Berikut ringkasan data planning yang telah disimpan untuk periode dan site di atas.</p>

      <table class="summary-table" role="presentation" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th style="width: 28px;">No</th>
            <th>Lokasi</th>
            <th>Detail Lokasi</th>
            <th>Aktivitas</th>
            <th>Karyawan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($summary as $idx => $row)
          <tr>
            <td style="color: #64748b;">{{ $idx + 1 }}</td>
            <td>{{ $row['lokasi'] ?? '-' }}</td>
            <td>{{ $row['detail_lokasi'] ?? '-' }}</td>
            <td>{{ $row['aktivitas'] ?? '-' }}</td>
            <td>
              @if(!empty($row['karyawans']))
                <ul class="karyawan-list">
                  @foreach($row['karyawans'] as $nama)
                    <li>{{ $nama }}</li>
                  @endforeach
                </ul>
              @else
                <span style="color: #94a3b8;">-</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">Tidak ada data planning.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="footer">
      Email ini dikirim otomatis dari sistem Roster Planning. &copy; {{ date('Y') }} IKK Monitoring System.
    </div>
  </div>

</td></tr>
</table>

</body>
</html>
