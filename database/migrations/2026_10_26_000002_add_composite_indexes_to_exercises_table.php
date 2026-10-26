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
                $table->index(['category', 'created_at'], 'exercises_category_created_at_index');
            });
        }

        if (Schema::hasTable('completed_exercises')) {
            Schema::table('completed_exercises', function (Blueprint $table) {
                $table->index(['user_id', 'created_at'], 'completed_exercises_user_created_at_index');
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
                $table->dropIndex('exercises_category_created_at_index');
            });
        }

        if (Schema::hasTable('completed_exercises')) {
            Schema::table('completed_exercises', function (Blueprint $table) {
                $table->dropIndex('completed_exercises_user_created_at_index');
            });
        }
    }
};
