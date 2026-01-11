<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    private Client $client;

    public function __construct(private readonly string $botToken, int $timeout = 60)
    {
        $this->client = new Client([
            'base_uri' => sprintf('https://api.telegram.org/bot%s/', $botToken),
            'timeout' => $timeout,
        ]);
    }

    public static function makeFromConfig(int $timeout = 60): self
    {
        $token = config('services.telegram.bot_token');

        if (empty($token)) {
            throw new \RuntimeException('Telegram bot token is not configured.');
        }

        return new self($token, $timeout);
    }

    public static function makeFromCctvConfig(int $timeout = 60): self
    {
        $token = config('services.telegram.bot_token_cctv');

        if (empty($token)) {
            throw new \RuntimeException('Telegram CCTV bot token is not configured.');
        }

        return new self($token, $timeout);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function getUpdates(array $payload = []): array
    {
        return $this->request('getUpdates', $payload);
    }

    /**
     * Get bot information
     * @return array<string, mixed>
     */
    public function getMe(): array
    {
        return $this->request('getMe');
    }

    /**
     * Get webhook information
     * @return array<string, mixed>
     */
    public function getWebhookInfo(): array
    {
        return $this->request('getWebhookInfo');
    }

    /**
     * Delete webhook
     * @param  bool  $dropPendingUpdates Drop all pending updates
     * @return array<string, mixed>
     */
    public function deleteWebhook(bool $dropPendingUpdates = false): array
    {
        return $this->request('deleteWebhook', [
            'drop_pending_updates' => $dropPendingUpdates,
        ]);
    }

    /**
     * Send message to Telegram
     * @param  array<string, mixed>  $payload
     * @param  bool  $saveToDatabase Save sent message to database
     * @return array<string, mixed>
     */
    public function sendMessage(array $payload = [], bool $saveToDatabase = false): array
    {
        $response = $this->request('sendMessage', $payload);

        // Save sent message to database if requested
        if ($saveToDatabase && Arr::get($response, 'ok')) {
            $this->saveSentMessage($response['result']);
        }

        return $response;
    }

    /**
     * Save sent message to database
     * @param  array<string, mixed>  $messageData
     * @return void
     */
    private function saveSentMessage(array $messageData): void
    {
        try {
            $botInfo = $this->getMe();
            $botId = Arr::get($botInfo, 'result.id');

            // Use message_id as unique identifier for sent messages
            // Combine with chat_id to ensure uniqueness
            $messageId = Arr::get($messageData, 'message_id');
            $chatId = Arr::get($messageData, 'chat.id');
            
            \App\Models\TelegramMessage::updateOrCreate(
                [
                    'message_id' => $messageId,
                    'chat_id' => $chatId,
                    'is_from_bot' => true,
                ],
                [
                    'update_id' => time() . rand(1000, 9999), // Generate unique update_id for sent messages
                    'chat_type' => Arr::get($messageData, 'chat.type'),
                    'username' => Arr::get($botInfo, 'result.username'),
                    'first_name' => Arr::get($botInfo, 'result.first_name'),
                    'text' => Arr::get($messageData, 'text'),
                    'bot_id' => $botId,
                    'raw_payload' => $messageData,
                    'message_date' => now(),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Failed to save sent message to database', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function request(string $method, array $payload = []): array
    {
        try {
            $response = $this->client->post($method, ['json' => $payload]);

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (ClientException $e) {
            // Handle HTTP client errors (4xx, 5xx)
            $statusCode = $e->getResponse()?->getStatusCode();
            $responseBody = $e->getResponse()?->getBody()?->getContents();
            
            // Try to parse Telegram's error response
            $errorData = [];
            if ($responseBody) {
                try {
                    $errorData = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException $jsonException) {
                    // If JSON parsing fails, use raw response
                    $errorData = ['description' => $responseBody];
                }
            }
            
            // If Telegram returned an error response, return it in the expected format
            if (isset($errorData['ok']) && $errorData['ok'] === false) {
                return $errorData;
            }
            
            // Otherwise, create a standard error response
            Log::error('Telegram API call failed', [
                'method' => $method,
                'status_code' => $statusCode,
                'error' => $errorData,
                'message' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'error_code' => $statusCode ?? 0,
                'description' => $errorData['description'] ?? $e->getMessage(),
            ];
        } catch (\Throwable $e) {
            Log::error('Telegram API call failed', [
                'method' => $method,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}


