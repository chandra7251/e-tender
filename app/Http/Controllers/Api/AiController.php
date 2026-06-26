<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Services\AiPricePredictionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(private AiPricePredictionService $ai) {}

    /** Prediksi harga wajar berdasarkan kategori dan HPS */
    public function predictPrice(Request $request): JsonResponse
    {
        $request->validate(['category' => 'required|string', 'hps' => 'required|numeric|min:1']);
        $result = $this->ai->predictPrice($request->category, $request->hps);
        return $this->success($result, 'Prediksi harga berhasil dianalisis.');
    }

    /** Deteksi anomali harga bid tertentu dalam tender */
    public function detectAnomaly(Request $request): JsonResponse
    {
        $request->validate(['tender_id' => 'required|integer|exists:tenders,id', 'bid_price' => 'required|numeric|min:0']);
        $result = $this->ai->detectAnomaly($request->tender_id, $request->bid_price);
        return $this->success($result, 'Analisis anomali selesai.');
    }

    /** Skor dan ranking vendor untuk tender tertentu */
    public function scoreVendors(int $tenderId): JsonResponse
    {
        $result = $this->ai->scoreVendors($tenderId);
        return $this->success($result, 'Scoring vendor selesai.');
    }

    /** Full AI analysis untuk satu tender */
    public function analyzeTender(int $tenderId): JsonResponse
    {
        $result = $this->ai->analyzeTender($tenderId);
        return $this->success($result, 'Analisis AI selesai.');
    }
}
