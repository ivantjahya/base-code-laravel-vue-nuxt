<?php

namespace App\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

trait TelegramApi
{
    /**
     * Private function for checking if the Telegram API is available.
     */
    public static function isTelegramApiAvailable(): bool
    {
        try {
            $response = Http::asForm()->post(config('telegram.endpoint').config('telegram.token').'/getMe');

            $response = $response->json();

            return $response['ok'] ?? false;
        } catch (ConnectionException $e) {
            return false;
        }
    }

    /**
     * Private function for sending message to Telegram.
     */
    public static function sendTelegramMessage(string $message, string $chatId, string $mode): bool
    {
        try {
            $response = Http::asForm()->post(config('telegram.endpoint').config('telegram.token').'/sendMessage', [
                'chat_id' => $chatId,
                'text' => substr($message, 0, 4096),
                'parse_mode' => $mode,
            ]);

            $response = $response->json();

            return $response['ok'] ?? false;
        } catch (ConnectionException $e) {
            return false;
        }
    }
}
