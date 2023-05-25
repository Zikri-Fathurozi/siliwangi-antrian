<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAntTiketAddNomerPeserta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('ant_tiket', 'tiket_pasien_asuransi')) {
            Schema::table('ant_tiket', function (Blueprint $table) {
                $table->string('tiket_pasien_asuransi', 256)->nullable()->default(NULL)->after('tiket_pasien_nik');
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
        Schema::table('ant_tiket', function (Blueprint $table) {
            $table->dropColumn(['tiket_pasien_asuransi']);
        });
    }
}
