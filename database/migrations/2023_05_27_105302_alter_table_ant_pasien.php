<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAntPasien extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ant_pasien', function (Blueprint $table) {
            if (!Schema::hasColumn('ant_pasien', 'pasien_uid')) {
                $table->string('pasien_uid', 100)->nullable()->default(null)->after('pasien_rw');
            }

            if (!Schema::hasColumn('ant_pasien', 'pasien_channel')) {
                $table
                    ->char("pasien_channel", 2)
                    ->default(null)
                    ->after('pasien_rw');
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
        //
    }
}
