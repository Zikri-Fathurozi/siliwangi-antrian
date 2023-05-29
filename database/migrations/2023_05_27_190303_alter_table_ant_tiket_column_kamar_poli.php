<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAntTiketColumnKamarPoli extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ant_tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('ant_tiket', 'kamar_poli')) {
                $table->char('kamar_poli', 2)->nullable()->default(null);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ant_tiket', function (Blueprint $table){
            $table->dropColumn('kamar_poli');
        });
    }
}
