<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;

class TenderAnnouncementController extends Controller
{
    use ApiResponse;

    // Narik semua data pengumuman buat tender tertentu.
    // Misal admin ngasih update info tender, munculnya di sini.
    public function index(Tender $tender): JsonResponse
    {
        if ($tender->status === 'draft') {
            return $this->error('Tender tidak ditemukan.', null, 404);
        }

        $announcements = $tender->announcements()
            ->orderByDesc('published_at')
            ->get()
            ->map(fn ($a) => [
                'id'           => $a->id,
                'title'        => $a->title,
                'content'      => $a->content,
                'published_at' => $a->published_at?->toIso8601String(),
            ]);

        return $this->success($announcements);
    }
}
