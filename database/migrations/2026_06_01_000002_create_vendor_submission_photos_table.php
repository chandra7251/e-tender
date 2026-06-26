<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_submission_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_submission_id')
                  ->constrained('vendor_submissions')
                  ->cascadeOnDelete();
            $table->string('photo_path', 500);
            $table->string('photo_url', 500);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_submission_photos');
    }
};
