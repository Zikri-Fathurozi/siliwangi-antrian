<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntDokterTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("ant_dokter", function (Blueprint $table) {
      $table->bigIncrements("id");
      $table->string("dokter_nik", 100)->nullable();
      $table->string("dokter_nama", 225);
      $table->char("dokter_gender", 1)->nullable();
      $table->date("dokter_birth_date")->nullable();
      $table->string("dokter_address", 225)->nullable();
      $table->string("dokter_jam_praktik", 225)->nullable();
      $table->string("dokter_poli_id", 100)->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists("ant_dokter");
  }
}
