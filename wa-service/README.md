# WhatsApp Web Service

Service Node.js untuk mengirim WhatsApp menggunakan whatsapp-web.js sebagai alternatif Fonnte.

## Persyaratan

- Node.js >= 18.0.0
- Google Chrome atau Chromium (untuk Puppeteer)

## Instalasi

```bash
cd wa-service
npm install
```

## Konfigurasi

Copy file `.env.example` ke `.env` dan sesuaikan:

```bash
cp .env.example .env
```

Edit `.env`:
```
WA_SERVICE_PORT=3001
WA_SERVICE_API_KEY=ganti-dengan-api-key-yang-aman
```

## Menjalankan Service

```bash
node index.js
```

Atau:
```bash
npm start
```

Saat pertama kali dijalankan:
1. QR code akan muncul di terminal
2. Scan QR code dengan WhatsApp di HP
3. Setelah berhasil, session akan tersimpan di `.wwebjs_auth/`

## Endpoints

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| GET | /status | No | Cek status koneksi |
| GET | /qr | No | Ambil QR code data |
| GET | /health | No | Health check |
| POST | /send | Yes | Kirim pesan tunggal |
| POST | /send-bulk | Yes | Kirim pesan massal |
| POST | /logout | Yes | Logout WhatsApp |

### Autentikasi

Endpoint yang memerlukan auth harus menyertakan header:
```
Authorization: <API_KEY>
```

### Contoh Request

**Kirim Pesan:**
```bash
curl -X POST http://localhost:3001/send \
  -H "Authorization: wa-service-secret-key" \
  -H "Content-Type: application/json" \
  -d '{"phone": "081234567890", "message": "Hello World!"}'
```

**Cek Status:**
```bash
curl http://localhost:3001/status
```

## Integrasi dengan Laravel

1. Tambahkan konfigurasi di `.env` Laravel:
```
WHATSAPP_PROVIDER=wwebjs
WWEBJS_URL=http://localhost:3001
WWEBJS_API_KEY=ganti-dengan-api-key-yang-aman
```

2. Jalankan migration:
```bash
php artisan migrate
```

3. Gunakan command dengan provider wwebjs:
```bash
php artisan dopm:auto-alert-wa --provider=wwebjs --test-phone=081234567890 --limit=1
```

## Tips Menghindari Ban

1. **Delay antar pesan**: Minimal 3 detik (sudah diatur otomatis)
2. **Jangan spam**: Batasi jumlah pesan per hari
3. **Gunakan nomor aged**: Nomor yang sudah lama dipakai
4. **Variasi pesan**: Hindari pesan identik berulang
5. **Simpan kontak**: Nomor tujuan sebaiknya ada di kontak

## Troubleshooting

### QR Code tidak muncul
- Pastikan Chrome/Chromium terinstall
- Coba hapus folder `.wwebjs_auth/` dan restart

### Session expired
- Restart service dan scan QR ulang

### Error: "WhatsApp not ready"
- Pastikan sudah scan QR
- Cek status dengan `GET /status`
