<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Solusi tie-breaker untuk bid dengan jumlah, jam, dan detik yang sama.
     *
     * Strategi dua lapis:
     * 1. submitted_at → dateTime(6): presisi microsecond (10^-6 detik)
     *    Praktis mustahil dua request punya microsecond sama dari jaringan berbeda.
     *
     * 2. ulid (kolom baru): ULID di-generate saat bid dibuat.
     *    ULID = 48-bit timestamp milidetik + 80-bit random.
     *    Bersifat sortable lexicographically → yang lebih kecil = masuk lebih awal.
     *    Ini adalah tie-breaker akhir jika submitted_at microsecond pun sama.
     *
     * Logika penentuan pemenang:
     *   ORDER BY bid_amount ASC, submitted_at ASC, ulid ASC
     *   → LIMIT 1
     */
    public function up(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            // Ubah submitted_at ke presisi microsecond (6 desimal)
            // dateTime(6) = DATETIME(6) di MySQL → simpan sampai 0.000001 detik
            $table->dateTime('submitted_at', 6)->change();

            // Tambah kolom ulid sebagai tie-breaker deterministik
            // Digenerate otomatis oleh model saat bid dibuat (bukan primary key,
            // sehingga tidak ada breaking change pada foreign key yang ada)
            $table->string('ulid', 26)->nullable()->unique()->after('notes');

            // Index gabungan untuk query pemenang yang efisien
            $table->index(['tender_id', 'bid_amount', 'submitted_at', 'ulid'], 'bids_winner_idx');
        });

        // Isi ulid untuk data yang sudah ada
        DB::table('bids')->whereNull('ulid')->orderBy('id')->each(function ($bid) {
            DB::table('bids')->where('id', $bid->id)->update([
                'ulid' => (string) Str::ulid(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropIndex('bids_winner_idx');
            $table->dropColumn('ulid');
            $table->dateTime('submitted_at')->change();
        });
    }
};
