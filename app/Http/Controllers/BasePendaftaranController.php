<?php

namespace App\Http\Controllers;
use App\Poli;
use App\Tiket;
use Illuminate\Support\Facades\DB;

class BasePendaftaranController extends Controller
{
  protected $prefix_prioritas = "P";

  protected function list()
  {
    $data = Poli::select([
      "poli_nama",
      "poli_id",
      "poli_prefix",
      "poli_gedung",
      DB::raw("0 as jumlah_terdaftar"),
    ])
      ->where(["poli_status" => "1"])
      ->orderBy("poli_nama", "ASC")
      ->get();
    $list = [];
    foreach ($data as $d) {
      $list[$d->poli_id] = $d;
    }
    return json_encode($list);
  }

  /*
  $nomor int
	$poli_id int
	$is_prioritas bool
  */
  protected function format_tiket_nomor($nomor, $poli_id, $is_prioritas = false)
  {
    if ($is_prioritas) {
      $prefix = $this->prefix_prioritas;
    } else {
      $poli = (array) json_decode($this->list());
      $prefix = $poli[$poli_id]->poli_prefix;
    }
    return $prefix . str_pad($nomor, 3, "0", STR_PAD_LEFT);
  }

  /*
  $poli_id int
	$is_prioritas bool
  */
  protected function get_nomor_poli($poli_id, $is_prioritas, $tiket_date)
  {
    $nomor = 1;

    if ($is_prioritas) {
      $tiket = Tiket::select(
        DB::raw("MAX(SUBSTRING(tiket_nomor,2)) as tiket_nomor")
      )
        ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), $tiket_date)
        ->where(DB::raw("SUBSTRING(tiket_nomor,1,1)"), $this->prefix_prioritas)
        ->first();
    } else {
      $tiket = Tiket::select(
        DB::raw("MAX(SUBSTRING(tiket_nomor,2)) as tiket_nomor")
      )
        ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), $tiket_date)
        ->where("tiket_poli_id", $poli_id)
        ->first();
    }

    // prettier-ignore
    $nomor = empty($tiket->tiket_nomor)? $nomor : ((int) $tiket->tiket_nomor + 1);
    return (object) [
      "angka" => $nomor,
      "nomor" => $this->format_tiket_nomor($nomor, $poli_id, $is_prioritas),
    ];
  }
}
