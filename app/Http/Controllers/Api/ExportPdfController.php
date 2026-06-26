<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\InstansiSetting;
use App\Models\Tender;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
class ExportPdfController extends Controller {
    private function settings(): array {
        return InstansiSetting::allAsArray();
    }
    /** Rekap Tender - semua peserta & penawaran */
    public function rekapTender(Tender $tender): Response {
        $tender->load(['participants.vendor.user','bids.vendor.user','result','creator:id,name']);
        $pdf = Pdf::loadView('pdf.rekap-tender',['tender'=>$tender,'settings'=>$this->settings()])
            ->setPaper('a4','portrait');
        $filename = 'Rekap-Tender-'.\Illuminate\Support\Str::slug($tender->title).'-'.now()->format('Ymd').'.pdf';
        return $pdf->download($filename);
    }
    /** Berita Acara Evaluasi */
    public function beritaAcaraEvaluasi(Tender $tender): Response {
        $tender->load(['bids.evaluations.criteria','bids.vendor.user','evaluationCriteria','result']);
        $docNo = 'BA-EVAL/'.now()->format('m').'/'.now()->format('Y').'/'.str_pad($tender->id,3,'0',STR_PAD_LEFT);
        $pdf = Pdf::loadView('pdf.ba-evaluasi',['tender'=>$tender,'doc_no'=>$docNo,'settings'=>$this->settings()])
            ->setPaper('a4','portrait');
        return $pdf->download('BA-Evaluasi-'.\Illuminate\Support\Str::slug($tender->title).'.pdf');
    }
    /** Berita Acara Penetapan Pemenang */
    public function beritaAcaraPemenang(Tender $tender): Response {
        $tender->load(['result.winnerVendor.user','bids.vendor.user','creator:id,name']);
        $docNo = 'BA-PWN/'.now()->format('m').'/'.now()->format('Y').'/'.str_pad($tender->id,3,'0',STR_PAD_LEFT);
        $pdf = Pdf::loadView('pdf.ba-pemenang',['tender'=>$tender,'doc_no'=>$docNo,'settings'=>$this->settings()])
            ->setPaper('a4','portrait');
        return $pdf->download('BA-Pemenang-'.\Illuminate\Support\Str::slug($tender->title).'.pdf');
    }
    /** PDF Kontrak */
    public function contractPdf(Contract $contract): Response {
        $contract->load(['tender','vendor.user','creator:id,name','deliveries']);
        $pdf = Pdf::loadView('pdf.contract',['contract'=>$contract,'settings'=>$this->settings()])
            ->setPaper('a4','portrait');
        $safeNumber = str_replace(['/', '\\'], '-', $contract->contract_number);
        return $pdf->download('Kontrak-'.$safeNumber.'.pdf');
    }
}
