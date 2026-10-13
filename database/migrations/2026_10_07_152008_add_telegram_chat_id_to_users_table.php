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
        // التحقق مما إذا كان العمود غير موجود، لكي لا يتم إضافته مرتين ويتسبب بخطأ
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'telegram_chat_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('telegram_chat_id')->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'telegram_chat_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('telegram_chat_id');
            });
        }
    }
};
