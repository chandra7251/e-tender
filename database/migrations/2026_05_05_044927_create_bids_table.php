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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->decimal('bid_amount', 15, 2);
            $table->text('notes')->nullable();
            $table->dateTime('submitted_at');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tender_id', 'vendor_id']);
            $table->index('tender_id');
            $table->index('vendor_id');
            $table->index('bid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
