<?php

use Illuminate\Database\Seeder;
use App\Service;
class ServiceTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $service = Service::where("service_code", "sim")->first();
    if (!$service) {
      Service::create([
        "service_nama" => "Service SIM Klinik",
        "service_url" => "http://192.168.8.102:8000/api",
        "service_code" => "sim",
        "service_enabled" => 1,
      ]);
    }

    $service = Service::where("service_code", "webservice-antrian")->first();
    if (!$service) {
      Service::create([
        "service_nama" => "Webservice Antrian",
        "service_url" => "http://localhost:8000/api/migrate/v1",
        "service_code" => "webservice-antrian",
        "service_enabled" => 1,
      ]);
    }
  }
}
