<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKamarPoli extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ant_kamar_poli')) {
            Schema::create('ant_kamar_poli', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('poli_id')->nullable()->default(null);
                $table->char("order", 2)->nullable();
                $table->timestamps();
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
        if (Schema::hasTable('ant_kamar_poli')) {
            Schema::dropIfExists('ant_kamar_poli');
        }
    }
}
