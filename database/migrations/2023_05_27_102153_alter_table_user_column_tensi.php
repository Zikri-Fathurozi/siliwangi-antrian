<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUserColumnTensi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'tensi')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('tensi')->nullable()->default(NULL)->after('prioritas');
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
        if (Schema::hasColumn('users', 'tensi')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['tensi']);
            });
        }
    }
}
