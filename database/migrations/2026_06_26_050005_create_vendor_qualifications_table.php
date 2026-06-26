<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('vendor_qualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('kbli_code')->nullable();    // Kode KBLI
            $table->string('kbli_name')->nullable();    // Nama kategori usaha
            $table->string('business_scale')->nullable(); // kecil/menengah/besar
            $table->string('npwp')->nullable();
            $table->string('siup_number')->nullable();
            $table->string('tdp_number')->nullable();
            $table->date('siup_expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('vendor_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('name');           // ISO 9001, SNI, SBUJK, dll
            $table->string('issuer');         // Lembaga penerbit
            $table->string('certificate_number');
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        Schema::create('tender_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // kbli|certification|business_scale|custom
            $table->string('value'); // kode/nama yang disyaratkan
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('tender_requirements');
        Schema::dropIfExists('vendor_certifications');
        Schema::dropIfExists('vendor_qualifications');
    }
};
