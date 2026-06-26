<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('tender_id')->constrained()->onDelete('cascade');
            $table->foreignId('rated_by')->constrained('users')->onDelete('cascade');

            $table->tinyInteger('quality_score')->unsigned()->default(0);      // 1-5
            $table->tinyInteger('delivery_score')->unsigned()->default(0);     // 1-5
            $table->tinyInteger('communication_score')->unsigned()->default(0);// 1-5
            $table->tinyInteger('compliance_score')->unsigned()->default(0);   // 1-5
            $table->decimal('overall_score', 3, 2)->default(0);               // avg of above

            $table->text('review')->nullable();
            $table->timestamps();

            $table->unique(['vendor_id', 'tender_id']); // 1 rating per tender per vendor
        });

        // Vendor blacklist
        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('is_blacklisted')->default(false)->after('verification_status');
            $table->text('blacklist_reason')->nullable()->after('is_blacklisted');
            $table->timestamp('blacklisted_at')->nullable()->after('blacklist_reason');
            $table->foreignId('blacklisted_by')->nullable()->constrained('users')->nullOnDelete()->after('blacklisted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_ratings');

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign(['blacklisted_by']);
            $table->dropColumn(['is_blacklisted', 'blacklist_reason', 'blacklisted_at', 'blacklisted_by']);
        });
    }
};
