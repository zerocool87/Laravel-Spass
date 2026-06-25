<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropForeign(['instance_id']);
            $table->renameColumn('instance_id', 'thematique_id');
            $table->foreign('thematique_id')->references('id')->on('instances')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropForeign(['thematique_id']);
            $table->renameColumn('thematique_id', 'instance_id');
            $table->foreign('instance_id')->references('id')->on('instances')->cascadeOnDelete();
        });
    }
};
