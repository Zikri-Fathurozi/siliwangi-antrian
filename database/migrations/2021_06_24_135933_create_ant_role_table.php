<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntRoleTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('ant_role')) {
      Schema::create("ant_role", function (Blueprint $table) {
        $table->string("role_id", 100)->primary();
        $table->string("role_nama", 100);
        $table->text("role_deskripsi")->nullable();
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
    if (Schema::hasTable('ant_role')) {
      Schema::dropIfExists("ant_role");
    }
  }
}
