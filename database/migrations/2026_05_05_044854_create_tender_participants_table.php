<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tender_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->dateTime('joined_at');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tender_id', 'vendor_id']);
            $table->index('tender_id');
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_participants');
    }
};
