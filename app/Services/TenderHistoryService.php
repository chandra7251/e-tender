<?php
namespace App\Services;
use App\Models\TenderHistory;
class TenderHistoryService
{
    public function log(
        int $tenderId,
        int $actorId,
        string $action,
        ?string $oldStatus = null,
        ?string $newStatus = null,
        ?string $description = null
    ): TenderHistory {
        return TenderHistory::create([
            'tender_id'   => $tenderId,
            'actor_id'    => $actorId,
            'action'      => $action,
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'description' => $description,
            'created_at'  => now(),
        ]);
    }
}
