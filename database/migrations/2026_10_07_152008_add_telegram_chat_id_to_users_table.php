<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // أضفنا الحقل كنوع string ليتسع للأرقام الطويلة، وجعلناه nullable لأن المستخدم قد لا يربط حسابه فور التسجيل
        $table->string('telegram_chat_id')->nullable()->after('email');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('telegram_chat_id');
    });
}
};
