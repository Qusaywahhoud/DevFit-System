<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('exercises')) {
            Schema::create('exercises', function (Blueprint $table) {
                $table->id();
                $table->string('title_ar');
                $table->string('title_en');
                $table->text('description_ar');
                $table->text('description_en');
                $table->string('category');
                $table->integer('duration_seconds');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
