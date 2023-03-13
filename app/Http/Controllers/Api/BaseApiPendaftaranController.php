<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BasePendaftaranController;
use App\Tiket;
use App\Poli;
use Illuminate\Support\Facades\DB;

class BaseApiPendaftaranController extends BasePendaftaranController
{
  protected $poli_id;
  protected $tanggalperiksa;
  protected $mapPoli = [];

  /*
  return { id, nama }
  */
  protected function _getMapPoli($kodepoli)
  {
    if (isset($this->mapPoli[$kodepoli])) {
      $poli = (object) $this->mapPoli[$kodepoli];
      $this->poli_id = $poli->id;

      return $poli;
    }

    return null;
  }

  protected function _getAntrianPanggil()
  {
    $nomor = "";

    $tiket = Tiket::select("tiket_nomor")
      ->where("tiket_call", ">", 0)
      ->where("tiket_poli_id", $this->poli_id)
      ->where(
        DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'),
        $this->tanggalperiksa
      )
      ->orderBy("tiket_call_datetime", "DESC")
      ->first();

    $nomor = empty($tiket->tiket_nomor) ? $nomor : $tiket->tiket_nomor;
    return $nomor;
  }

  protected function _getTotalPendaftar()
  {
    return Tiket::where("tiket_poli_id", $this->poli_id)
      ->where(
        DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'),
        $this->tanggalperiksa
      )
      ->count();
  }

  protected function _getSisaKuota()
  {
    $poli = Poli::find($this->poli_id);
    $kuota = $poli->poli_kuota;
    $kuota = $kuota == 0 ? 20 : $kuota;
    $total_pendaftar = $this->_getTotalPendaftar(
      $this->poli_id,
      $this->tanggalperiksa
    );
    $sisa_antrian = $kuota > $total_pendaftar ? $kuota - $total_pendaftar : 0;

    return $sisa_antrian;
  }

  protected function responseWithError($message, $code)
  {
    return response()->json(
      [
        "response" => null,
        "metadata" => [
          "message" => $message,
          "code" => $code,
        ],
      ],
      $code
    );
  }

  /*
    $response Array
    */
  protected function responseWithSuccess($response)
  {
    return response()->json(
      [
        "response" => $response,
        "metadata" => [
          "message" => "OK",
          "code" => "200",
        ],
      ],
      200
    );
  }
}
