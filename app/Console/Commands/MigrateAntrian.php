<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service;
use App\User;
use App\Tiket;
use App\Pasien;

class MigrateAntrian extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = "migrasi:start 
                          {tiket_date? : format [yyyy-mm-dd]}";

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = "Migrasi antrian dari online ke offline";
  private $client = null;
  private $base_url = null;
  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();

    $this->client = new \GuzzleHttp\Client();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    // initial parameter
    if ($this->argument("tiket_date")) {
      $validate_date = \DateTime::createFromFormat(
        "Y-m-d",
        $this->argument("tiket_date")
      );
      if (!$validate_date) {
        $this->error("  Tiket date is incorrect!");
        die();
      }
      $tiket_date = $this->argument("tiket_date");
    } else {
      $tiket_date = date("Y-m-d");
    }

    $service = Service::find("webservice-antrian");
    if ($service) {
      $this->base_url = $service->service_url;

      if (!$service->service_enabled) {
        $this->base_url = "";
      }
    }

    if (!empty($this->base_url)) {
      // proses get token
      $token = $this->getTokenAuth();

      // migrate start
      $this->info("Migrasi Mulai");
      // proses get data antrian
      $list_antrian = $this->getAntrianBy($token, $tiket_date);

      // proses migrasi data antrian
      $this->migrateAntrian($list_antrian, $tiket_date);

      if (count($list_antrian) > 0) {
        $this->info("\n");
        // proses get data pasien
        $list_pasien = $this->getPasienBy($token, $list_antrian);

        // proses migrasi data pasien
        $this->migratePasien($list_pasien);
      }

      // migrate end
      $this->info("Migrasi Selesai");
    } else {
      $this->info(
        date("Y-m-d H:i:s") . " [service-migrasi] info: service tidak tersedia"
      );
    }
  }

  private function getAntrianBy($token, $tiket_date)
  {
    $response = $this->client->request(
      "GET",
      $this->base_url . "/antrian/{$tiket_date}",
      [
        "headers" => [
          "X-Token-Migrate" => $token,
        ],
      ]
    );

    if ($response->getStatusCode() != 200) {
      $this->info("Error : " . $response->getStatusCode());
    }
    $data = json_decode($response->getBody()->getContents(), true);
    return $data["data"];
  }

  private function getPasienBy($token, $list_antrian)
  {
    $list_nik_pasien = [];
    foreach ($list_antrian as $antrian) {
      if (!isset($list_nik_pasien[$antrian["tiket_pasien_nik"]])) {
        $list_nik_pasien[] = $antrian["tiket_pasien_nik"];
      }
    }
    $response = $this->client->request("POST", $this->base_url . "/pasien", [
      "json" => ["nik" => $list_nik_pasien],
      "headers" => [
        "X-Token-Migrate" => $token,
      ],
    ]);

    if ($response->getStatusCode() != 200) {
      $this->info("Error : " . $response->getStatusCode());
    }
    $data = json_decode($response->getBody()->getContents(), true);
    return $data["data"];
  }

  private function migrateAntrian($list_antrian, $tiket_date)
  {
    // informasi data antrian
    $this->info("[Start] Migrasi Data Antrian");
    $total_pendaftar = count($list_antrian);
    $this->info("Tanggal          : " . $tiket_date);

    $errors = [];
    foreach ($list_antrian as $antrian) {
      try {
        Tiket::create($antrian);
      } catch (\Illuminate\Database\QueryException $e) {
        $errors[] = "Error Duplicate for number : " . $antrian["tiket_nomor"];
      }
    }
    $this->info("Jumlah Antrian   : " . $total_pendaftar);
    $this->info("Jumlah Duplikat  : " . count($errors));
    $this->info("[End] Migrasi Data Antrian");
  }

  private function migratePasien($list_pasien)
  {
    // informasi data pasien
    $this->info("[Start] Migrasi Data Pasien");
    $total_pasien = count($list_pasien);

    $errors = [];
    foreach ($list_pasien as $pasien) {
      try {
        Pasien::create($pasien);
      } catch (\Illuminate\Database\QueryException $e) {
        $errors[] = "Error Duplicate for number : " . $pasien["pasien_nik"];
      }
    }
    $this->info("Jumlah Pasien    : " . $total_pasien);
    $this->info("Jumlah Duplikat  : " . count($errors));
    $this->info("[End] Migrasi Data Pasien");

    // foreach ($errors as $error) {
    //   $this->info($error);
    // }
  }

  private function getTokenAuth()
  {
    try {
      $response = $this->client->request("GET", $this->base_url . "/auth", [
        "headers" => [
          "X-Username" => env("APP_MIGRATE_USERNAME"),
          "X-Password" => env("APP_MIGRATE_PASSWORD"),
        ],
      ]);

      $data = json_decode($response->getBody()->getContents(), true);
      $token = $data["response"]["token"];
      return $token;
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      $this->error($e->getMessage());
      die();
    }
  }
}
