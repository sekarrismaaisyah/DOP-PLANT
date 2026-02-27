<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private const API_URL = 'https://api.fonnte.com/send';
    private const TIMEOUT_SECONDS = 30;

    private string $apiToken;

    public function __construct(?string $apiToken = null)
    {
        $this->apiToken = $apiToken ?? config('services.fonnte.token', '');
    }

    /**
     * Kirim pesan WhatsApp via Fonnte API.
     *
     * @param  string  $phoneNumber  Nomor tujuan (format: 628xxx atau 08xxx)
     * @param  string  $message  Pesan yang akan dikirim
     * @return array  ['success' => bool, 'id' => string|null, 'status' => string, 'response' => array]
     */
    public function sendMessage(string $phoneNumber, string $message): array
    {
        if ($this->apiToken === '') {
            Log::error('FonnteService: API token tidak dikonfigurasi');
            return [
                'success' => false,
                'id' => null,
                'status' => 'error',
                'response' => ['error' => 'API token tidak dikonfigurasi'],
            ];
        }

        $normalizedPhone = $this->normalizePhoneNumber($phoneNumber);
        if ($normalizedPhone === '') {
            return [
                'success' => false,
                'id' => null,
                'status' => 'error',
                'response' => ['error' => 'Nomor telepon tidak valid'],
            ];
        }

        try {
            $response = Http::timeout(self::TIMEOUT_SECONDS)
                ->withHeaders([
                    'Authorization' => $this->apiToken,
                ])
                ->asForm()
                ->post(self::API_URL, [
                    'target' => $normalizedPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

            $body = $response->json() ?? [];
            $isSuccess = $response->successful() && ($body['status'] ?? false) === true;

            Log::info('FonnteService: Pesan terkirim', [
                'phone' => $normalizedPhone,
                'success' => $isSuccess,
                'response' => $body,
            ]);

            return [
                'success' => $isSuccess,
                'id' => $body['id'] ?? null,
                'status' => $isSuccess ? 'success' : 'failed',
                'response' => $body,
            ];
        } catch (\Throwable $e) {
            Log::error('FonnteService: Error mengirim pesan', [
                'phone' => $normalizedPhone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'id' => null,
                'status' => 'error',
                'response' => ['error' => $e->getMessage()],
            ];
        }
    }

    /**
     * Kirim pesan ke banyak nomor sekaligus.
     *
     * @param  array  $recipients  Array of ['phone' => string, 'name' => string, 'sid' => string]
     * @param  string  $messageTemplate  Template pesan (gunakan {name} untuk placeholder nama)
     * @return array  Array of results per recipient
     */
    public function sendBulk(array $recipients, string $messageTemplate): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            $phone = $recipient['phone'] ?? '';
            $name = $recipient['name'] ?? '';

            if ($phone === '') {
                continue;
            }

            $message = str_replace('{name}', $name, $messageTemplate);
            $result = $this->sendMessage($phone, $message);
            $result['recipient'] = $recipient;
            $results[] = $result;

            usleep(500000); // 500ms delay antar pengiriman untuk menghindari rate limit
        }

        return $results;
    }

    /**
     * Normalize nomor telepon ke format Indonesia (62xxx).
     */
    public function normalizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[\s\-\(\)]+/', '', trim($phone));

        if ($phone === '') {
            return '';
        }

        if (str_starts_with($phone, '+62')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }

        if (str_starts_with($phone, '62')) {
            return $phone;
        }

        if (preg_match('/^8\d{8,12}$/', $phone)) {
            return '62' . $phone;
        }

        return $phone;
    }
}
