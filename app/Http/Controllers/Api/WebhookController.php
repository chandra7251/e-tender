<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\WebhookSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class WebhookController extends Controller {
    public function index(): JsonResponse {
        return $this->success(WebhookSubscription::latest()->get());
    }
    public function store(Request $req): JsonResponse {
        $req->validate(['name'=>'required|string','url'=>'required|url','events'=>'required|array','events.*'=>'in:tender.created,tender.published,tender.winner_decided,po.issued,contract.active,contract.completed']);
        $webhook = WebhookSubscription::create(['name'=>$req->name,'url'=>$req->url,'events'=>$req->events,'secret'=>\Illuminate\Support\Str::random(40)]);
        return $this->success($webhook,'Webhook berhasil didaftarkan.',201);
    }
    public function destroy(WebhookSubscription $webhook): JsonResponse {
        $webhook->delete();
        return $this->success(null,'Webhook dihapus.');
    }
    public function toggle(WebhookSubscription $webhook): JsonResponse {
        $webhook->update(['is_active'=>!$webhook->is_active]);
        return $this->success($webhook);
    }
    /** Kirim event ke semua webhook subscriber (dipanggil dari service) */
    public static function dispatch(string $event, array $payload): void {
        $hooks = WebhookSubscription::where('is_active',true)->where('events','like',"%{$event}%")->get();
        foreach ($hooks as $hook) {
            try {
                $body = json_encode(['event'=>$event,'payload'=>$payload,'timestamp'=>now()->toISOString()]);
                $sig = hash_hmac('sha256',$body,$hook->secret??'');
                Http::timeout(5)->withHeaders(['X-Zeta-Event'=>$event,'X-Zeta-Signature'=>$sig,'Content-Type'=>'application/json'])->post($hook->url,$payload);
                $hook->update(['last_triggered_at'=>now(),'failure_count'=>0]);
            } catch (\Throwable) {
                $hook->increment('failure_count');
                if ($hook->failure_count >= 10) $hook->update(['is_active'=>false]);
            }
        }
    }
}
