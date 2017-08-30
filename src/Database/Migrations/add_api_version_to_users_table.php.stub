<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddApiVersionToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!(Schema::hasColumn('users', 'api_version'))) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('api_version')->nullable()->default(null);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if ((Schema::hasColumn('users', 'api_version'))) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('api_version');
            });
        }
    }
}