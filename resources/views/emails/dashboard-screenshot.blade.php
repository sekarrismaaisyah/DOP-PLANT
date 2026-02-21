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

        <!-- KPI ROW 1 — 3 columns -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="kpi-3col" style="border:1px solid #E8E7E3;border-collapse:collapse;margin-bottom:1px;">
        <tr>
          <td style="padding:20px 20px 16px;border-right:1px solid #E8E7E3;vertical-align:top;width:33.33%;">
            <div style="font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.09em;text-transform:uppercase;color:#999994;margin-bottom:10px;line-height:1.4;">Need<br>Verification</div>
            <div class="kpi-num" style="font-family:Georgia,'Times New Roman',serif;font-size:40px;line-height:1;letter-spacing:-1px;color:#111110;margin-bottom:6px;">{{ $needVerification ?? 36 }}</div>
            <div style="font-family:Georgia,serif;font-size:11px;color:#999994;">item IKK pending</div>
          </td>
          <td style="padding:20px 20px 16px;border-right:1px solid #E8E7E3;vertical-align:top;width:33.33%;">
            <div style="font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.09em;text-transform:uppercase;color:#999994;margin-bottom:10px;line-height:1.4;">Pekerjaan<br>Batal</div>
            <div class="kpi-num" style="font-family:Georgia,'Times New Roman',serif;font-size:40px;line-height:1;letter-spacing:-1px;color:#B83232;margin-bottom:6px;">{{ $cancelCount ?? 0 }}</div>
            <div style="font-family:Georgia,serif;font-size:11px;color:#999994;">cancel hari ini</div>
          </td>
          <td style="padding:20px 20px 16px;vertical-align:top;width:33.33%;">
            <div style="font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.09em;text-transform:uppercase;color:#999994;margin-bottom:10px;line-height:1.4;">Compliance<br>Rate</div>
            <div class="kpi-num" style="font-family:Georgia,'Times New Roman',serif;font-size:40px;line-height:1;letter-spacing:-1px;color:#B83232;margin-bottom:6px;">{{ $compliance ?? '0%' }}</div>
            <div style="font-family:Georgia,serif;font-size:11px;color:#999994;">target &gt; 80%</div>
          </td>
        </tr>
        </table>

        <!-- KPI ROW 2 — 2 columns -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="kpi-2col" style="border:1px solid #E8E7E3;border-top:none;border-collapse:collapse;margin-bottom:28px;">
        <tr>
          <td style="padding:18px 20px;border-right:1px solid #E8E7E3;vertical-align:top;width:50%;">
            <div style="font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.09em;text-transform:uppercase;color:#999994;margin-bottom:8px;">OAK Hari Ini</div>
            <div class="kpi-num-sm" style="font-family:Georgia,'Times New Roman',serif;font-size:30px;line-height:1;letter-spacing:-0.5px;color:#111110;margin-bottom:4px;">{{ $oakToday ?? 0 }}</div>
            <div style="font-family:Georgia,serif;font-size:11px;color:#999994;">Total OAK IKK</div>
          </td>
          <td style="padding:18px 20px;vertical-align:top;width:50%;">
            <div style="font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.09em;text-transform:uppercase;color:#999994;margin-bottom:8px;">Data IKK Minggu Ini</div>
            <div class="kpi-num-sm" style="font-family:Georgia,'Times New Roman',serif;font-size:30px;line-height:1;letter-spacing:-0.5px;color:#111110;margin-bottom:4px;">{{ $weeklyCount ?? 25 }}<span style="font-size:16px;color:#CCCCC6;">+</span></div>
            <div style="font-family:Georgia,serif;font-size:11px;color:#999994;">per {{ now()->format('d M Y') }}</div>
          </td>
        </tr>
        </table>

        <!-- STATUS LABEL -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:14px;">
        <tr>
          <td style="font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.14em;text-transform:uppercase;color:#CCCCC6;white-space:nowrap;padding-right:12px;">Status Pekerjaan</td>
          <td width="100%"><table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td style="border-top:1px solid #ECEAE6;">&nbsp;</td></tr></table></td>
        </tr>
        </table>

        <!-- STATUS 3 CARDS -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="status-3col">
        <tr>
          <!-- Need Action -->
          <td style="padding-right:6px;vertical-align:top;width:33.33%;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr><td style="padding:14px 16px;border:1px solid #F0CECE;background-color:#FDF4F4;">
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:8px;">
              <tr>
                <td style="width:6px;height:6px;background-color:#B83232;border-radius:50%;vertical-align:middle;">&nbsp;</td>
                <td style="padding-left:6px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.1em;text-transform:uppercase;color:#B83232;vertical-align:middle;">Need Action</td>
              </tr>
              </table>
              <div style="font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:normal;letter-spacing:-0.5px;color:#111110;margin-bottom:4px;">{{ $needAction ?? 4 }}</div>
              <div style="font-family:Georgia,serif;font-size:10px;color:#999994;line-height:1.4;">item butuh tindakan segera</div>
            </td></tr>
            </table>
          </td>
          <!-- Warning -->
          <td style="padding:0 3px;vertical-align:top;width:33.33%;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr><td style="padding:14px 16px;border:1px solid #ECD9A8;background-color:#FDF8F0;">
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:8px;">
              <tr>
                <td style="width:6px;height:6px;background-color:#9A6200;border-radius:50%;vertical-align:middle;">&nbsp;</td>
                <td style="padding-left:6px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.1em;text-transform:uppercase;color:#9A6200;vertical-align:middle;">Warning</td>
              </tr>
              </table>
              <div style="font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:normal;letter-spacing:-0.5px;color:#111110;margin-bottom:4px;">{{ $warningCount ?? 0 }}</div>
              <div style="font-family:Georgia,serif;font-size:10px;color:#999994;line-height:1.4;">item dalam status peringatan</div>
            </td></tr>
            </table>
          </td>
          <!-- Complete -->
          <td style="padding-left:6px;vertical-align:top;width:33.33%;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
            <tr><td style="padding:14px 16px;border:1px solid #A8D8BC;background-color:#F2FBF6;">
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:8px;">
              <tr>
                <td style="width:6px;height:6px;background-color:#1E6E48;border-radius:50%;vertical-align:middle;">&nbsp;</td>
                <td style="padding-left:6px;font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.1em;text-transform:uppercase;color:#1E6E48;vertical-align:middle;">Complete</td>
              </tr>
              </table>
              <div style="font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:normal;letter-spacing:-0.5px;color:#111110;margin-bottom:4px;">{{ $completeCount ?? 0 }}</div>
              <div style="font-family:Georgia,serif;font-size:10px;color:#999994;line-height:1.4;">item selesai &amp; terverifikasi</div>
            </td></tr>
            </table>
          </td>
        </tr>
        </table>

      </td>
    </tr>

    <!-- ③ SCREENSHOT SECTION ── -->
    <tr>
      <td class="pad-all" style="padding:36px 48px;border-bottom:1px solid #ECEAE6;background-color:#FAF9F7;">

        <!-- Label -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:16px;">
        <tr>
          <td style="font-family:Georgia,serif;font-size:10px;font-weight:bold;letter-spacing:0.14em;text-transform:uppercase;color:#CCCCC6;white-space:nowrap;padding-right:12px;">Screenshot Dashboard</td>
          <td width="100%"><table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td style="border-top:1px solid #ECEAE6;">&nbsp;</td></tr></table></td>
        </tr>
        </table>

        <!-- Browser frame -->
        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border:1px solid #E2E1DD;background-color:#FFFFFF;">
          <!-- Browser bar -->
          <tr>
            <td style="padding:10px 14px;background-color:#F2F1EE;border-bottom:1px solid #E2E1DD;">
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
              <tr>
                <!-- Dots -->
                <td style="width:auto;">
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td style="width:8px;height:8px;background-color:#E8C4C4;border-radius:50%;font-size:0;line-height:0;">&nbsp;</td>
                    <td style="width:5px;font-size:0;">&nbsp;</td>
                    <td style="width:8px;height:8px;background-color:#E8DBC4;border-radius:50%;font-size:0;line-height:0;">&nbsp;</td>
                    <td style="width:5px;font-size:0;">&nbsp;</td>
                    <td style="width:8px;height:8px;background-color:#C4E8D4;border-radius:50%;font-size:0;line-height:0;">&nbsp;</td>
                  </tr>
                  </table>
                </td>
                <!-- URL bar -->
                <td style="padding-left:12px;">
                  <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                  <tr>
                    <td style="border:1px solid #E2E1DD;background-color:#FFFFFF;padding:4px 10px;font-family:Georgia,serif;font-size:10px;color:#999994;letter-spacing:0.02em;">
                      dopm-system.internal / ikk-dashboard
                    </td>
                  </tr>
                  </table>
                </td>
              </tr>
              </table>
            </td>
          </tr>
          <!-- Screenshot image area -->
          <tr>
            <td align="center" style="padding:0;background-color:#F5F4F1;">
              {{--
                UNTUK PRODUCTION — ganti blok di bawah dengan:
                <img src="{{ $screenshotUrl }}" width="598" style="display:block;width:100%;max-width:598px;" alt="Dashboard IKK DOPM">
                ATAU embed base64:
                <img src="data:image/png;base64,{{ $screenshotBase64 }}" width="598" style="display:block;width:100%;" alt="Dashboard IKK DOPM">
              --}}
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
              <tr>
                <td align="center" style="padding:48px 24px;">
                  <!-- Placeholder icon -->
                  <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;margin:0 auto 12px;">
                    <rect x="2" y="7" width="36" height="27" rx="2" stroke="#CCCCC6" stroke-width="1.5"/>
                    <path d="M2 14h36" stroke="#CCCCC6" stroke-width="1.5"/>
                    <rect x="6" y="18" width="10" height="11" rx="1" fill="#CCCCC6" opacity=".5"/>
                    <rect x="18" y="20" width="10" height="9" rx="1" fill="#CCCCC6" opacity=".5"/>
                    <rect x="30" y="16" width="5" height="13" rx="1" fill="#CCCCC6" opacity=".7"/>
                  </svg>
                  <div style="font-family:Georgia,serif;font-size:12px;color:#AAAAAA;letter-spacing:0.04em;">Screenshot terlampir di email ini</div>
                </td>
              </tr>
              </table>
            </td>
          </tr>
          <!-- Caption bar -->
          <tr>
            <td style="padding:9px 14px;border-top:1px solid #E2E1DD;">
              <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
              <tr>
                <td style="font-family:Georgia,serif;font-size:10px;color:#999994;letter-spacing:0.03em;">IKK Dashboard &middot; {{ now()->format('d F Y, H:i') }} WIB</td>
                <td align="right" style="font-family:Georgia,serif;font-size:10px;color:#CCCCC6;">Semua Situs</td>
              </tr>
              </table>
            </td>
          </tr>
        </table>

      </td>
    </tr>

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
            <div style="font-family:Georgia,serif;font-size:11px;font-weight:bold;letter-spacing:0.08em;text-transform:uppercase;color:#111110;margin-bottom:4px;">DOPM System</div>
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