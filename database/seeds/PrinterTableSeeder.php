<?php

use Illuminate\Database\Seeder;
use App\Printer;

class PrinterTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $printer = Printer::where("printer_code", "tiket")->first();
    if (!$printer) {
      Printer::create([
        "printer_nama" => "Printer Tiket Dispenser",
        "printer_url_service" => "http://192.168.8.102:8000/api/ticket",
        "printer_alias" => "t82",
        "printer_code" => "tiket",
      ]);
    }
  }
}
