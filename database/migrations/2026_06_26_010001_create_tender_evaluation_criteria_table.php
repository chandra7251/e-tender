<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_evaluation_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('name');           // e.g. "Harga Penawaran", "Kualitas Teknis"
            $table->decimal('weight', 5, 2);  // e.g. 40.00 means 40%
            $table->integer('max_score')->default(100);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tender_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_evaluation_criteria');
    }
};
