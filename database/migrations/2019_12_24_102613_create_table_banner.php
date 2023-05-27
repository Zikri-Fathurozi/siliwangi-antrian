<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBanner extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('ant_banner')) {
      Schema::create("ant_banner", function (Blueprint $table) {
        $table->bigIncrements("banner_id");
        $table->string("banner_path", 225)->nullable();
        $table->string("banner_type", 100)->nullable();
        $table->string("banner_title", 100)->nullable();
        $table->string("banner_desc", 100)->nullable();
        $table->string("banner_author", 100)->nullable();
        $table->dateTime("banner_created")->nullable();
        $table->char("banner_status", 1)->nullable();
        $table->string("banner_mime", 100)->nullable();
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
    if (Schema::hasTable('ant_banner')) {
      Schema::dropIfExists("ant_banner");
    }
  }
}
