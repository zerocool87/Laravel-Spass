<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reunions', function (Blueprint $table) {
            $table->json('titres')->nullable()->after('status');
            $table->boolean('visible_to_all')->default(false)->after('titres');
        });
    }

    public function down(): void
    {
        Schema::table('reunions', function (Blueprint $table) {
            $table->dropColumn(['titres', 'visible_to_all']);
        });
    }
};
