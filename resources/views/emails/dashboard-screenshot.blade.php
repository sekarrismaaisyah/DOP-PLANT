<!DOCTYPE html>
<html lang="id" xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="x-apple-disable-message-reformatting"/>
  <title>Daily Report · Dashboard IKK–DOPM</title>
  <style type="text/css">
    /* ── RESET ── */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    body { margin: 0 !important; padding: 0 !important; background-color: #ECEAE6; width: 100% !important; }

    /* ── MOBILE ── */
    @media screen and (max-width: 600px) {
      .shell        { width: 100% !important; }
      .card         { width: 100% !important; }
      .hero-title   { font-size: 26px !important; line-height: 1.25 !important; }
      .kpi-3col td  { display: block !important; width: 100% !important; border-right: none !important; border-bottom: 1px solid #E8E7E3 !important; }
      .kpi-2col td  { display: block !important; width: 100% !important; border-right: none !important; border-bottom: 1px solid #E8E7E3 !important; }
      .status-3col td { display: block !important; width: 100% !important; margin-bottom: 8px !important; }
      .pad-lr       { padding-left: 24px !important; padding-right: 24px !important; }
      .pad-all      { padding: 28px 24px !important; }
      .hide-mobile  { display: none !important; }
      .kpi-num      { font-size: 32px !important; }
      .kpi-num-sm   { font-size: 24px !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#ECEAE6;">

<!-- ════════════════════════════════════════ WRAPPER -->
<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color:#ECEAE6;">
<tr><td align="center" style="padding:40px 16px 56px;">

  <!-- ── PRE-HEADER ── -->
  <table role="presentation" cellspacing="0" cellpadding="0" border="0" class="shell" style="width:600px;max-width:600px;">
  <tr>
    <td style="padding:0 4px 14px;">
      <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td style="vertical-align:middle;">
          <!-- Logo box + name -->
          <table role="presentation" cellspacing="0" cellpadding="0" border="0">
          <tr>
            <td style="background-color:#111110;width:26px;height:26px;text-align:center;vertical-align:middle;padding:6px;">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="1" y="1" width="5" height="5" fill="white"/>
                <rect x="8" y="1" width="5" height="5" fill="white" opacity=".5"/>
                <rect x="1" y="8" width="5" height="5" fill="white" opacity=".5"/>
                <rect x="8" y="8" width="5" height="5" fill="white"/>
              </svg>
            </td>
            <td style="padding-left:8px;font-family:Georgia,serif;font-size:11px;font-weight:bold;letter-spacing:0.1em;text-transform:uppercase;color:#111110;">IKK Monitoring System</td>
          </tr>
          </table>
        </td>
        <td align="right" class="hide-mobile" style="font-family:Georgia,serif;font-size:11px;color:#999994;letter-spacing:0.04em;">
          {{ now()->format('d F Y') }} &middot; {{ now()->format('H:i') }} WIB
        </td>
      </tr>
      </table>
    </td>
  </tr>
  </table>

  <!-- ════ CARD ════ -->
  <table role="presentation" cellspacing="0" cellpadding="0" border="0" class="shell card" style="width:600px;max-width:600px;background-color:#FFFFFF;border:1px solid #E2E1DD;">

    <!-- TOP ACCENT BAR -->
    <tr><td style="height:4px;background-color:#111110;font-size:0;line-height:0;">&nbsp;</td></tr>

    <!-- ① HERO ── -->
    <tr>
      <td class="pad-all" style="padding:44px 48px 36px;border-bottom:1px solid #ECEAE6;">

        <!-- Eyebrow row -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:22px;">
        <tr>
          <td style="width:auto;padding:4px 12px;border:1px solid #E2E1DD;background-color:#FAF9F7;font-family:Georgia,serif;font-size:10px;letter-spacing:0.14em;text-transform:uppercase;color:#999994;white-space:nowrap;">Daily Report</td>
          <td style="padding:0 12px;"><table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td style="border-top:1px solid #E2E1DD;font-size:0;line-height:0;">&nbsp;</td></tr></table></td>
          <td class="hide-mobile" style="font-family:Georgia,serif;font-size:10px;color:#999994;letter-spacing:0.06em;white-space:nowrap;">Sesi {{ $timeOfDay ?? 'Pagi' }} &middot; {{ now()->format('H:i') }} WIB</td>
        </tr>
        </table>

        <!-- Title -->
        <div class="hero-title" style="font-family:Georgia,'Times New Roman',serif;font-size:34px;font-weight:normal;line-height:1.2;letter-spacing:-0.5px;color:#111110;margin:0 0 16px;">
          Dashboard <em>IKK</em><br>Laporan Harian
        </div>

        <!-- Lead -->
        <p style="font-family:Georgia,serif;font-size:14px;font-weight:normal;color:#555552;line-height:1.75;margin:0;">
          Ringkasan otomatis kondisi Dashboard IKK per
          <strong style="color:#111110;">{{ now()->format('d F Y') }}</strong>.
          Screenshot dashboard terlampir pada email ini untuk keperluan monitoring dan tindak lanjut.
        </p>

        <!-- Bottom accent line (right side) -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top:28px;">
        <tr>
          <td style="border-bottom:1px solid #ECEAE6;">&nbsp;</td>
          <td style="width:80px;border-bottom:3px solid #111110;font-size:0;line-height:0;">&nbsp;</td>
        </tr>
        </table>

      </td>
    </tr>

    <!-- ② SUMMARY METRICS ── -->
    <tr>
      <td class="pad-all" style="padding:36px 48px;border-bottom:1px solid #ECEAE6;">

        <!-- Section label -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:20px;">
        <tr>
          <td style="font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.14em;text-transform:uppercase;color:#CCCCC6;white-space:nowrap;padding-right:12px;">Ringkasan Metrik Utama</td>
          <td width="100%"><table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td style="border-top:1px solid #ECEAE6;">&nbsp;</td></tr></table></td>
        </tr>
        </table>

        <!-- SITE TABLE — IKK / OAK / IPK -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-collapse:collapse;border:1px solid #E8E7E3;">

          <!-- Table Header -->
          <tr style="background-color:#111110;">
            <td style="padding:11px 16px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.12em;text-transform:uppercase;color:#FFFFFF;border-right:1px solid #2A2A28;width:40%;">Site</td>
            <td style="padding:11px 16px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.12em;text-transform:uppercase;color:#FFFFFF;border-right:1px solid #2A2A28;text-align:center;width:20%;">IKK</td>
            <td style="padding:11px 16px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.12em;text-transform:uppercase;color:#FFFFFF;border-right:1px solid #2A2A28;text-align:center;width:20%;">OAK</td>
            <td style="padding:11px 16px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.12em;text-transform:uppercase;color:#FFFFFF;text-align:center;width:20%;">IPK</td>
          </tr>

          @php
            $sites = $summary['sites'] ?? [];
            $totalIkk = (int) ($summary['totalIkk'] ?? 0);
            $totalOak = (int) ($summary['totalOak'] ?? 0);
            $totalIpk = (int) ($summary['totalIpk'] ?? 0);
            if (empty($sites)) {
              $sites = [['name' => 'Semua Situs', 'ikk' => $totalIkk, 'oak' => $totalOak, 'ipk' => $totalIpk]];
            }
            $totIkk = array_sum(array_column($sites, 'ikk'));
            $totOak = isset($summary['totalOak']) ? (int) $summary['totalOak'] : array_sum(array_column($sites, 'oak'));
            $totIpk = array_sum(array_column($sites, 'ipk'));
          @endphp

          @foreach($sites as $i => $site)
          <tr style="background-color:{{ $i % 2 === 0 ? '#FFFFFF' : '#FAF9F7' }};">
            <td style="padding:12px 16px;font-family:Georgia,serif;font-size:13px;font-weight:bold;color:#111110;border-right:1px solid #E8E7E3;border-top:1px solid #E8E7E3;">{{ $site['name'] }}</td>
            <td style="padding:12px 16px;font-family:Georgia,'Times New Roman',serif;font-size:15px;color:#111110;text-align:center;border-right:1px solid #E8E7E3;border-top:1px solid #E8E7E3;">{{ number_format($site['ikk']) }}</td>
            <td style="padding:12px 16px;font-family:Georgia,'Times New Roman',serif;font-size:15px;color:#111110;text-align:center;border-right:1px solid #E8E7E3;border-top:1px solid #E8E7E3;">{{ number_format($site['oak']) }}</td>
            <td style="padding:12px 16px;font-family:Georgia,'Times New Roman',serif;font-size:15px;color:#111110;text-align:center;border-top:1px solid #E8E7E3;">{{ number_format($site['ipk']) }}</td>
          </tr>
          @endforeach

          <!-- Total Row -->
          <!-- <tr style="background-color:#F2F1EE;">
            <td style="padding:12px 16px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.1em;text-transform:uppercase;color:#555552;border-right:1px solid #E8E7E3;border-top:2px solid #CCCCC6;">Total</td>
            <td style="padding:12px 16px;font-family:Georgia,'Times New Roman',serif;font-size:16px;font-weight:bold;color:#111110;text-align:center;border-right:1px solid #E8E7E3;border-top:2px solid #CCCCC6;">{{ number_format($totIkk) }}</td>
            <td style="padding:12px 16px;font-family:Georgia,'Times New Roman',serif;font-size:16px;font-weight:bold;color:#111110;text-align:center;border-right:1px solid #E8E7E3;border-top:2px solid #CCCCC6;">{{ number_format($totOak) }}</td>
            <td style="padding:12px 16px;font-family:Georgia,'Times New Roman',serif;font-size:16px;font-weight:bold;color:#111110;text-align:center;border-top:2px solid #CCCCC6;">{{ number_format($totIpk) }}</td>
          </tr> -->

        </table>

      </td>
    </tr>

    <!-- ③ SCREENSHOT SECTION ── -->
   

    <!-- ④ ATTACHMENT ROW ── -->
    <tr>
      <td style="padding:0 48px 28px;background-color:#FAF9F7;border-bottom:1px solid #ECEAE6;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border:1px solid #E2E1DD;background-color:#FFFFFF;">
        <tr>
          <!-- Icon box -->
          <td style="width:46px;background-color:#111110;text-align:center;vertical-align:middle;padding:15px 14px;">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="2" y="1" width="10" height="15" rx="1" stroke="white" stroke-width="1.3"/>
              <path d="M10 1L15 6" stroke="white" stroke-width="1.3"/>
              <path d="M10 1V6H15" stroke="white" stroke-width="1.3"/>
              <path d="M5 9h6M5 11.5h4" stroke="white" stroke-width="1.1" stroke-linecap="round"/>
            </svg>
          </td>
          <!-- Info -->
          <td style="padding:12px 16px;vertical-align:middle;">
            <div style="font-family:Georgia,serif;font-size:13px;font-weight:bold;color:#111110;margin-bottom:3px;">dashboard-ikk-dopm-{{ now()->format('Ymd') }}-{{ strtolower($timeOfDay ?? 'pagi') }}.png</div>
            <div style="font-family:Georgia,serif;font-size:10px;color:#999994;letter-spacing:0.03em;">Screenshot otomatis &middot; {{ now()->format('d F Y, H:i') }} WIB</div>
          </td>
          <!-- Badge -->
          <td align="right" style="padding:12px 16px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.08em;text-transform:uppercase;color:#CCCCC6;white-space:nowrap;vertical-align:middle;">PNG</td>
        </tr>
        </table>
      </td>
    </tr>

    <!-- ⑤ CTA ── -->
    <tr>
      <td class="pad-all" style="padding:32px 48px;border-bottom:1px solid #ECEAE6;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <td style="background-color:#111110;">
            <a href="{{ $dashboardUrl ?? '#' }}" style="display:inline-block;padding:14px 36px;font-family:Georgia,serif;font-size:11px;font-weight:bold;letter-spacing:0.12em;text-transform:uppercase;color:#FFFFFF;text-decoration:none;">Buka Dashboard &rarr;</a>
          </td>
          <td style="padding-left:16px;font-family:Georgia,serif;font-size:12px;color:#999994;vertical-align:middle;" class="hide-mobile">Atau akses melalui portal internal DOPM</td>
        </tr>
        </table>
      </td>
    </tr>

    <!-- ⑥ FOOTER ── -->
    <tr>
      <td class="pad-all" style="padding:22px 48px;background-color:#FAF9F7;">
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
          <td style="vertical-align:top;">
            <div style="font-family:Georgia,serif;font-size:11px;font-weight:bold;letter-spacing:0.08em;text-transform:uppercase;color:#111110;margin-bottom:4px;">IKK Monitoring System</div>
            <div style="font-family:Georgia,serif;font-size:10px;color:#CCCCC6;">&copy; {{ now()->format('Y') }} &middot; IKK Monitoring &middot; Semua Situs</div>
          </td>
          <td align="right" style="font-family:Georgia,serif;font-size:10px;color:#CCCCC6;line-height:1.7;vertical-align:top;" class="hide-mobile">
            Email ini dikirim otomatis sesuai jadwal harian.<br>
            Jangan balas pesan ini &middot; <a href="#" style="color:#CCCCC6;text-decoration:underline;">Unsubscribe</a>
          </td>
        </tr>
        </table>
      </td>
    </tr>

    <!-- BOTTOM ACCENT -->
    <tr>
      <td style="height:3px;background-color:#111110;font-size:0;line-height:0;">&nbsp;</td>
    </tr>

  </table>
  <!-- end card -->

</td></tr>
</table>
<!-- end wrapper -->

</body>
</html>