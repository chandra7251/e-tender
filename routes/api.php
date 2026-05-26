<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\TenderAnnouncementController;
use App\Http\Controllers\Api\TenderController;
use App\Http\Controllers\Api\TenderParticipantController;
use App\Http\Controllers\Api\TenderResultController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\VendorDocumentController;
use App\Http\Controllers\Api\VendorProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Vendor Mobile App
|--------------------------------------------------------------------------
|
| Auth strategy: JWT Bearer token via tymon/jwt-auth.
| Guard: auth:api (driver=jwt, defined in config/auth.php)
|
*/

// ── Public: Auth ─────────────────────────────────────────────────────────────
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('register',        [AuthController::class, 'register'])->name('register')->middleware('throttle:10,1');
    Route::post('login',           [AuthController::class, 'login'])->name('login')->middleware('throttle:5,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password')->middleware('throttle:3,1');
    Route::post('reset-password',  [AuthController::class, 'resetPassword'])->name('reset-password');
    Route::post('refresh',         [AuthController::class, 'refresh'])->name('refresh')->middleware('throttle:10,1');
});

// ── Public: Tender listing (vendor can browse without login) ──────────────────
Route::get('tenders',        [TenderController::class, 'index'])->name('api.tenders.index');
Route::get('tenders/{tender}', [TenderController::class, 'show'])->name('api.tenders.show');

// ── Protected: Require vendor JWT token ─────────────────────────────────────────
Route::middleware('auth:api')->group(function () {

    // Auth actions
    Route::post('auth/logout',         [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('auth/me',              [AuthController::class, 'me'])->name('api.auth.me');
    Route::put('auth/change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password');

    // Vendor Profile (semua vendor bisa akses, termasuk pending)
    Route::get('vendors/me',     [VendorProfileController::class, 'show'])->name('api.vendors.me');
    Route::put('vendors/me',     [VendorProfileController::class, 'update'])->name('api.vendors.me.update');
    Route::get('vendors/status', [VendorProfileController::class, 'status'])->name('api.vendors.status');

    // Vendor Documents (semua vendor bisa upload dokumen untuk verifikasi)
    Route::get('vendors/documents',                               [VendorDocumentController::class, 'index'])->name('api.vendors.documents.index');
    Route::post('vendors/documents',                              [VendorDocumentController::class, 'store'])->name('api.vendors.documents.store');
    Route::get('vendors/documents/{document}/download',           [VendorDocumentController::class, 'download'])->name('api.vendors.documents.download');

    // Lihat announcements & results (semua vendor bisa lihat)
    Route::get('tenders/{tender}/announcements', [TenderAnnouncementController::class, 'index'])->name('api.tenders.announcements');
    Route::get('tenders/{tender}/result',        [TenderResultController::class, 'show'])->name('api.tenders.result');
    Route::get('tenders/{tender}/winner',        [TenderResultController::class, 'winner'])->name('api.tenders.winner');

    // Cek status kepesertaan tender (semua vendor bisa akses, termasuk pending)
    Route::get('tenders/{tender}/participants/check', [TenderParticipantController::class, 'check'])->name('api.tenders.participants.check');

    // Tender & hasil yang diikuti vendor yang login
    Route::get('vendors/tenders', [VendorController::class, 'myTenders'])->name('api.vendors.my-tenders');
    Route::get('vendors/results', [VendorController::class, 'myResults'])->name('api.vendors.my-results');

    // ── Hanya vendor APPROVED yang bisa join & bid ────────────────────────────
    Route::middleware('vendor.approved')->group(function () {
        Route::post('tenders/{tender}/participants',  [TenderParticipantController::class, 'store'])->name('api.tenders.join');
        Route::get('tenders/{tender}/bids/me',        [BidController::class, 'myBid'])->name('api.tenders.bids.me');
        Route::post('tenders/{tender}/bids',          [BidController::class, 'store'])->name('api.tenders.bids.store');
        Route::put('tenders/{tender}/bids/{bid}',     [BidController::class, 'update'])->name('api.tenders.bids.update');
    });
});

