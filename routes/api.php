<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FcmController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\TenderAnnouncementController;
use App\Http\Controllers\Api\TenderController;
use App\Http\Controllers\Api\TenderParticipantController;
use App\Http\Controllers\Api\TenderResultController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\VendorDocumentController;
use App\Http\Controllers\Api\VendorProfileController;
use App\Http\Controllers\Api\VendorSubmissionController;
use App\Http\Controllers\Api\AdminSubmissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('register',        [AuthController::class, 'register'])->name('register')->middleware('throttle:10,1');
    Route::post('login',           [AuthController::class, 'login'])->name('login')->middleware('throttle:5,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password')->middleware('throttle:3,1');
    Route::post('reset-password',  [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::post('refresh',         [AuthController::class, 'refresh'])->name('refresh')->middleware('throttle:10,1');
});

Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('email/resend', [AuthController::class, 'resendVerificationEmail'])->name('verification.resend')->middleware('throttle:3,1');

Route::get('tenders',        [TenderController::class, 'index'])->name('api.tenders.index');
Route::get('tenders/{tender}', [TenderController::class, 'show'])->name('api.tenders.show');

Route::middleware('auth:api')->group(function () {

    Route::delete('auth/delete-account', [AuthController::class, 'deleteAccount'])->name('api.auth.delete-account');
Route::post('auth/logout',         [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('auth/me',              [AuthController::class, 'me'])->name('api.auth.me');

    Route::put('auth/change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password')->middleware('throttle:5,1');

    Route::get('notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::delete('notifications/all', [\App\Http\Controllers\Api\NotificationController::class, 'destroyAll'])->name('api.notifications.destroyAll');
    Route::patch('notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])->name('api.notifications.readAll');
    Route::patch('notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::delete('notifications/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy'])->name('api.notifications.destroy');

    Route::get('vendors/me',     [VendorProfileController::class, 'show'])->name('api.vendors.me');
    Route::put('vendors/me',     [VendorProfileController::class, 'update'])->name('api.vendors.me.update');
    Route::get('vendors/status', [VendorProfileController::class, 'status'])->name('api.vendors.status');
    Route::get('vendors/my-rating', [VendorProfileController::class, 'myRating'])->name('api.vendors.my-rating');
    Route::post('device/fcm-token',     [FcmController::class, 'register'])->name('api.device.fcm.register');
    Route::delete('device/fcm-token',   [FcmController::class, 'unregister'])->name('api.device.fcm.unregister');

    Route::get('vendors/documents',                               [VendorDocumentController::class, 'index'])->name('api.vendors.documents.index');
    Route::post('vendors/documents',                              [VendorDocumentController::class, 'store'])->name('api.vendors.documents.store');
    Route::get('vendors/documents/{document}/download',           [VendorDocumentController::class, 'download'])->name('api.vendors.documents.download');

    Route::get('tenders/{tender}/announcements', [TenderAnnouncementController::class, 'index'])->name('api.tenders.announcements');
    Route::get('tenders/{tender}/result',        [TenderResultController::class, 'show'])->name('api.tenders.result');
    Route::get('tenders/{tender}/winner',        [TenderResultController::class, 'winner'])->name('api.tenders.winner');

    Route::get('tenders/{tender}/participants/check', [TenderParticipantController::class, 'check'])->name('api.tenders.participants.check');

    Route::get('vendors/tenders', [VendorController::class, 'myTenders'])->name('api.vendors.my-tenders');
    Route::get('vendors/results', [VendorController::class, 'myResults'])->name('api.vendors.my-results');

    Route::get('tenders/{tender}/penawaran/me', [BidController::class, 'myBid'])->name('api.tenders.bids.me');

    Route::middleware('vendor.approved')->group(function () {
        Route::post('tenders/{tender}/participants',  [TenderParticipantController::class, 'store'])->name('api.tenders.join');
        Route::post('tenders/{tender}/penawaran',          [BidController::class, 'store'])->name('api.tenders.bids.store');
        Route::put('tenders/{tender}/penawaran/{bid}',     [BidController::class, 'update'])->name('api.tenders.bids.update');
    });
});

Route::middleware('auth:api')->prefix('vendor')->name('api.vendor.')->group(function () {
    Route::get('submissions',      [VendorSubmissionController::class, 'index'])->name('vendor.submissions.index');
    Route::get('submissions/{id}', [VendorSubmissionController::class, 'show'])->name('vendor.submissions.show');
    Route::post('submissions',     [VendorSubmissionController::class, 'store'])->name('vendor.submissions.store');
});

Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {
    Route::get('submissions',              [AdminSubmissionController::class, 'index'])->name('admin.submissions.index');
    Route::get('submissions/{id}',         [AdminSubmissionController::class, 'show'])->name('admin.submissions.show');
    Route::patch('submissions/{id}/approve', [AdminSubmissionController::class, 'approve'])->name('admin.submissions.approve');
    Route::patch('submissions/{id}/reject',  [AdminSubmissionController::class, 'reject'])->name('admin.submissions.reject');

    // Evaluation & Ranking API
    Route::get('tenders/{tender}/ranking',    [\App\Http\Controllers\Api\EvaluationApiController::class, 'ranking'])->name('tenders.ranking');
    Route::get('dashboard/stats',             [\App\Http\Controllers\Api\DashboardApiController::class, 'stats'])->name('dashboard.stats');
});

// ─── Public Settings (White-Label) ───────────────────────────────────────────
Route::get('settings/public', [\App\Http\Controllers\Api\InstansiSettingController::class, 'publicSettings'])->name('api.settings.public');

// ─── Tender Items / BQ (public read) ─────────────────────────────────────────
Route::get('tenders/{tender}/items', [\App\Http\Controllers\Api\TenderItemController::class, 'index'])->name('api.tenders.items.index');

Route::middleware('auth:api')->group(function () {

    // ─── Vendor: Sanggahan & Banding ──────────────────────────────────────────
    Route::get('complaints',                     [\App\Http\Controllers\Api\TenderComplaintController::class, 'index'])->name('api.complaints.index');
    Route::post('tenders/{tender}/complaints',   [\App\Http\Controllers\Api\TenderComplaintController::class, 'store'])->name('api.tenders.complaints.store');

    // ─── Vendor: Kualifikasi ──────────────────────────────────────────────────
    Route::get('vendors/qualification',         [\App\Http\Controllers\Api\VendorQualificationController::class, 'show'])->name('api.vendors.qualification');
    Route::put('vendors/qualification',         [\App\Http\Controllers\Api\VendorQualificationController::class, 'updateQualification'])->name('api.vendors.qualification.update');
    Route::post('vendors/certifications',       [\App\Http\Controllers\Api\VendorQualificationController::class, 'storeCertification'])->name('api.vendors.certifications.store');
    Route::delete('vendors/certifications/{id}',[\App\Http\Controllers\Api\VendorQualificationController::class, 'destroyCertification'])->name('api.vendors.certifications.destroy');

    // ─── Vendor: Kontrak ──────────────────────────────────────────────────────
    Route::get('vendor/contracts',              [\App\Http\Controllers\Api\ContractController::class, 'vendorContracts'])->name('api.vendor.contracts.index');
    Route::get('contracts/{contract}',          [\App\Http\Controllers\Api\ContractController::class, 'show'])->name('api.contracts.show');
    Route::patch('contracts/{contract}/sign-vendor', [\App\Http\Controllers\Api\ContractController::class, 'vendorSign'])->name('api.contracts.sign-vendor');

    // ─── Vendor: Contract Delivery ────────────────────────────────────────────
    Route::patch('contracts/{contract}/deliveries/{delivery}/submit',
        [\App\Http\Controllers\Api\ContractDeliveryController::class, 'vendorUpdate'])->name('api.contracts.deliveries.submit');
});

Route::middleware(['auth:api', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {

    // ─── Admin: Instansi Settings ─────────────────────────────────────────────
    Route::get('settings',                      [\App\Http\Controllers\Api\InstansiSettingController::class, 'index'])->name('settings.index');
    Route::put('settings',                      [\App\Http\Controllers\Api\InstansiSettingController::class, 'update'])->name('settings.update');
    Route::post('settings/logo',                [\App\Http\Controllers\Api\InstansiSettingController::class, 'uploadLogo'])->name('settings.logo');

    // ─── Admin: Sanggahan ─────────────────────────────────────────────────────
    Route::get('complaints',                    [\App\Http\Controllers\Api\TenderComplaintController::class, 'adminIndex'])->name('complaints.index');
    Route::patch('complaints/{complaint}/respond', [\App\Http\Controllers\Api\TenderComplaintController::class, 'respond'])->name('complaints.respond');

    // ─── Admin: Tender Items / BQ ─────────────────────────────────────────────
    Route::post('tenders/{tender}/items',        [\App\Http\Controllers\Api\TenderItemController::class, 'sync'])->name('tenders.items.sync');
    Route::delete('tenders/{tender}/items/{item}',[\App\Http\Controllers\Api\TenderItemController::class, 'destroy'])->name('tenders.items.destroy');

    // ─── Admin: Kontrak Digital ───────────────────────────────────────────────
    Route::get('contracts',                      [\App\Http\Controllers\Api\ContractController::class, 'index'])->name('contracts.index');
    Route::post('contracts',                     [\App\Http\Controllers\Api\ContractController::class, 'store'])->name('contracts.store');
    Route::patch('contracts/{contract}/send',    [\App\Http\Controllers\Api\ContractController::class, 'sendToVendor'])->name('contracts.send');
    Route::patch('contracts/{contract}/sign-admin', [\App\Http\Controllers\Api\ContractController::class, 'adminSign'])->name('contracts.sign-admin');
    Route::patch('contracts/{contract}/complete',[\App\Http\Controllers\Api\ContractController::class, 'complete'])->name('contracts.complete');

    // ─── Admin: Contract Deliveries ───────────────────────────────────────────
    Route::post('contracts/{contract}/deliveries',  [\App\Http\Controllers\Api\ContractDeliveryController::class, 'store'])->name('contracts.deliveries.store');
    Route::patch('contracts/{contract}/deliveries/{delivery}/verify',
        [\App\Http\Controllers\Api\ContractDeliveryController::class, 'adminVerify'])->name('contracts.deliveries.verify');

    // ─── Admin: Webhook ───────────────────────────────────────────────────────
    Route::get('webhooks',                       [\App\Http\Controllers\Api\WebhookController::class, 'index'])->name('webhooks.index');
    Route::post('webhooks',                      [\App\Http\Controllers\Api\WebhookController::class, 'store'])->name('webhooks.store');
    Route::delete('webhooks/{webhook}',          [\App\Http\Controllers\Api\WebhookController::class, 'destroy'])->name('webhooks.destroy');
    Route::patch('webhooks/{webhook}/toggle',    [\App\Http\Controllers\Api\WebhookController::class, 'toggle'])->name('webhooks.toggle');

    // ─── Admin: Export PDF ────────────────────────────────────────────────────
    Route::get('tenders/{tender}/export/rekap',  [\App\Http\Controllers\Api\ExportPdfController::class, 'rekapTender'])->name('tenders.export.rekap');
    Route::get('tenders/{tender}/export/ba-evaluasi', [\App\Http\Controllers\Api\ExportPdfController::class, 'beritaAcaraEvaluasi'])->name('tenders.export.ba-evaluasi');
    Route::get('tenders/{tender}/export/ba-pemenang', [\App\Http\Controllers\Api\ExportPdfController::class, 'beritaAcaraPemenang'])->name('tenders.export.ba-pemenang');
    Route::get('contracts/{contract}/export/pdf',[\App\Http\Controllers\Api\ExportPdfController::class, 'contractPdf'])->name('contracts.export.pdf');
});

// ─── E-Catalogue (Public) ─────────────────────────────────────────────────────
Route::get('catalogue',            [\App\Http\Controllers\Api\VendorCatalogueController::class, 'index']);
Route::get('catalogue/categories', [\App\Http\Controllers\Api\VendorCatalogueController::class, 'categories']);
Route::get('catalogue/{id}',       [\App\Http\Controllers\Api\VendorCatalogueController::class, 'show'])->where('id','[0-9]+');

// ─── E-Catalogue (Vendor Auth) ────────────────────────────────────────────────
Route::middleware('auth:api')->prefix('vendor/catalogue')->name('api.vendor.catalogue.')->group(function () {
    Route::get('/',      [\App\Http\Controllers\Api\VendorCatalogueController::class, 'myItems'])->name('index');
    Route::post('/',     [\App\Http\Controllers\Api\VendorCatalogueController::class, 'store'])->name('store');
    Route::put('/{id}',  [\App\Http\Controllers\Api\VendorCatalogueController::class, 'update'])->name('update');
    Route::delete('/{id}',[\App\Http\Controllers\Api\VendorCatalogueController::class, 'destroy'])->name('destroy');
});

// ─── AI / Machine Learning ─────────────────────────────────────────────────
Route::prefix('ai')->middleware('auth:api')->group(function () {
    Route::post('predict-price',            [\App\Http\Controllers\Api\AiController::class, 'predictPrice']);
    Route::post('detect-anomaly',           [\App\Http\Controllers\Api\AiController::class, 'detectAnomaly']);
    Route::get('score-vendors/{tenderId}',  [\App\Http\Controllers\Api\AiController::class, 'scoreVendors']);
    Route::get('analyze/{tenderId}',        [\App\Http\Controllers\Api\AiController::class, 'analyzeTender']);
});

// ─── Blockchain Verification ───────────────────────────────────────────────
Route::get('blockchain/verify',          [\App\Http\Controllers\Api\BlockchainController::class, 'publicVerify']);
Route::middleware('auth:api')->prefix('blockchain')->group(function () {
    Route::post('record',                [\App\Http\Controllers\Api\BlockchainController::class, 'record']);
    Route::get('verify/{id}',           [\App\Http\Controllers\Api\BlockchainController::class, 'verify']);
    Route::get('chain/{tenderId}',      [\App\Http\Controllers\Api\BlockchainController::class, 'tenderChain']);
});

// ─── Payment Gateway ───────────────────────────────────────────────────────
Route::post('payment/notification',    [\App\Http\Controllers\Api\PaymentController::class, 'notification']);
Route::get('payment/client-key',       [\App\Http\Controllers\Api\PaymentController::class, 'clientKey']);
Route::middleware('auth:api')->prefix('payment')->group(function () {
    Route::post('deposit',             [\App\Http\Controllers\Api\PaymentController::class, 'createDeposit']);
    Route::get('tender/{tenderId}',    [\App\Http\Controllers\Api\PaymentController::class, 'tenderPayments']);
    Route::post('refund/{paymentId}',  [\App\Http\Controllers\Api\PaymentController::class, 'refundDeposit']);
});

