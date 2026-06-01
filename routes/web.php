<?php

use App\Http\Controllers\Admin\Auth\AdminLoginController;
use App\Http\Controllers\Admin\BidMonitoringController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PurchaseOrderController;
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

// ── Root redirect ────────────────────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('admin.login'));

// ── Admin Routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest
    Route::get('login',  [AdminLoginController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminLoginController::class, 'login'])->name('login.post');

    // Protected
    Route::middleware(['auth', 'role:admin'])->group(function () {

        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Vendor Management ─────────────────────────────────────────────────────────────
        Route::get('vendors',                   [VendorController::class, 'index'])->name('vendors.index');
        Route::get('vendors/{vendor}',          [VendorController::class, 'show'])->name('vendors.show');
        Route::patch('vendors/{vendor}/approve',[VendorController::class, 'approve'])->name('vendors.approve');
        Route::patch('vendors/{vendor}/reject', [VendorController::class, 'reject'])->name('vendors.reject');

        // Download dokumen vendor untuk validasi legalitas
        Route::get('vendors/{vendor}/documents/{document}/download',
            [VendorDocumentController::class, 'download']
        )->name('vendors.documents.download');

        // ── Tender Management ────────────────────────────────────────────────
        Route::get('tenders',               [TenderController::class, 'index'])->name('tenders.index');
        Route::get('tenders/create',        [TenderController::class, 'create'])->name('tenders.create');
        Route::post('tenders',              [TenderController::class, 'store'])->name('tenders.store');
        Route::get('tenders/{tender}',      [TenderController::class, 'show'])->name('tenders.show');
        Route::get('tenders/{tender}/edit', [TenderController::class, 'edit'])->name('tenders.edit');
        Route::put('tenders/{tender}',      [TenderController::class, 'update'])->name('tenders.update');
        Route::patch('tenders/{tender}/status', [TenderController::class, 'updateStatus'])->name('tenders.status');

        // ── Announcement / Aanwijzing ────────────────────────────────────────
        Route::post('tenders/{tender}/announcements', [TenderAnnouncementController::class, 'store'])
            ->name('tenders.announcements.store');

        // ── Participant Monitoring ───────────────────────────────────────────
        Route::get('tenders/{tender}/participants', [TenderParticipantController::class, 'index'])
            ->name('tenders.participants.index');

        // ── Bid Monitoring ───────────────────────────────────────────────────
        Route::get('tenders/{tender}/bids', [BidMonitoringController::class, 'index'])
            ->name('tenders.bids.index');
        Route::get('tenders/{tender}/bids/{bid}/histories', [BidMonitoringController::class, 'histories'])
            ->name('tenders.bids.histories');

        // ── Winner Selection ─────────────────────────────────────────────────
        Route::get('tenders/{tender}/winner/create', [WinnerSelectionController::class, 'create'])
            ->name('tenders.winner.create');
        Route::post('tenders/{tender}/winner', [WinnerSelectionController::class, 'store'])
            ->name('tenders.winner.store');

        // ── Tender Result ────────────────────────────────────────────────────
        Route::get('tenders/{tender}/result',  [TenderResultController::class, 'show'])
            ->name('tenders.result.show');
        Route::patch('tenders/{tender}/finish', [TenderResultController::class, 'finish'])
            ->name('tenders.finish');

        // ── Purchase Order ───────────────────────────────────────────────────
        Route::get('tenders/{tender}/purchase-order/create', [PurchaseOrderController::class, 'create'])
            ->name('tenders.purchase-order.create');
        Route::post('tenders/{tender}/purchase-order', [PurchaseOrderController::class, 'store'])
            ->name('tenders.purchase-order.store');
        Route::get('tenders/{tender}/purchase-order', [PurchaseOrderController::class, 'show'])
            ->name('tenders.purchase-order.show');

        // ── Tender History ───────────────────────────────────────────────────
        Route::get('tenders/{tender}/histories', [TenderHistoryController::class, 'index'])
            ->name('tenders.histories.index');

        // ── Pengajuan Vendor (dari Mobile App) ───────────────────────────────
        Route::get('submissions',                       [SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('submissions/{submission}',          [SubmissionController::class, 'show'])->name('submissions.show');
        Route::patch('submissions/{submission}/approve',[SubmissionController::class, 'approve'])->name('submissions.approve');
        Route::patch('submissions/{submission}/reject', [SubmissionController::class, 'reject'])->name('submissions.reject');

    });
});
