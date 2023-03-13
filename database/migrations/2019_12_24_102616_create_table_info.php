<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ant_info', function (Blueprint $table) {
            $table->bigIncrements('info_id');
            $table->string('info_text',100)->nullable();
            $table->string('info_status',100)->nullable();
            $table->string('info_author',100)->nullable();
            $table->dateTime('info_created')->nullable();
            $table->string('info_modifier',100)->nullable();
            $table->dateTime('info_modified')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ant_info');
    }
}
