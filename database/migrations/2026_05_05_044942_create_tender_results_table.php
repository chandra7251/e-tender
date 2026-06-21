<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tender_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->unique()->constrained('tenders')->cascadeOnDelete();
            $table->foreignId('winner_vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('winning_bid_id')->constrained('bids')->cascadeOnDelete();
            $table->decimal('winning_bid_amount', 15, 2);
            $table->string('selection_method'); 
            $table->text('notes')->nullable();
            $table->foreignId('decided_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('decided_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index('winner_vendor_id');
            $table->index('decided_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_results');
    }
};
