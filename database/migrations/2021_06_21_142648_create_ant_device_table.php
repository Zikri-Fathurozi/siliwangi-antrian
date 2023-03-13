<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntDeviceTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("ant_device", function (Blueprint $table) {
      $table->bigIncrements("device_id");
      $table->string("device_nama", 100);
      $table->string("device_ip", 100);
      $table->string("device_modifier", 100);
      $table->dateTime("device_modified")->nullable();
      $table->string("device_code", 100);
      $table->string("device_group", 100)->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists("ant_device");
  }
}
