<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('bid_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bid_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('unit_price', 20, 2)->default(0);
            // subtotal dihitung di PHP: unit_price * quantity (subquery tidak didukung MySQL generated column)
            $table->decimal('subtotal', 20, 2)->default(0)->comment('unit_price * tender_item.quantity, dihitung saat save');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('bid_items'); }
};
