<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Be defensive: only add the column if it doesn't already exist (prevents duplicate column errors)
        if (! Schema::hasColumn('events', 'type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('type')->nullable()->default('autre')->after('location');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('events', 'type')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
