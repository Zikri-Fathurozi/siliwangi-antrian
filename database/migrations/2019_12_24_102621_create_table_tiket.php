<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableTiket extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("ant_tiket", function (Blueprint $table) {
      $table->string("tiket_id", 100)->primary();
      $table->softDeletes();
      $table->string("tiket_nomor", 10)->nullable();
      // $table->integer('tiket_call',11)->nullable();
      $table->dateTime("tiket_call_datetime")->nullable();
      $table->dateTime("tiket_created")->nullable();
      $table->dateTime("tiket_accepted")->nullable();
      $table->string("tiket_acceptor", 100)->nullable();
      $table
        ->char("tiket_status", 1)
        ->default("0")
        ->comment("0:menunggu, 1:hadir, 2:tidak hadir");
      $table
        ->date("tiket_date")
        ->nullable()
        ->comment("tanggal antrian");
      $table->string("tiket_poli_id", 100)->nullable();
      $table->string("tiket_poli_nomor", 20)->nullable();
      // $table->integer('tiket_poli_call',11)->nullable();
      $table->dateTime("tiket_poli_call_datetime")->nullable();
      $table->dateTime("tiket_poli_accepted")->nullable();
      $table->string("tiket_poli_acceptor", 100)->nullable();
      $table
        ->char("tiket_poli_status", 1)
        ->default("0")
        ->comment("0:menunggu, 1:hadir, 2:tidak hadir");

      $table->char("tiket_prioritas", 1)->default("0");
      $table->char("tiket_pasien_dirujuk", 1)->default("0");
      $table->char("tiket_loket", 1)->nullable();

      $table->string("tiket_pasien_nik", 100)->nullable();
      $table->text("tiket_pasien_keluhan", 100)->nullable();
      $table->unique(
        ["tiket_nomor", "tiket_date"],
        "ant_tiket_UN"
      );
    });

    DB::statement(
      "ALTER TABLE ant_tiket add tiket_call INT(11) NULL DEFAULT 1 after tiket_nomor"
    );
    DB::statement(
      "ALTER TABLE ant_tiket add tiket_channel_id INT(11) NULL DEFAULT 1 after tiket_loket"
    );
    DB::statement(
      "ALTER TABLE ant_tiket add tiket_poli_call INT(11) NULL DEFAULT 1 after tiket_poli_nomor"
    );
    DB::statement(
      "ALTER TABLE ant_tiket add tiket_created_by INT(11) NULL DEFAULT NULL after tiket_pasien_keluhan"
    );
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists("ant_tiket");
  }
}