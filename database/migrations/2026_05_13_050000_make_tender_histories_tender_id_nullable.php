<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FIX HIGH-05: Jadikan tender_histories.tender_id nullable agar event vendor-level
 * (approve/reject) bisa dilog meskipun vendor belum join tender manapun.
 *
 * Sebelumnya: NOT NULL — event vendor yang belum join tender tidak dilog sama sekali.
 * Sesudah:    nullable — event vendor bisa dilog tanpa FK ke tender.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_histories', function (Blueprint $table) {
            // Ubah tender_id menjadi nullable — event vendor-level tidak perlu tender_id.
            $table->foreignId('tender_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tender_histories', function (Blueprint $table) {
            // Rollback: kembalikan ke NOT NULL (perlu pastikan tidak ada null rows dulu).
            $table->foreignId('tender_id')->nullable(false)->change();
        });
    }
};
