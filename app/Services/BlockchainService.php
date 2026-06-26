<?php

namespace App\Services;

use App\Models\BlockchainRecord;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Blockchain Transparency Layer
 * Stores SHA-256 hashes of critical tender data to a public ledger.
 * Uses Polygon Mumbai testnet (free) or falls back to local hash chain.
 */
class BlockchainService
{
    private string $network;
    private string $rpcUrl;

    public function __construct()
    {
        $this->network = config('services.blockchain.network', 'local');
        $this->rpcUrl  = config('services.blockchain.rpc_url', '');
    }

    /**
     * Record a tender event to blockchain.
     * Returns the record with hash and transaction reference.
     */
    public function recordTenderEvent(string $eventType, int $tenderId, array $data): BlockchainRecord
    {
        $payload = [
            'event'      => $eventType,
            'tender_id'  => $tenderId,
            'data'       => $data,
            'timestamp'  => now()->toIso8601String(),
            'app'        => 'ZETA',
            'version'    => '2.0',
        ];

        // Generate SHA-256 hash dari payload
        $hash = $this->generateHash($payload);

        // Previous hash untuk chain integrity
        $prevRecord  = BlockchainRecord::where('tender_id', $tenderId)->latest()->first();
        $prevHash    = $prevRecord?->hash ?? str_repeat('0', 64);
        $blockHash   = $this->generateHash(['hash' => $hash, 'prev_hash' => $prevHash]);

        // Simpan ke DB dulu
        $record = BlockchainRecord::create([
            'tender_id'    => $tenderId,
            'event_type'   => $eventType,
            'payload_hash' => $hash,
            'block_hash'   => $blockHash,
            'prev_hash'    => $prevHash,
            'payload'      => json_encode($payload),
            'network'      => $this->network,
            'tx_hash'      => null,
            'verified'     => false,
        ]);

        // Coba submit ke blockchain eksternal
        $txHash = $this->submitToChain($blockHash, $tenderId, $eventType);
        if ($txHash) {
            $record->update(['tx_hash' => $txHash, 'verified' => true]);
        }

        return $record->fresh();
    }

    /**
     * Record contract signing event.
     */
    public function recordContractSigned(int $contractId, string $contractNumber, float $value, int $vendorId): BlockchainRecord
    {
        return $this->recordTenderEvent('CONTRACT_SIGNED', $contractId, [
            'contract_number' => $contractNumber,
            'value'           => $value,
            'vendor_id'       => $vendorId,
            'signed_at'       => now()->toIso8601String(),
        ]);
    }

    /**
     * Record winner selection event.
     */
    public function recordWinnerSelected(int $tenderId, int $vendorId, float $winnerPrice): BlockchainRecord
    {
        return $this->recordTenderEvent('WINNER_SELECTED', $tenderId, [
            'vendor_id'    => $vendorId,
            'winner_price' => $winnerPrice,
            'selected_at'  => now()->toIso8601String(),
        ]);
    }

    /**
     * Verify integrity of a blockchain record.
     * Returns true if chain is intact and not tampered.
     */
    public function verifyRecord(int $recordId): array
    {
        $record = BlockchainRecord::findOrFail($recordId);
        $payload = json_decode($record->payload, true);

        // Recalculate hash dari payload tersimpan
        $recalculated = $this->generateHash($payload);
        $hashMatch    = $recalculated === $record->payload_hash;

        // Verify block hash
        $recalcBlock  = $this->generateHash([
            'hash'      => $record->payload_hash,
            'prev_hash' => $record->prev_hash,
        ]);
        $blockMatch   = $recalcBlock === $record->block_hash;

        // Check chain continuity (prev block)
        $prevRecord   = BlockchainRecord::where('tender_id', $record->tender_id)
            ->where('id', '<', $record->id)->latest()->first();
        $chainIntact  = !$prevRecord || $prevRecord->block_hash === $record->prev_hash;

        $isValid = $hashMatch && $blockMatch && $chainIntact;

        return [
            'record_id'       => $record->id,
            'tender_id'       => $record->tender_id,
            'event_type'      => $record->event_type,
            'is_valid'        => $isValid,
            'hash_match'      => $hashMatch,
            'block_match'     => $blockMatch,
            'chain_intact'    => $chainIntact,
            'payload_hash'    => $record->payload_hash,
            'block_hash'      => $record->block_hash,
            'tx_hash'         => $record->tx_hash,
            'network'         => $record->network,
            'created_at'      => $record->created_at->toIso8601String(),
            'verification_status' => $isValid ? 'VALID_TIDAK_DIMANIPULASI' : 'PERINGATAN_DATA_BERUBAH',
        ];
    }

    /**
     * Get full chain of events for a tender.
     */
    public function getTenderChain(int $tenderId): array
    {
        $records = BlockchainRecord::where('tender_id', $tenderId)->orderBy('id')->get();
        return $records->map(fn($r) => $this->verifyRecord($r->id))->toArray();
    }

    /**
     * Public verification page data — no auth needed.
     */
    public function publicVerify(string $blockHash): array
    {
        $record = BlockchainRecord::where('block_hash', $blockHash)
            ->orWhere('payload_hash', $blockHash)
            ->first();

        if (!$record) {
            return ['found' => false, 'message' => 'Hash tidak ditemukan dalam sistem ZETA.'];
        }

        return array_merge(
            ['found' => true],
            $this->verifyRecord($record->id)
        );
    }

    // ─── Private helpers ────────────────────────────────────────────────────

    private function generateHash(array $data): string
    {
        ksort($data);
        return hash('sha256', json_encode($data));
    }

    private function submitToChain(string $blockHash, int $tenderId, string $event): ?string
    {
        // Jika network = local, tidak perlu submit ke chain external
        if ($this->network === 'local' || empty($this->rpcUrl)) {
            return 'LOCAL-' . strtoupper(substr($blockHash, 0, 16));
        }

        // Submit ke Polygon Mumbai atau chain lain via HTTP (opsional)
        try {
            $memo = "ZETA|{$tenderId}|{$event}|" . substr($blockHash, 0, 16);
            $response = Http::timeout(5)->post($this->rpcUrl, [
                'jsonrpc' => '2.0',
                'method'  => 'eth_sendRawTransaction',
                'params'  => [$memo],
                'id'      => 1,
            ]);
            return $response->json('result') ?? null;
        } catch (\Throwable $e) {
            Log::warning('Blockchain submit gagal: ' . $e->getMessage());
            return 'LOCAL-' . strtoupper(substr($blockHash, 0, 16));
        }
    }
}
