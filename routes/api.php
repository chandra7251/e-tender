<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\TenderAnnouncementController;
use App\Http\Controllers\Api\TenderController;
use App\Http\Controllers\Api\TenderParticipantController;
use App\Http\Controllers\Api\TenderResultController;
use App\Http\Controllers\Api\VendorDocumentController;
use App\Http\Controllers\Api\VendorProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Vendor Mobile App
|--------------------------------------------------------------------------
|
| Auth strategy: Bearer token stored in users.remember_token.
| SWAP NOTE: Replace auth.api middleware with auth:sanctum when Sanctum
| is confirmed. No other changes needed in controllers.
|
*/

// ── Public: Auth ─────────────────────────────────────────────────────────────
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('register',        [AuthController::class, 'register'])->name('register');
    Route::post('login',           [AuthController::class, 'login'])->name('login');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('reset-password',  [AuthController::class, 'resetPassword'])->name('reset-password');
});

// ── Public: Tender listing (vendor can browse without login) ──────────────────
Route::get('tenders',        [TenderController::class, 'index'])->name('api.tenders.index');
Route::get('tenders/{tender}', [TenderController::class, 'show'])->name('api.tenders.show');

// ── Protected: Require vendor auth token ─────────────────────────────────────
Route::middleware('auth.api')->group(function () {

    // Auth actions
    Route::post('auth/logout',          [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('auth/me',               [AuthController::class, 'me'])->name('api.auth.me');
    Route::put('auth/change-password',  [AuthController::class, 'changePassword'])->name('api.auth.change-password');

    // Vendor Profile
    Route::get('vendors/me',     [VendorProfileController::class, 'show'])->name('api.vendors.me');
    Route::put('vendors/me',     [VendorProfileController::class, 'update'])->name('api.vendors.me.update');
    Route::get('vendors/status', [VendorProfileController::class, 'status'])->name('api.vendors.status');

    // Vendor Documents
    Route::get('vendors/documents',  [VendorDocumentController::class, 'index'])->name('api.vendors.documents.index');
    Route::post('vendors/documents', [VendorDocumentController::class, 'store'])->name('api.vendors.documents.store');

    // Tender actions (protected)
    Route::post('tenders/{tender}/participants',   [TenderParticipantController::class, 'store'])->name('api.tenders.join');
    Route::get('tenders/{tender}/announcements',   [TenderAnnouncementController::class, 'index'])->name('api.tenders.announcements');

    // Bidding
    Route::get('tenders/{tender}/bids/me',         [BidController::class, 'myBid'])->name('api.tenders.bids.me');
    Route::post('tenders/{tender}/bids',           [BidController::class, 'store'])->name('api.tenders.bids.store');
    Route::put('tenders/{tender}/bids/{bid}',      [BidController::class, 'update'])->name('api.tenders.bids.update');

    // Results
    Route::get('tenders/{tender}/result', [TenderResultController::class, 'show'])->name('api.tenders.result');
    Route::get('tenders/{tender}/winner', [TenderResultController::class, 'winner'])->name('api.tenders.winner');
});
