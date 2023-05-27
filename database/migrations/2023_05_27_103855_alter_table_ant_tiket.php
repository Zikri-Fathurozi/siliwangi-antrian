<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAntTiket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ant_tiket', function (Blueprint $table) {
            if (!Schema::hasColumn('ant_tiket', 'tiket_tensi_id')) {
                $table->string('tiket_tensi_id', 100)->nullable()->default(0)->after('tiket_date');
            }

            if (!Schema::hasColumn('ant_tiket', 'tiket_tensi_nomor')) {
                $table->string('tiket_tensi_nomor', 20)->nullable()->default(null)->after('tiket_date');
            }

            if (!Schema::hasColumn('ant_tiket', 'tiket_tensi_call')) {
                $table->integer('tiket_tensi_call')->nullable()->default(0)->after('tiket_date');
            }

            if (!Schema::hasColumn('ant_tiket', 'tiket_tensi_call_datetime')) {
                $table->dateTime("tiket_tensi_call_datetime")->nullable()->after('tiket_date');;
            }

            if (!Schema::hasColumn('ant_tiket', 'tiket_tensi_accepted')) {
                $table->dateTime("tiket_tensi_accepted")->nullable()->after('tiket_date');;
            }

            if (!Schema::hasColumn('ant_tiket', 'tiket_tensi_acceptor')) {
                $table->string('tiket_tensi_acceptor', 100)->nullable()->default(null)->after('tiket_date');
            }

            if (!Schema::hasColumn('ant_tiket', 'tiket_tensi_status')) {
                $table
                    ->char("tiket_tensi_status", 1)
                    ->default("0")
                    ->comment("0:menunggu, 1:hadir, 2:tidak hadir");
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
