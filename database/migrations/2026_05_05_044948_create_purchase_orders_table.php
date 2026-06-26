<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_result_id')->constrained('tender_results')->cascadeOnDelete();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('po_number')->unique();
            $table->decimal('amount', 15, 2);
            $table->date('issued_date');
            $table->text('notes')->nullable();
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tender_id');
            $table->index('vendor_id');
            $table->index('generated_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
