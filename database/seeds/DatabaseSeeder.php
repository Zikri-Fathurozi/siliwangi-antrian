<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call(UserTableSeeder::class);
    $this->call(DeviceSeeder::class);
    $this->call(PrinterTableSeeder::class);
    $this->call(PoliTableSeeder::class);
    $this->call(ConfigTableSeeder::class);
    $this->call(ServiceTableSeeder::class);
    $this->call(ChannelTableSeeder::class);
  }
}
