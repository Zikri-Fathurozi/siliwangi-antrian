<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntServiceTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('ant_service')) {
      Schema::create("ant_service", function (Blueprint $table) {
        $table->bigIncrements("service_id");
        $table->string("service_nama", 100);
        $table->string("service_url", 225)->comment("URL Service");
        $table->string("service_modifier", 100)->nullable();
        $table->dateTime("service_modified")->nullable();
        $table->string("service_code", 100);
      });

      DB::statement(
        "ALTER Table ant_service add service_enabled INT(1) NULL DEFAULT 0 after service_code"
      );
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    if (Schema::hasTable('ant_service')) {
      Schema::dropIfExists("ant_service");
    }
  }
}
