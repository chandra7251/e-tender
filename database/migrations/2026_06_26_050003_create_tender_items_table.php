<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tender_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('unit')->default('unit'); // pcs, m2, liter, dll
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('hps_unit_price', 20, 2)->default(0); // harga satuan HPS
            $table->decimal('hps_subtotal', 20, 2)->storedAs('quantity * hps_unit_price');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tender_items'); }
};
