<?php

declare(strict_types=1);

use App\Models\Instance;
use App\Models\Thematique;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Map existing instances to thematiques
        $instances = Instance::all(['id', 'name']);
        $idMap = [];

        foreach ($instances as $instance) {
            $thematique = Thematique::create(['name' => $instance->name]);
            $idMap[$instance->id] = $thematique->id;
        }

        // Drop old FK, update data, add new FK
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropForeign(['thematique_id']);
        });

        foreach ($idMap as $oldId => $newId) {
            DB::table('forum_threads')
                ->where('thematique_id', $oldId)
                ->update(['thematique_id' => $newId]);
        }

        // Handle any orphaned thematique_ids (shouldn't happen, but safety)
        DB::table('forum_threads')
            ->whereNotIn('thematique_id', $idMap)
            ->update(['thematique_id' => null]);

        Schema::table('forum_threads', function (Blueprint $table) {
            $table->foreign('thematique_id')->references('id')->on('thematiques')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('forum_threads', function (Blueprint $table) {
            $table->dropForeign(['thematique_id']);
        });

        // Restore old instance IDs
        $thematiques = Thematique::all(['id', 'name']);
        foreach ($thematiques as $thematique) {
            $instance = Instance::where('name', $thematique->name)->first();
            if ($instance) {
                DB::table('forum_threads')
                    ->where('thematique_id', $thematique->id)
                    ->update(['thematique_id' => $instance->id]);
            }
        }

        Schema::table('forum_threads', function (Blueprint $table) {
            $table->foreign('thematique_id')->references('id')->on('instances')->cascadeOnDelete();
        });
    }
};
