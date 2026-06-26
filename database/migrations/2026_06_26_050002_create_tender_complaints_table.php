<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tender_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->enum('type', ['sanggahan', 'banding'])->default('sanggahan');
            $table->text('reason');
            $table->text('supporting_docs')->nullable(); // JSON array of file paths
            $table->enum('status', ['pending','reviewed','accepted','rejected'])->default('pending');
            $table->text('response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('deadline')->nullable(); // batas waktu ajukan sanggahan
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tender_complaints'); }
};
