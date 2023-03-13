<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntPasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ant_pasien', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pasien_nik',100)->unique();
            $table->string('pasien_nomor_kk',100)->nullable();
            $table->string('pasien_nomor_kartu',100)->unique()->nullable();
            $table->string('pasien_nama',225)->nullable();
            $table->char('pasien_gender',1)->nullable();
            $table->date('pasien_birth_date')->nullable();
            $table->string('pasien_address',225)->nullable();
            $table->string('pasien_prop_kode',10)->nullable();
            $table->string('pasien_prop_nama',100)->nullable();
            $table->string('pasien_dati2_kode',10)->nullable();
            $table->string('pasien_dati2_nama',100)->nullable();
            $table->string('pasien_kec_kode',10)->nullable();
            $table->string('pasien_kec_nama',100)->nullable();
            $table->string('pasien_kel_kode',10)->nullable();
            $table->string('pasien_kel_nama',100)->nullable();
            $table->string('pasien_rt',5)->nullable();
            $table->string('pasien_rw',5)->nullable();            
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
        Schema::dropIfExists('ant_pasien');
    }
}
