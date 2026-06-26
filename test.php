<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'vendor')->first();
$tender = App\Models\Tender::where('status', 'bidding')->first();
if (!$user || !$tender) { echo "No user or tender found\n"; exit; }

echo "User: " . $user->id . "\n";
echo "Vendor: " . ($user->vendor ? $user->vendor->id : 'null') . "\n";

try {
    $controller = app(App\Http\Controllers\Api\BidController::class);
    auth('api')->setUser($user);
    $response = $controller->myBid($tender);
    echo "Status: " . $response->status() . "\n";
    echo "Content: " . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
}
