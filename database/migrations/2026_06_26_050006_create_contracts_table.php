<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['draft','sent_to_vendor','signed_vendor','signed_admin','active','completed','terminated'])->default('draft');
            $table->decimal('contract_value', 20, 2);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('terms')->nullable();
            $table->string('vendor_signature_path')->nullable();
            $table->timestamp('vendor_signed_at')->nullable();
            $table->string('admin_signature_path')->nullable();
            $table->timestamp('admin_signed_at')->nullable();
            $table->string('document_hash')->nullable(); // SHA-256 integrity
            $table->string('qr_code_path')->nullable();
            $table->text('termination_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contract_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->string('milestone_name');
            $table->text('description')->nullable();
            $table->date('due_date');
            $table->enum('status', ['scheduled','in_progress','delivered','verified','overdue'])->default('scheduled');
            $table->text('vendor_notes')->nullable();
            $table->string('evidence_path')->nullable(); // bukti pengiriman
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('contract_deliveries');
        Schema::dropIfExists('contracts');
    }
};
