<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BidEvaluationController;
use App\Http\Controllers\Admin\BidMonitoringController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EvaluationCriteriaController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TenderAnnouncementController;
use App\Http\Controllers\Admin\TenderController;
use App\Http\Controllers\Admin\TenderHistoryController;
use App\Http\Controllers\Admin\TenderParticipantController;
use App\Http\Controllers\Admin\TenderResultController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\VendorDocumentController;
use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\WinnerSelectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.login'));

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('login',  [AdminLoginController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login'])->name('login.post');

    Route::middleware(['auth', 'role:admin,super_admin,procurement_manager,evaluator,verifikator,auditor'])->group(function () {

        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('vendors',                   [VendorController::class, 'index'])->name('vendors.index');
        Route::get('vendors/{vendor}',          [VendorController::class, 'show'])->name('vendors.show');
        Route::patch('vendors/{vendor}/approve',[VendorController::class, 'approve'])->name('vendors.approve');
        Route::patch('vendors/{vendor}/reject', [VendorController::class, 'reject'])->name('vendors.reject');

        Route::get('vendors/{vendor}/documents/{document}/download',
            [VendorDocumentController::class, 'download']
        )->name('vendors.documents.download');

        Route::get('tenders',               [TenderController::class, 'index'])->name('tenders.index');
        Route::get('tenders/create',        [TenderController::class, 'create'])->name('tenders.create');
        Route::post('tenders',              [TenderController::class, 'store'])->name('tenders.store');
        Route::get('tenders/{tender}',      [TenderController::class, 'show'])->name('tenders.show');
        Route::get('tenders/{tender}/edit', [TenderController::class, 'edit'])->name('tenders.edit');
        Route::put('tenders/{tender}',      [TenderController::class, 'update'])->name('tenders.update');
        Route::delete('tenders/{tender}/photos/{photo}', [TenderController::class, 'deletePhoto'])->name('tenders.photos.destroy');
        Route::patch('tenders/{tender}/status', [TenderController::class, 'updateStatus'])->name('tenders.status');

        Route::post('tenders/{tender}/announcements', [TenderAnnouncementController::class, 'store'])
            ->name('tenders.announcements.store');

        Route::get('tenders/{tender}/participants', [TenderParticipantController::class, 'index'])
            ->name('tenders.participants.index');

        Route::get('tenders/{tender}/bids', [BidMonitoringController::class, 'index'])
            ->name('tenders.bids.index');
        Route::get('tenders/{tender}/bids/{bid}/histories', [BidMonitoringController::class, 'histories'])
            ->name('tenders.bids.histories');

        // ── Evaluation Criteria ──
        Route::get('tenders/{tender}/evaluation-criteria', [EvaluationCriteriaController::class, 'create'])
            ->name('tenders.evaluation-criteria.create');
        Route::post('tenders/{tender}/evaluation-criteria', [EvaluationCriteriaController::class, 'store'])
            ->name('tenders.evaluation-criteria.store');

        // ── Bid Evaluations ──
        Route::get('tenders/{tender}/evaluations', [BidEvaluationController::class, 'create'])
            ->name('tenders.evaluations.create');
        Route::post('tenders/{tender}/evaluations', [BidEvaluationController::class, 'store'])
            ->name('tenders.evaluations.store');
        Route::get('tenders/{tender}/ranking', [BidEvaluationController::class, 'ranking'])
            ->name('tenders.ranking');

        Route::get('tenders/{tender}/winner/create', [WinnerSelectionController::class, 'create'])
            ->name('tenders.winner.create');
        Route::post('tenders/{tender}/winner', [WinnerSelectionController::class, 'store'])
            ->name('tenders.winner.store');

        Route::get('tenders/{tender}/result',  [TenderResultController::class, 'show'])
            ->name('tenders.result.show');
        Route::patch('tenders/{tender}/finish', [TenderResultController::class, 'finish'])
            ->name('tenders.finish');

        Route::get('tenders/{tender}/purchase-order/create', [PurchaseOrderController::class, 'create'])
            ->name('tenders.purchase-order.create');
        Route::post('tenders/{tender}/purchase-order', [PurchaseOrderController::class, 'store'])
            ->name('tenders.purchase-order.store');
        Route::get('tenders/{tender}/purchase-order', [PurchaseOrderController::class, 'show'])
            ->name('tenders.purchase-order.show');
        Route::get('tenders/{tender}/purchase-order/pdf', [PurchaseOrderController::class, 'downloadPdf'])
            ->name('tenders.purchase-order.pdf');

        Route::get('tenders/{tender}/histories', [TenderHistoryController::class, 'index'])
            ->name('tenders.histories.index');

        Route::get('submissions',                       [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('submissions/{submission}',          [SubmissionController::class, 'show'])->name('submissions.show');
        Route::patch('submissions/{submission}/approve',[SubmissionController::class, 'approve'])->name('submissions.approve');
        Route::patch('submissions/{submission}/reject', [SubmissionController::class, 'reject'])->name('submissions.reject');

        // ── Audit Logs ──

        // ─── Settings / White-Label ────────────────────────────────────────────────
        Route::get('settings',       [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::put('settings',       [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/logo', [\App\Http\Controllers\Admin\SettingController::class, 'uploadLogo'])->name('settings.logo');

        // ─── Sanggahan (Complaints) ────────────────────────────────────────────────
        Route::get('complaints',                   [\App\Http\Controllers\Admin\ComplaintController::class, 'index'])->name('complaints.index');
        Route::patch('complaints/{id}/respond',    [\App\Http\Controllers\Admin\ComplaintController::class, 'respond'])->name('complaints.respond');

        // ─── Kontrak Digital ───────────────────────────────────────────────────────
        Route::get('contracts',                    [\App\Http\Controllers\Admin\ContractWebController::class, 'index'])->name('contracts.index');
        Route::get('contracts/{id}',               [\App\Http\Controllers\Admin\ContractWebController::class, 'show'])->name('contracts.show');
        Route::patch('contracts/{id}/send',        [\App\Http\Controllers\Admin\ContractWebController::class, 'send'])->name('contracts.send');
        Route::patch('contracts/{id}/sign',        [\App\Http\Controllers\Admin\ContractWebController::class, 'sign'])->name('contracts.sign');
        Route::patch('contracts/{id}/complete',    [\App\Http\Controllers\Admin\ContractWebController::class, 'complete'])->name('contracts.complete');

        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        // ── Reports & Export ──
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export/tenders', [ReportController::class, 'exportTenders'])->name('reports.export.tenders');
        Route::get('reports/export/vendors', [ReportController::class, 'exportVendors'])->name('reports.export.vendors');
        Route::get('reports/export/audit-logs', [ReportController::class, 'exportAuditLogs'])->name('reports.export.audit-logs');

        // ── Web PDF Export (for admin browser downloads) ──
        Route::get('tenders/{tender}/export/rekap', [\App\Http\Controllers\Api\ExportPdfController::class, 'rekapTender'])->name('tenders.export.rekap');
        Route::get('tenders/{tender}/export/ba-evaluasi', [\App\Http\Controllers\Api\ExportPdfController::class, 'beritaAcaraEvaluasi'])->name('tenders.export.ba-evaluasi');
        Route::get('tenders/{tender}/export/ba-pemenang', [\App\Http\Controllers\Api\ExportPdfController::class, 'beritaAcaraPemenang'])->name('tenders.export.ba-pemenang');
        Route::get('contracts/{contract}/export/pdf', [\App\Http\Controllers\Api\ExportPdfController::class, 'contractPdf'])->name('contracts.export.pdf');


        // ── Vendor Rating ──
        Route::get('tenders/{tender}/vendors/{vendor}/rating', [\App\Http\Controllers\Admin\VendorRatingController::class, 'create'])
            ->name('tenders.vendors.rating.create');
        Route::post('tenders/{tender}/vendors/{vendor}/rating', [\App\Http\Controllers\Admin\VendorRatingController::class, 'store'])
            ->name('tenders.vendors.rating.store');
        Route::get('vendors/{vendor}/ratings', [\App\Http\Controllers\Admin\VendorRatingController::class, 'vendorRatings'])
            ->name('vendors.ratings');

        // ── Vendor Blacklist ──
        Route::get('vendors-blacklist', [\App\Http\Controllers\Admin\VendorBlacklistController::class, 'index'])
            ->name('vendors.blacklist.index');
        Route::post('vendors/{vendor}/blacklist', [\App\Http\Controllers\Admin\VendorBlacklistController::class, 'blacklist'])
            ->name('vendors.blacklist');
        Route::post('vendors/{vendor}/unblacklist', [\App\Http\Controllers\Admin\VendorBlacklistController::class, 'unblacklist'])
            ->name('vendors.unblacklist');

        // ── Two-Envelope Evaluation (Evaluasi 2 Amplop) ──
        Route::get('tenders/{tender}/envelope/technical', [\App\Http\Controllers\Admin\TwoEnvelopeController::class, 'technical'])
            ->name('tenders.envelope.technical');
        Route::post('tenders/{tender}/envelope/technical', [\App\Http\Controllers\Admin\TwoEnvelopeController::class, 'saveTechnical'])
            ->name('tenders.envelope.technical.store');
        Route::get('tenders/{tender}/envelope/price', [\App\Http\Controllers\Admin\TwoEnvelopeController::class, 'price'])
            ->name('tenders.envelope.price');
        Route::get('tenders/{tender}/envelope/ranking', [\App\Http\Controllers\Admin\TwoEnvelopeController::class, 'combinedRanking'])
            ->name('tenders.envelope.ranking');

    });
});

// ─── Multi-Role: Admin Users Management ──────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,super_admin'])->group(function () {
    

    Route::resource('users', \App\Http\Controllers\Admin\AdminUserController::class)
         ->except(['show']);

    // ─── E-Catalogue Admin ────────────────────────────────────────────────────
    Route::get('catalogue',           [\App\Http\Controllers\Admin\CatalogueController::class, 'index'])->name('catalogue.index');
    Route::get('catalogue/{id}',      [\App\Http\Controllers\Admin\CatalogueController::class, 'show'])->name('catalogue.show');
    Route::patch('catalogue/{id}/toggle', [\App\Http\Controllers\Admin\CatalogueController::class, 'toggleActive'])->name('catalogue.toggle');
});
