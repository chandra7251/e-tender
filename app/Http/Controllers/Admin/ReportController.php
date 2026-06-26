<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tender;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    // Separator titik koma agar langsung rapi di Excel Indonesia / LibreOffice
    private const SEP = ';';

    public function index(): View
    {
        return view('admin.reports.index');
    }

    // -------------------------------------------------------------------------
    // Helper: tulis satu baris CSV pakai separator titik koma
    // Setiap nilai dibungkus kutip ganda dan karakter kutip di-escape
    // -------------------------------------------------------------------------
    private function csvLine(array $fields): string
    {
        $escaped = array_map(function ($v) {
            $v = (string) ($v ?? '');
            // escape internal double-quote
            $v = str_replace('"', '""', $v);
            return '"' . $v . '"';
        }, $fields);
        return implode(self::SEP, $escaped) . "\r\n";
    }

    // -------------------------------------------------------------------------
    // Helper: buka stream response CSV
    // -------------------------------------------------------------------------
    private function streamCsv(string $filename, callable $writer): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
            'Pragma'              => 'no-cache',
        ];

        return response()->streamDownload(function () use ($writer) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM agar Excel buka langsung dengan encoding benar
            fwrite($handle, "\xEF\xBB\xBF");
            // Baris sep= agar Excel otomatis pakai titik koma
            fwrite($handle, 'sep=;' . "\r\n");
            $writer($handle);
            fclose($handle);
        }, $filename, $headers);
    }

    // =========================================================================
    // EXPORT TENDER
    // =========================================================================
    public function exportTenders(Request $request): StreamedResponse
    {
        $q = Tender::with(['creator', 'result.winner', 'purchaseOrder'])->latest();

        if ($request->filled('status'))    $q->where('status', $request->status);
        if ($request->filled('date_from')) $q->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $q->whereDate('created_at', '<=', $request->date_to);

        $tenders = $q->get();

        ActivityLog::log(
            action: 'export_tenders',
            module: 'report',
            description: "Export laporan tender ({$tenders->count()} data).",
        );

        $filename = 'laporan-tender-' . now()->format('Y-m-d') . '.csv';

        return $this->streamCsv($filename, function ($handle) use ($tenders) {
            // Header kolom
            fwrite($handle, $this->csvLine([
                'No', 'Judul Tender', 'Status', 'Tanggal Mulai', 'Tanggal Selesai',
                'Bidding Mulai', 'Bidding Selesai', 'Jumlah Peserta', 'Jumlah Bid',
                'Nama Pemenang', 'Perusahaan Pemenang', 'Nilai Pemenang',
                'No. PO', 'Dibuat Oleh', 'Dibuat Pada',
            ]));

            foreach ($tenders as $i => $t) {
                fwrite($handle, $this->csvLine([
                    $i + 1,
                    $t->title,
                    strtoupper($t->status),
                    $t->start_date?->format('d/m/Y H:i'),
                    $t->end_date?->format('d/m/Y H:i'),
                    $t->bidding_start?->format('d/m/Y H:i'),
                    $t->bidding_end?->format('d/m/Y H:i'),
                    $t->participants()->count(),
                    $t->bids()->count(),
                    $t->result?->winner?->user?->name ?? '-',
                    $t->result?->winner?->company_name ?? '-',
                    $t->result ? 'Rp ' . number_format($t->result->winning_bid_amount, 0, ',', '.') : '-',
                    $t->purchaseOrder?->po_number ?? '-',
                    $t->creator?->name ?? '-',
                    $t->created_at?->format('d/m/Y H:i'),
                ]));
            }
        });
    }

    // =========================================================================
    // EXPORT VENDOR
    // =========================================================================
    public function exportVendors(Request $request): StreamedResponse
    {
        $q = Vendor::with(['user', 'documents', 'tenderParticipants', 'wonResults'])->latest();

        if ($request->filled('status')) $q->where('verification_status', $request->status);

        $vendors = $q->get();

        ActivityLog::log(
            action: 'export_vendors',
            module: 'report',
            description: "Export laporan vendor ({$vendors->count()} data).",
        );

        $filename = 'laporan-vendor-' . now()->format('Y-m-d') . '.csv';

        return $this->streamCsv($filename, function ($handle) use ($vendors) {
            fwrite($handle, $this->csvLine([
                'No', 'Nama Perusahaan', 'Nama Kontak', 'Email', 'Telepon',
                'Alamat', 'Status Verifikasi', 'Jumlah Dokumen',
                'Tender Diikuti', 'Tender Dimenangkan', 'Terdaftar Pada',
            ]));

            foreach ($vendors as $i => $v) {
                fwrite($handle, $this->csvLine([
                    $i + 1,
                    $v->company_name,
                    $v->user?->name ?? '-',
                    $v->user?->email ?? '-',
                    $v->phone,
                    $v->address,
                    strtoupper($v->verification_status),
                    $v->documents->count(),
                    $v->tenderParticipants->count(),
                    $v->wonResults->count(),
                    $v->created_at?->format('d/m/Y H:i'),
                ]));
            }
        });
    }

    // =========================================================================
    // EXPORT AUDIT LOG
    // =========================================================================
    public function exportAuditLogs(Request $request): StreamedResponse
    {
        $q = ActivityLog::with('user')->latest('performed_at');

        if ($request->filled('date_from')) $q->whereDate('performed_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $q->whereDate('performed_at', '<=', $request->date_to);
        if ($request->filled('module'))    $q->where('module', $request->module);

        $logs = $q->get();

        $filename = 'audit-log-' . now()->format('Y-m-d') . '.csv';

        return $this->streamCsv($filename, function ($handle) use ($logs) {
            fwrite($handle, $this->csvLine([
                'No', 'Waktu', 'User', 'Role', 'Modul', 'Aksi', 'Deskripsi', 'IP Address',
            ]));

            foreach ($logs as $i => $log) {
                fwrite($handle, $this->csvLine([
                    $i + 1,
                    $log->performed_at?->format('d/m/Y H:i:s'),
                    $log->user?->name ?? 'System',
                    $log->user?->role ?? '-',
                    $log->module,
                    $log->action,
                    $log->description,
                    $log->ip_address,
                ]));
            }
        });
    }
}
