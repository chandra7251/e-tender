<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('blockchain_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tender_id');
            $table->string('event_type', 50);
            $table->char('payload_hash', 64);
            $table->char('block_hash', 64)->unique();
            $table->char('prev_hash', 64);
            $table->text('payload');
            $table->string('network', 20)->default('local');
            $table->string('tx_hash', 100)->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamps();
            $table->index('tender_id');
            $table->index('event_type');
        });
    }
    public function down(): void { Schema::dropIfExists('blockchain_records'); }
};
