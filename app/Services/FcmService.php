<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private ?string $projectId;
    private ?string $credentialsPath;

    public function __construct()
    {
        $this->projectId = config('services.fcm.project_id', env('FCM_PROJECT_ID', ''));
        $this->credentialsPath = storage_path('firebase-credentials.json');
    }

    private function getAccessToken(): ?string
    {
        try {
            if (!file_exists($this->credentialsPath)) {
                Log::error('FCM credentials file not found at: ' . $this->credentialsPath);
                return null;
            }

            $client = new \Google\Client();
            $client->setAuthConfig($this->credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            // Generate access token
            $token = $client->fetchAccessTokenWithAssertion();
            return $token['access_token'] ?? null;
        } catch (\Throwable $e) {
            Log::error('FCM Auth Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Kirim notifikasi ke satu FCM token.
     */
    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): bool
    {
        if (empty($this->projectId) || empty($fcmToken)) {
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) return false;

        $url = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';

        // Cast all values in $data to strings since FCM data payload only accepts string values
        $stringData = array_map('strval', $data);

        try {
            $response = Http::withToken($accessToken)
                ->post($url, [
                    'message' => [
                        'token' => $fcmToken,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => (object)$stringData,
                    ]
                ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning('FCM send failed', ['status' => $response->status(), 'body' => $response->body()]);
            return false;

        } catch (\Throwable $e) {
            Log::error('FCM exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi ke banyak token sekaligus (fallback loop for HTTP v1).
     */
    public function sendToMultiple(array $fcmTokens, string $title, string $body, array $data = []): void
    {
        if (empty($this->projectId) || empty($fcmTokens)) {
            return;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) return;

        $url = 'https://fcm.googleapis.com/v1/projects/' . $this->projectId . '/messages:send';
        $stringData = array_map('strval', $data);

        // Limit loop for basic usage, HTTP v1 doesn't support multicast directly
        $fcmTokens = array_slice(array_filter(array_unique($fcmTokens)), 0, 500);

        foreach ($fcmTokens as $token) {
            try {
                Http::withToken($accessToken)->post($url, [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        'data' => (object)$stringData,
                    ]
                ]);
            } catch (\Throwable $e) {
                Log::error('FCM multicast exception: ' . $e->getMessage());
            }
        }
    }

    /**
     * Kirim push ke user tertentu berdasarkan fcm_token di kolom users.
     */
    public function notifyUser(\App\Models\User $user, string $title, string $body, array $data = []): bool
    {
        if (!$user->fcm_token) {
            return false;
        }
        return $this->sendToToken($user->fcm_token, $title, $body, $data);
    }
}
