<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadikan decided_by nullable (untuk auto-selection oleh sistem)
     * dan selection_method nullable sementara kita tambahkan nilai 'auto'.
     *
     * Sebelumnya decided_by NOT NULL dan constrained ke users,
     * namun sistem otomatis tidak memiliki user_id → perlu nullable.
     */
    public function up(): void
    {
        Schema::table('tender_results', function (Blueprint $table) {
            // Drop foreign key dulu sebelum ubah kolom
            $table->dropForeign(['decided_by']);

            // Jadikan nullable agar bisa diisi null saat sistem otomatis memilih
            $table->unsignedBigInteger('decided_by')->nullable()->change();

            // Tambah kembali foreign key dengan nullable
            $table->foreign('decided_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tender_results', function (Blueprint $table) {
            $table->dropForeign(['decided_by']);
            $table->unsignedBigInteger('decided_by')->nullable(false)->change();
            $table->foreign('decided_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
