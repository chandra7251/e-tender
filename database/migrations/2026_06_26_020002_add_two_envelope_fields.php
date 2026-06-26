<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mark evaluation criteria as technical vs price type
        if (!Schema::hasColumn('tender_evaluation_criteria', 'envelope')) {
            Schema::table('tender_evaluation_criteria', function (Blueprint $table) {
                $table->string('envelope', 20)->default('technical')->after('description');
            });
        }

        // Track bid technical pass/fail status
        if (!Schema::hasColumn('bids', 'technical_status')) {
            Schema::table('bids', function (Blueprint $table) {
                $table->string('technical_status', 20)->default('pending')->after('notes');
                $table->decimal('technical_score', 8, 2)->nullable()->after('technical_status');
                $table->decimal('price_score', 8, 2)->nullable()->after('technical_score');
                $table->text('technical_notes')->nullable()->after('price_score');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tender_evaluation_criteria', function (Blueprint $table) {
            $table->dropColumn('envelope');
        });

        Schema::table('bids', function (Blueprint $table) {
            $table->dropColumn(['technical_status', 'technical_score', 'price_score', 'technical_notes']);
        });
    }
};
