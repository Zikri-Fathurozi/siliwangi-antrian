<?php

use Illuminate\Database\Seeder;
use App\Channel;

class ChannelTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Channel::truncate();
    Channel::create([
      "channel_id" => 1,
      "channel_nama" => "Mesin Antrian",
    ]);
    Channel::create([
      "channel_id" => 2,
      "channel_nama" => "Mobile JKN",
    ]);
    Channel::create([
      "channel_id" => 3,
      "channel_nama" => "Mobile Siliwangi",
    ]);
  }
}
