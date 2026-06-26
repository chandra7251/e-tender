<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('specification');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('aanwijzing_date')->nullable();
            $table->dateTime('bidding_start');
            $table->dateTime('bidding_end');
            $table->string('status')->default('draft'); 
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_by');
            $table->index('status');
            $table->index('bidding_start');
            $table->index('bidding_end');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
