<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EskizService
{
    public function sendSms(string $mobilePhone, string $message, ?string $from = null, ?string $callbackUrl = null): array
    {
        $token = (string) config('services.eskiz.token');
        $baseUrl = rtrim((string) config('services.eskiz.base_url'), '/');
        $from = $from ?? (string) config('services.eskiz.from');

        $payload = [
            'mobile_phone' => $mobilePhone,
            'message' => $message,
            'from' => $from,
        ];

        if ($callbackUrl) {
            $payload['callback_url'] = $callbackUrl;
        }

        $response = Http::withToken($token)
            ->asForm()
            ->post("{$baseUrl}/api/message/sms/send", $payload);

        if ($response->status() === 401) {
            $responseBody = $response->json();
            if (isset($responseBody['message']) && $responseBody['message'] === 'Expired') {
                Log::error('Eskiz token expired. Please get a new token using: php get_eskiz_token.php', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                    'error' => 'Token expired. Please update ESKIZ_TOKEN in .env file.',
                ];
            }
        }

        if ($response->failed()) {
            Log::warning('Eskiz sendSms failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } else {
            Log::info('SMS sent successfully', [
                'mobile_phone' => $mobilePhone,
                'status' => $response->status(),
            ]);
        }

        return [
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ];
    }

    public function getNewToken(string $email, string $password): array
    {
        $baseUrl = rtrim((string) config('services.eskiz.base_url'), '/');
        
        $response = Http::asForm()->post("{$baseUrl}/api/auth/login", [
            'email' => $email,
            'password' => $password,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['data']['token'])) {
                Log::info('New Eskiz token obtained successfully');
                return [
                    'success' => true,
                    'token' => $data['data']['token'],
                ];
            }
        }

        Log::error('Failed to get new Eskiz token', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'success' => false,
            'error' => $response->body(),
        ];
    }
}
