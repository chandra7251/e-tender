<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('tender_results', function (Blueprint $table) {

            $table->dropForeign(['decided_by']);

            $table->unsignedBigInteger('decided_by')->nullable()->change();

            $table->foreign('decided_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tender_results', function (Blueprint $table) {
            $table->dropForeign(['decided_by']);
            $table->unsignedBigInteger('decided_by')->nullable(false)->change();
            $table->foreign('decided_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
