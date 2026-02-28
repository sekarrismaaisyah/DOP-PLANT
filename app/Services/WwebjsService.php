<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WwebjsService
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct(?string $baseUrl = null, ?string $apiKey = null)
    {
        $this->baseUrl = $baseUrl ?? config('services.wwebjs.url', 'http://localhost:3001');
        $this->apiKey = $apiKey ?? config('services.wwebjs.api_key', 'wa-service-secret-key');
        $this->timeout = (int) config('services.wwebjs.timeout', 30);
    }

    public function sendMessage(string $phoneNumber, string $message): array
    {
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
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/send', [
                    'phone' => $normalizedPhone,
                    'message' => $message,
                ]);

            $body = $response->json() ?? [];

            Log::info('WwebjsService: Response received', [
                'phone' => $normalizedPhone,
                'success' => $body['success'] ?? false,
                'response' => $body,
            ]);

            return [
                'success' => $body['success'] ?? false,
                'id' => $body['id'] ?? null,
                'status' => $body['status'] ?? 'error',
                'response' => $body['response'] ?? $body,
            ];
        } catch (\Throwable $e) {
            Log::error('WwebjsService: Error sending message', [
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

    public function sendBulk(array $recipients, string $messageTemplate, int $delayMs = 3000): array
    {
        $formattedRecipients = [];

        foreach ($recipients as $recipient) {
            $phone = $this->normalizePhoneNumber($recipient['phone'] ?? '');
            if ($phone === '') {
                continue;
            }

            $name = $recipient['name'] ?? '';
            $message = str_replace('{name}', $name, $messageTemplate);

            $formattedRecipients[] = [
                'phone' => $phone,
                'message' => $message,
            ];
        }

        if (empty($formattedRecipients)) {
            return [
                'success' => false,
                'error' => 'No valid recipients',
                'results' => [],
            ];
        }

        try {
            $response = Http::timeout($this->timeout * count($formattedRecipients))
                ->withHeaders([
                    'Authorization' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/send-bulk', [
                    'recipients' => $formattedRecipients,
                    'delayMs' => $delayMs,
                ]);

            return $response->json() ?? ['success' => false, 'error' => 'Empty response'];
        } catch (\Throwable $e) {
            Log::error('WwebjsService: Error sending bulk', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'results' => [],
            ];
        }
    }

    public function getStatus(): array
    {
        try {
            $response = Http::timeout(5)
                ->get($this->baseUrl . '/status');

            return $response->json() ?? ['ready' => false];
        } catch (\Throwable $e) {
            return [
                'ready' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function isReady(): bool
    {
        $status = $this->getStatus();
        return $status['ready'] ?? false;
    }

    public function getQrCode(): ?string
    {
        try {
            $response = Http::timeout(5)
                ->get($this->baseUrl . '/qr');

            $body = $response->json() ?? [];
            return $body['qr'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

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
