<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
    }

    public function down(): void {}
};
