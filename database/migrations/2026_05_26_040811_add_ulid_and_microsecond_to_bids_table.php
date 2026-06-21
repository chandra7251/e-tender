<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('bids', function (Blueprint $table) {

            $table->dateTime('submitted_at', 6)->change();

            $table->string('ulid', 26)->nullable()->unique()->after('notes');

            $table->index(['tender_id', 'bid_amount', 'submitted_at', 'ulid'], 'bids_winner_idx');
        });

        DB::table('bids')->whereNull('ulid')->orderBy('id')->each(function ($bid) {
            DB::table('bids')->where('id', $bid->id)->update([
                'ulid' => (string) Str::ulid(),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropIndex('bids_winner_idx');
            $table->dropColumn('ulid');
            $table->dateTime('submitted_at')->change();
        });
    }
};
