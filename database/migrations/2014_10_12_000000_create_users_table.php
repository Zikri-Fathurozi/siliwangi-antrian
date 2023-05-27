<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    if (!Schema::hasTable('users')) {
      Schema::create("users", function (Blueprint $table) {
        $table->bigIncrements("id");
        $table->string("name");
        $table->string("email")->unique();
        $table->timestamp("email_verified_at")->nullable();
        $table->string("password");
        $table->rememberToken();
        $table->timestamps();
        $table
          ->string("role", 100)
          ->comment("superadmin, admin, pendaftaran, poli, channel")
          ->nullable();
        $table->char("poli", 10)->nullable();
        $table
          ->char("loket", 10)
          ->comment("nomor loket")
          ->nullable();
        $table
          ->char("loket_lansia", 1)
          ->default("0")
          ->nullable();
        $table
          ->char("prioritas", 1)
          ->default("0")
          ->nullable();
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
    if (Schema::hasTable('users')) {
      Schema::dropIfExists("users");
    }
  }
}
