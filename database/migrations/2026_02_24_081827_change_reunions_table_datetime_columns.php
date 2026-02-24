<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data from 'date' column to 'start_time' and 'end_time' if not already set
        if (Schema::hasColumn('reunions', 'date')) {
            DB::table('reunions')
                ->whereNull('start_time')
                ->orWhereNull('end_time')
                ->update([
                    'start_time' => DB::raw('COALESCE(start_time, date)'),
                    'end_time' => DB::raw("COALESCE(end_time, datetime(COALESCE(start_time, date), '+2 hours'))"),
                ]);
        }

        // Drop indices before dropping columns
        Schema::table('reunions', function (Blueprint $table) {
            if (Schema::hasIndex('reunions', 'reunions_date_index')) {
                $table->dropIndex('reunions_date_index');
            }
        });

        // Drop the old 'date' column if it exists
        Schema::table('reunions', function (Blueprint $table) {
            if (Schema::hasColumn('reunions', 'date')) {
                $table->dropColumn('date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reunions', function (Blueprint $table) {
            $table->dateTime('date')->nullable();
        });

        // Restore date from start_time
        DB::table('reunions')
            ->whereNull('date')
            ->update(['date' => DB::raw('start_time')]);

        // Recreate the index
        Schema::table('reunions', function (Blueprint $table) {
            $table->index('date', 'reunions_date_index');
        });
    }
};
