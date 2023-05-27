<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAntPoli extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ant_poli', function (Blueprint $table) {
            if (!Schema::hasColumn('ant_poli', 'poli_hide_on_register')) {
                $table->integer('poli_hide_on_register', 1)->nullable()->default(0)->after('poli_hide_on_prioritas');
            }

            if (!Schema::hasColumn('ant_poli', 'poli_tensi_id')) {
                $table->integer('poli_tensi_id')->nullable()->default(null)->after('poli_hide_on_prioritas');
            }

            if (!Schema::hasColumn('ant_poli', 'poli_is_tensi')) {
                $table->integer('poli_is_tensi', 1)->nullable()->default(0)->after('poli_hide_on_prioritas');
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
