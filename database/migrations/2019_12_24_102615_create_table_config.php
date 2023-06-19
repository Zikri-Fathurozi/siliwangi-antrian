<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ant_config')) {
            Schema::create('ant_config', function (Blueprint $table) {
                $table->string('config_key',100)->primary();
                $table->string('config_value',100)->nullable();
                $table->string('config_modifier',100)->nullable();
                $table->dateTime('config_modified')->nullable();
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
        if (Schema::hasTable('ant_config')) {
            Schema::dropIfExists('ant_config');
        }
    }
}
