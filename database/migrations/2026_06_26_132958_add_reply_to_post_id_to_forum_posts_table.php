<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->foreignId('reply_to_post_id')
                ->nullable()
                ->after('body')
                ->constrained('forum_posts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('forum_posts', function (Blueprint $table) {
            $table->dropForeign(['reply_to_post_id']);
            $table->dropColumn('reply_to_post_id');
        });
    }
};
