<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('title');                  // العنوان بالإنجليزية (الأساسي)
            $table->string('title_ar')->nullable();   // العنوان بالعربية
            $table->text('description');             // الوصف بالإنجليزية (الأساسي)
            $table->text('description_ar')->nullable(); // الوصف بالعربية
            $table->string('category');
            $table->integer('duration_seconds')->default(60);
            $table->string('image_path')->nullable();
            $table->string('video_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
