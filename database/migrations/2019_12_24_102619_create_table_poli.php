<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePoli extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('ant_poli')) {
      Schema::create("ant_poli", function (Blueprint $table) {
        $table->bigIncrements("poli_id");
        $table->string("poli_nama", 100)->nullable();
        $table->string("poli_color", 50)->nullable();
        $table->string("poli_icon", 50)->nullable();
        $table->char("poli_status", 1)->nullable();
        $table->dateTime("poli_created")->nullable();
        $table->dateTime("poli_modified")->nullable();
        $table->string("poli_modifier", 100)->nullable();
        $table->string("poli_author", 100)->nullable();
        $table->string("poli_gedung", 100)->nullable();
        $table->char("poli_prefix", 2)->nullable();
        $table->string("poli_audio", 100)->nullable();
        $table->string("poli_deskripsi", 225)->nullable();
        $table->string("poli_dayopen", 50)->nullable();
        $table->string("poli_closehour", 50)->nullable();
      });

      DB::statement(
        "ALTER Table ant_poli add poli_kuota INT(11) NULL after poli_deskripsi"
      );

      DB::statement(
        "ALTER Table ant_poli add poli_prioritas INT(1) DEFAULT 0 after poli_closehour"
      );

      DB::statement(
        "ALTER Table ant_poli add poli_hide_on_prioritas INT(1) DEFAULT 0 after poli_prioritas"
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
    if (Schema::hasTable('ant_poli')) {
      Schema::dropIfExists("ant_poli");
    }
  }
}
