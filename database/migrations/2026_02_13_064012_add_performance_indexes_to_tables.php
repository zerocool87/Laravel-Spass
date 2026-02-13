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
        // Add indexes to documents table for better query performance
        Schema::table('documents', function (Blueprint $table) {
            $table->index('created_by', 'documents_created_by_index');
            $table->index('category', 'documents_category_index');
        });

        // Add indexes to events table
        Schema::table('events', function (Blueprint $table) {
            $table->index('created_by', 'events_created_by_index');
            $table->index(['start_at', 'end_at'], 'events_dates_index');
        });

        // Add indexes to reunions table
        Schema::table('reunions', function (Blueprint $table) {
            $table->index(['instance_id', 'status'], 'reunions_instance_status_index');
            $table->index('date', 'reunions_date_index');
        });

        // Add indexes to instances table
        Schema::table('instances', function (Blueprint $table) {
            $table->index('type', 'instances_type_index');
            $table->index('territory', 'instances_territory_index');
        });

        // Add indexes to projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->index(['status', 'created_at'], 'projects_status_created_index');
            $table->index('type', 'projects_type_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex('documents_created_by_index');
            $table->dropIndex('documents_category_index');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_created_by_index');
            $table->dropIndex('events_dates_index');
        });

        Schema::table('reunions', function (Blueprint $table) {
            $table->dropIndex('reunions_instance_status_index');
            $table->dropIndex('reunions_date_index');
        });

        Schema::table('instances', function (Blueprint $table) {
            $table->dropIndex('instances_type_index');
            $table->dropIndex('instances_territory_index');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_status_created_index');
            $table->dropIndex('projects_type_index');
        });
    }
};
