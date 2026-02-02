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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'fonction')) {
                $table->string('fonction')->nullable()->after('is_admin');
            }

            if (! Schema::hasColumn('users', 'commune')) {
                $table->string('commune')->nullable()->after('fonction');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'commune')) {
                $table->dropColumn('commune');
            }

            if (Schema::hasColumn('users', 'fonction')) {
                $table->dropColumn('fonction');
            }
        });
    }
};
