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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('visible_to_all')->default(true);
            $table->timestamps();
        });

        Schema::create('document_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['document_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_user');
        Schema::dropIfExists('documents');
    }
};
