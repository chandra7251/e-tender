<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tender_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tender_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->string('order_id', 100)->unique();
            $table->enum('type', ['deposit','invoice','refund'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending','paid','failed','refunded','expired'])->default('pending');
            $table->string('snap_token', 255)->nullable();
            $table->text('snap_url')->nullable();
            $table->json('midtrans_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->index(['tender_id','vendor_id']);
            $table->index('status');
        });
        // Add deposit_paid column to tender_bids if not exists
        if (Schema::hasTable('tender_bids') && !Schema::hasColumn('tender_bids', 'deposit_paid')) {
            Schema::table('tender_bids', function (Blueprint $table) {
                $table->boolean('deposit_paid')->default(false)->after('is_winner');
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('tender_payments');
        if (Schema::hasColumn('tender_bids', 'deposit_paid')) {
            Schema::table('tender_bids', fn($t) => $t->dropColumn('deposit_paid'));
        }
    }
};
