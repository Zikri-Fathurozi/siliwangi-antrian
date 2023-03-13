<?php

use Illuminate\Database\Seeder;

class ConfigTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $config = DB::table("ant_config")
      ->where("config_key", "display_banner")
      ->first();

    if (!$config) {
      DB::table("ant_config")->insert([[
        "config_key" => "display_banner",
        "config_value" => "image",
      ]]);
    }
  }
}
