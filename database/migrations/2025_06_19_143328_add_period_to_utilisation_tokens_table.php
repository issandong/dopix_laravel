<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('utilisation_tokens', function (Blueprint $table) {
            $table->string('period', 7)->after('user_id')->index(); // 'YYYY-MM', indexé pour les requêtes
        });
    }

    public function down()
    {
        Schema::table('utilisation_tokens', function (Blueprint $table) {
            $table->dropColumn('period');
        });
    }
};