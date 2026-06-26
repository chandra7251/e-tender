<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bid_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bid_id')->constrained()->cascadeOnDelete();
            $table->foreignId('criteria_id')->constrained('tender_evaluation_criteria')->cascadeOnDelete();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2);     // 0 - max_score
            $table->text('notes')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            $table->unique(['bid_id', 'criteria_id'], 'bid_criteria_unique');
            $table->index(['tender_id', 'vendor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bid_evaluations');
    }
};
