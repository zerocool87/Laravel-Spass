<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop indexes on columns being removed (required by SQLite)
        Schema::table('instances', function (Blueprint $table) {
            $table->dropIndex('instances_type_index');
            $table->dropIndex('instances_territory_index');
        });

        Schema::table('instances', function (Blueprint $table) {
            $table->dropColumn(['type', 'description', 'members', 'territory']);
        });

        DB::table('instances')->delete();
    }

    public function down(): void
    {
        Schema::table('instances', function (Blueprint $table) {
            $table->string('type')->nullable()->after('name');
            $table->text('description')->nullable()->after('type');
            $table->json('members')->nullable()->after('description');
            $table->string('territory')->nullable()->after('members');
        });
    }
};
