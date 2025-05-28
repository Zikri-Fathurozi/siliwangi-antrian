<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BasePendaftaranController;
use App\Pasien;
use App\Poli;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use App\Tiket;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends BasePendaftaranController
{
  public function nomor_current()
  {
    $nomor = "-";

    $tiket = Tiket::select("tiket_nomor")
      ->where("tiket_call", ">", 0)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->orderBy("tiket_call_datetime", "DESC")
      ->first();

    $nomor = empty($tiket->tiket_nomor) ? $nomor : $tiket->tiket_nomor;
    return $nomor;
  }

  public function summary()
  {
    if (config("antrian.multi_loket_pendaftaran") == "true") {
      return $this->summary_multiloket();
    } else {
      return $this->summary_singleloket();
    }
  }

  private function summary_multiloket()
  {
    $loket = [1, 2, 3, 4];

    $list = [];
    foreach ($loket as $no) {
      $tiket = Tiket::select("tiket_nomor")
        ->where("tiket_call", ">", 0)
        ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
        ->where("tiket_loket", $no)
        ->orderBy("tiket_call_datetime", "DESC")
        ->first();
      $current = $tiket ? $tiket->tiket_nomor : "-";
      $list[] = [
        "loket" => $no,
        "current" => $current,
      ];
    }

    return $list;
  }

  private function summary_singleloket()
  {
    $list_poli = json_decode($this->list());
    $list = [];
    foreach ($list_poli as $poli_id => $poli) {
      $tiket = Tiket::select("tiket_nomor")
        ->whereNull("tiket_call")
        ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
        ->where("tiket_nomor", "like", "{$poli->poli_prefix}%")
        ->orderBy("tiket_created", "ASC")
        ->first();
      $next = $tiket ? $tiket->tiket_nomor : "-";
      $list[] = [
        "prefix" => $poli->poli_prefix,
        "next" => $next,
      ];
    }

    return $list;
  }

  public function register(Request $request)
  {
    $poli_id = $request->get("poli_id");
    $is_prioritas = $request->get("is_prioritas");
    $nomer_peserta = $request->get("nomer_peserta");
    $success = true;
    $error = 0;
    $tiket_date = date("Y-m-d");

    do {
      $nomor_antri_poli = $this->get_nomor_poli($poli_id, $is_prioritas, $tiket_date);
      try {
        $tiket = [
          "tiket_id" => Uuid::uuid1(),
          "tiket_nomor" => $nomor_antri_poli->nomor,
          "tiket_created" => date("Y-m-d H:i:s"),
          "tiket_poli_id" => $poli_id,
          "tiket_date" => $tiket_date,
          "tiket_prioritas" => $is_prioritas ? 1 : 0,
          "tiket_pasien_asuransi" => $nomer_peserta
        ];

        // jika sebelum masuk poli tersebut harus masuk ruang tensi dahulu
        $poli = Poli::find($poli_id);
        if ($poli && $poli->poli_tensi_id) {
          $tiket["tiket_tensi_id"] = $poli->poli_tensi_id;
        }
        if ($poli && $poli->poli_farmasi_id) {
          $tiket["tiket_farmasi_id"] = $poli->poli_farmasi_id;
        }

        Tiket::insert($tiket);

        $success = true;
      } catch (\Illuminate\Database\QueryException $exception) {
        if (
          isset($exception->errorInfo[1]) &&
          $exception->errorInfo[1] == "1062"
        ) {
          $success = false;
        }
        $error++;
      }

      if ($error > 2) {
        return "failed";
      }
    } while ($success == false);

    return $nomor_antri_poli->nomor;
  }
  // end new register

  public function antrian()
  {
    if (Auth::user()->prioritas == "1") {
      $data = Tiket::with(["channel", "poli"])
        ->select([
          "tiket_nomor",
          "tiket_status",
          "tiket_acceptor",
          "tiket_poli_nomor",
          "tiket_poli_status",
          DB::raw(
            'DATE_FORMAT(tiket_created,"%Y-%m-%d") as tiket_date_created'
          ),
          DB::raw('DATE_FORMAT(tiket_created,"%H:%i:%s") as tiket_created'),
          DB::raw('DATE_FORMAT(tiket_accepted,"%H:%i:%s") as tiket_accepted'),
          DB::raw("tiket_call as panggil_ulang"),
          "tiket_tensi_id",
          "tiket_poli_id",
          "tiket_channel_id",
          "tiket_pasien_nik",
        ])
        ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
        ->where(function ($query) {
          $query
            ->where(
              "ant_tiket.tiket_loket",
              "like",
              "%" . Auth::user()->loket . "%"
            )
            ->orWhereNull("ant_tiket.tiket_loket");
        })
        ->where("ant_tiket.tiket_prioritas", "1")
        ->orderBy("ant_tiket.tiket_call", "DESC")
        ->orderBy("ant_tiket.tiket_prioritas", "DESC")
        ->orderBy("ant_tiket.tiket_created", "ASC")
        ->get();
    } else {
      $data = Tiket::with(["channel", "poli"])
        ->select([
          "tiket_nomor",
          "tiket_status",
          "tiket_acceptor",
          "tiket_poli_nomor",
          "tiket_poli_status",
          DB::raw(
            'DATE_FORMAT(tiket_created,"%Y-%m-%d") as tiket_date_created'
          ),
          DB::raw('DATE_FORMAT(tiket_created,"%H:%i:%s") as tiket_created'),
          DB::raw('DATE_FORMAT(tiket_accepted,"%H:%i:%s") as tiket_accepted'),
          DB::raw("tiket_call as panggil_ulang"),
          "tiket_tensi_id",
          "tiket_poli_id",
          "tiket_channel_id",
          "tiket_pasien_nik",
        ])
        ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
        ->where(function ($query) {
          $query
            ->where(
              "ant_tiket.tiket_loket",
              "like",
              "%" . Auth::user()->loket . "%"
            )
            ->orWhereNull("ant_tiket.tiket_loket");
        })
        ->orderBy("ant_tiket.tiket_call", "DESC")
        ->orderBy("ant_tiket.tiket_prioritas", "DESC")
        ->orderBy("ant_tiket.tiket_created", "ASC")
        ->get();
    }

    return json_encode($data);
  }

  public function next(Request $request)
  {
    $poli = $request->get("poli");
    $next = $request->get("next");

    Tiket::where("tiket_nomor", $next)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->update([
        "tiket_call" => 1,
        "tiket_call_datetime" => date("Y-m-d H:i:s"),
        "tiket_loket" => Auth::User()->loket,
      ]);

    Tiket::where("tiket_nomor", $poli["tiket_nomor"])
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->update([
        "tiket_status" => empty($poli["tiket_poli_nomor"]) ? 2 : 1,
      ]);

    return json_encode(["res" => 1]);
  }

  public function call(Request $request)
  {
    $nomor = $request->get("nomor");
    $call = $request->get("call");

    Tiket::where("tiket_nomor", $nomor)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->update([
        "tiket_call" => $call,
        "tiket_call_datetime" => date("Y-m-d H:i:s"),
        "tiket_loket" => Auth::User()->loket,
      ]);
  }

  public function end(Request $request)
  {
    $poli = $request->get("poli");

    Tiket::where("tiket_nomor", $poli["tiket_nomor"])
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->update([
        "tiket_status" => empty($poli["tiket_poli_nomor"]) ? 2 : 1,
      ]);
  }

  public function antrian_detail($nik)
  {
    $data = Pasien::find($nik);
    return json_encode($data);
  }
}