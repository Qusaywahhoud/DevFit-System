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
        if (Schema::hasTable('exercises')) {
            Schema::table('exercises', function (Blueprint $table) {
                // Add composite index combining user_id (if exists), category, and created_at
                $columns = ['category', 'created_at'];
                if (Schema::hasColumn('exercises', 'user_id')) {
                    array_unshift($columns, 'user_id');
                }
                $table->index($columns, 'exercises_composite_cat_date_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exercises')) {
            Schema::table('exercises', function (Blueprint $table) {
                $table->dropIndex('exercises_composite_cat_date_index');
            });
        }
    }
};

