<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tender_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path', 500);
            $table->timestamps();
        });

        // Pindahkan data foto lama ke tabel baru
        $tendersWithPhotos = \Illuminate\Support\Facades\DB::table('tenders')
            ->whereNotNull('photo_path')
            ->get(['id', 'photo_path']);

        foreach ($tendersWithPhotos as $tender) {
            \Illuminate\Support\Facades\DB::table('tender_photos')->insert([
                'tender_id'  => $tender->id,
                'photo_path' => $tender->photo_path,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Hapus kolom photo_path dari tabel tenders
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_photos');
    }
};
