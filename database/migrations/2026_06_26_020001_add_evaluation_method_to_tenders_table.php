<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->string('evaluation_method', 30)->default('lowest_price')->after('status');
            // lowest_price | multi_criteria | two_envelope
            $table->decimal('technical_weight', 5, 2)->nullable()->after('evaluation_method');
            $table->decimal('price_weight', 5, 2)->nullable()->after('technical_weight');
            $table->decimal('passing_grade', 5, 2)->nullable()->after('price_weight');
            // minimum technical score to pass (e.g. 70)
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropColumn(['evaluation_method', 'technical_weight', 'price_weight', 'passing_grade']);
        });
    }
};
