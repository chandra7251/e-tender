<?php
$c = App\Models\Contract::first();
if ($c) {
    try {
        $pdf = app('App\Http\Controllers\Api\ExportPdfController')->contractPdf($c);
        echo "Contract PDF OK, size: " . strlen($pdf->getContent()) . "\n";
    } catch (\Exception $e) {
        echo "Contract PDF Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

$t = App\Models\Tender::first();
if ($t) {
    try {
        $pdf2 = app('App\Http\Controllers\Api\ExportPdfController')->rekapTender($t);
        echo "Rekap Tender PDF OK, size: " . strlen($pdf2->getContent()) . "\n";
    } catch (\Exception $e) {
        echo "Rekap Tender Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    }

    try {
        $pdf3 = app('App\Http\Controllers\Api\ExportPdfController')->beritaAcaraEvaluasi($t);
        echo "BA Evaluasi PDF OK, size: " . strlen($pdf3->getContent()) . "\n";
    } catch (\Exception $e) {
        echo "BA Evaluasi Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    }

    try {
        $pdf4 = app('App\Http\Controllers\Api\ExportPdfController')->beritaAcaraPemenang($t);
        echo "BA Pemenang PDF OK, size: " . strlen($pdf4->getContent()) . "\n";
    } catch (\Exception $e) {
        echo "BA Pemenang Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}

// Test CSV
try {
    $req = new \Illuminate\Http\Request();
    $csv1 = app('App\Http\Controllers\Admin\ReportController')->exportTenders($req);
    ob_start();
    $csv1->sendContent();
    $output = ob_get_clean();
    echo "Tender CSV OK, size: " . strlen($output) . "\n";
} catch (\Exception $e) {
    echo "Tender CSV Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}

try {
    $req = new \Illuminate\Http\Request();
    $csv2 = app('App\Http\Controllers\Admin\ReportController')->exportVendors($req);
    ob_start();
    $csv2->sendContent();
    $output = ob_get_clean();
    echo "Vendor CSV OK, size: " . strlen($output) . "\n";
} catch (\Exception $e) {
    echo "Vendor CSV Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}

try {
    $req = new \Illuminate\Http\Request();
    $csv3 = app('App\Http\Controllers\Admin\ReportController')->exportAuditLogs($req);
    ob_start();
    $csv3->sendContent();
    $output = ob_get_clean();
    echo "AuditLog CSV OK, size: " . strlen($output) . "\n";
} catch (\Exception $e) {
    echo "AuditLog CSV Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}
