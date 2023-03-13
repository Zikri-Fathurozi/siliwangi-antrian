<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePrinter extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create("ant_printer", function (Blueprint $table) {
      $table->bigIncrements("printer_id");
      $table->string("printer_nama", 100);
      $table
        ->string("printer_url_service", 225)
        ->comment("URL Service untuk print");
      $table
        ->string("printer_alias", 100)
        ->default("epson")
        ->nullable();
      $table->string("printer_modifier", 100)->nullable();
      $table->dateTime("printer_modified")->nullable();
      $table->string("printer_code", 100);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists("ant_printer");
  }
}
