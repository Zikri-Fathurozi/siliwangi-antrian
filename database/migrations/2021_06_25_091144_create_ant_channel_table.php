<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntChannelTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('ant_channel')) {
      Schema::create("ant_channel", function (Blueprint $table) {
        $table->bigIncrements("channel_id");
        $table->string("channel_nama", 225)->unique();
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
    if (Schema::hasTable('ant_channel')) {
      Schema::dropIfExists("ant_channel");
    }
  }
}
