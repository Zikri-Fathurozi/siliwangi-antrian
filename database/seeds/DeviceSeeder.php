<?php

use App\Device;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $device = Device::where("device_code", "tiket")->first();
    if (!$device) {
      Device::create([
        "device_nama" => "PC Tiket Dispenser",
        "device_ip" => "127.0.0.1",
        "device_code" => "tiket",
      ]);
    }

    $device = Device::where("device_code", "display-pendaftaran")->first();
    if (!$device) {
      Device::create([
        "device_nama" => "PC Display Pendaftaran",
        "device_ip" => "127.0.0.1",
        "device_code" => "display-pendaftaran",
      ]);
    }

    $device = Device::where("device_code", "display-gedung-1")->first();
    if (!$device) {
      Device::create([
        "device_nama" => "PC Display Poli",
        "device_ip" => "127.0.0.1",
        "device_code" => "display-gedung-1",
        "device_group" => "display-gedung-poli",
      ]);
    }
  }
}
