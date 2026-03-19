<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebSocketService
{
    protected $wsUrl;

    public function __construct()
    {
        $this->wsUrl = config('services.websocket.url', 'http://localhost:8080');
    }

    public function sendNotification(int $userId, array $data): bool
    {
        try {
            $response = Http::post("{$this->wsUrl}/notify", [
                'userId' => $userId,
                'data' => $data
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WebSocket notification failed', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function sendNotificationToMultiple(array $userIds, array $data): bool
    {
        try {
            $response = Http::post("{$this->wsUrl}/notify", [
                'userIds' => $userIds,
                'data' => $data
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WebSocket bulk notification failed', [
                'userIds' => $userIds,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getStatus(): ?array
    {
        try {
            $response = Http::get("{$this->wsUrl}/status");
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('WebSocket status check failed', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
