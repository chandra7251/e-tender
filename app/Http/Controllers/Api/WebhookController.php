<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebhookSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->success(WebhookSubscription::latest()->get());
    }

    public function store(Request $req): JsonResponse
    {
        $req->validate([
            'name' => 'required|string|max:100',
            'url' => ['required', 'url', function ($attribute, $value, $fail) {
                if (! self::isSafeWebhookUrl($value)) {
                    $fail('URL webhook tidak valid atau mengarah ke alamat internal/privat.');
                }
            }],
            'events' => 'required|array',
            'events.*' => 'in:tender.created,tender.published,tender.winner_decided,po.issued,contract.active,contract.completed',
        ]);
        $webhook = WebhookSubscription::create(['name' => $req->name, 'url' => $req->url, 'events' => $req->events, 'secret' => Str::random(40)]);

        return $this->success($webhook, 'Webhook berhasil didaftarkan.', 201);
    }

    public function destroy(WebhookSubscription $webhook): JsonResponse
    {
        $webhook->delete();

        return $this->success(null, 'Webhook dihapus.');
    }

    public function toggle(WebhookSubscription $webhook): JsonResponse
    {
        $webhook->update(['is_active' => ! $webhook->is_active]);

        return $this->success($webhook);
    }

    /** Kirim event ke semua webhook subscriber (dipanggil dari service) */
    public static function dispatch(string $event, array $payload): void
    {
        $hooks = WebhookSubscription::where('is_active', true)->where('events', 'like', "%{$event}%")->get();
        foreach ($hooks as $hook) {
            if (! self::isSafeWebhookUrl($hook->url)) {
                $hook->increment('failure_count');
                if ($hook->failure_count >= 10) {
                    $hook->update(['is_active' => false]);
                }

                continue;
            }
            try {
                $body = json_encode(['event' => $event, 'payload' => $payload, 'timestamp' => now()->toISOString()]);
                $sig = hash_hmac('sha256', $body, $hook->secret ?? '');
                Http::timeout(5)->withoutRedirecting()->withHeaders(['X-Zeta-Event' => $event, 'X-Zeta-Signature' => $sig, 'Content-Type' => 'application/json'])->post($hook->url, $payload);
                $hook->update(['last_triggered_at' => now(), 'failure_count' => 0]);
            } catch (\Throwable) {
                $hook->increment('failure_count');
                if ($hook->failure_count >= 10) {
                    $hook->update(['is_active' => false]);
                }
            }
        }
    }

    public static function isSafeWebhookUrl(string $url): bool
    {
        $parsed = parse_url($url);
        if (! $parsed || empty($parsed['host']) || empty($parsed['scheme'])) {
            return false;
        }
        $scheme = strtolower($parsed['scheme']);
        if (! in_array($scheme, ['http', 'https'], true)) {
            return false;
        }
        $host = strtolower($parsed['host']);
        if ($host === 'localhost' || str_ends_with($host, '.localhost') || str_ends_with($host, '.local') || str_ends_with($host, '.internal') || str_ends_with($host, '.lan')) {
            return false;
        }
        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
        if (empty($ips)) {
            return false;
        }
        foreach ($ips as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
            if ($ip === '127.0.0.1' || str_starts_with($ip, '127.') || $ip === '::1' || $ip === '169.254.169.254') {
                return false;
            }
        }

        return true;
    }
}
