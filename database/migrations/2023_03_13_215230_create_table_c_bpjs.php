<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCBpjs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_bpjs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('kode_provider', 8)->nullable()->default(null);
            $table->string('username')->nullable()->default(null);
            $table->string('password')->nullable()->default(null);
            $table->string('consid')->nullable()->default(null);
            $table->string('secret')->nullable()->default(null);
            $table->string('user_key')->nullable()->default(null);
            $table->string('url')->nullable()->default(null);
        });
        $path = 'https://apijkn-dev.bpjs-kesehatan.go.id/pcare-rest-dev/';
        $secret = '7mY98FCD80';
        $consid = '17146';
        $username = 'user3-bkt';
        $password = '#BPJSkes2022';
        $user_key = '4cc8079bd1fdf75bbc2a9916cfa45ff8';

        DB::table('c_bpjs')->insert([
            [
                'kode_provider' => '00930020',
                'username' => $username,
                'password' => $password,
                'consid' => $consid,
                'secret' => $secret,
                'url' => $path,
                'user_key' => $user_key
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_bpjs');
    }
}
