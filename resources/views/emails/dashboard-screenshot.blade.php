<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard DOPM Screenshot</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #1a1a1a; font-size: 20px; margin-bottom: 8px; }
        .meta { color: #666; font-size: 14px; margin-bottom: 20px; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #eee; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Screenshot Dashboard DOPM</h1>
        <p class="meta">Waktu kirim: <strong>{{ $timeOfDay }}</strong> — {{ now()->format('d F Y H:i') }} WIB</p>
        <p>Berikut ini adalah screenshot otomatis dari Dashboard DOPM yang dikirim sesuai jadwal harian (pagi, siang, sore).</p>
        <p>Gambar terlampir dalam email ini.</p>
        <div class="footer">
            Email ini dikirim otomatis oleh sistem. Jangan balas ke email ini.
        </div>
    </div>
</body>
</html>
