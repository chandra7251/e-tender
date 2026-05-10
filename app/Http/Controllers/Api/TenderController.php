<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TenderResource;
use App\Http\Traits\ApiResponse;
use App\Models\Tender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenderController extends Controller
{
    use ApiResponse;

    /** GET /api/tenders */
    public function index(Request $request): JsonResponse
    {
        $allowedStatuses = ['open', 'aanwijzing', 'bidding', 'finished'];

        $query = Tender::query()
            ->whereIn('status', $allowedStatuses)
            ->when($request->input('status'), function ($q, $s) use ($allowedStatuses) {
                if (in_array($s, $allowedStatuses)) {
                    $q->where('status', $s);
                }
            })
            ->when($request->input('search'), fn ($q, $s) =>
                $q->where(function ($q2) use ($s) {
                    $q2->where('title', 'like', "%{$s}%")
                       ->orWhere('description', 'like', "%{$s}%");
                })
            )
            ->orderByDesc('created_at');

        $tenders = $query->paginate(15);

        return response()->json([
            'status'  => 'success',
            'message' => 'OK',
            'data'    => TenderResource::collection($tenders->items()),
            'meta'    => [
                'current_page' => $tenders->currentPage(),
                'last_page'    => $tenders->lastPage(),
                'per_page'     => $tenders->perPage(),
                'total'        => $tenders->total(),
            ],
        ]);
    }

    /** GET /api/tenders/{tender} */
    public function show(Tender $tender): JsonResponse
    {
        if ($tender->status === 'draft') {
            return $this->error('Tender tidak ditemukan.', null, 404);
        }

        return $this->success(new TenderResource($tender));
    }
}
