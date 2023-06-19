<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Tiket;
use App\Poli;
use Illuminate\Support\Facades\DB;

class TensiController extends Controller
{
  public function antrian()
  {
    if (Auth::user()->prioritas == "1") {
      $data = Tiket::select([
        "tiket_nomor",
        "tiket_status",
        "tiket_poli_id",
        "tiket_tensi_nomor",
        "tiket_tensi_status",
        "tiket_tensi_acceptor",
        "tiket_pasien_dirujuk",
        "ant_poli.poli_id",
        "ant_poli.poli_nama",
        DB::raw('DATE_FORMAT(tiket_accepted,"%H:%i:%s") as tiket_accepted'),
        DB::raw(
          'DATE_FORMAT(tiket_tensi_accepted,"%H:%i:%s") as tiket_tensi_accepted'
        ),
        DB::raw("tiket_tensi_call as panggil_ulang"),
      ])
        ->join("ant_poli", "ant_tiket.tiket_tensi_id", "ant_poli.poli_id")
        ->where(["tiket_tensi_id" => Auth::user()->tensi])
        ->where("tiket_prioritas", "1")
        ->where("tiket_status", "1")
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),
          date("Y-m-d")
        )
        ->orderBy("ant_tiket.tiket_tensi_call", "DESC")
        ->orderBy("ant_tiket.tiket_prioritas", "DESC")
        ->orderBy("ant_tiket.tiket_tensi_nomor", "ASC")
        ->get();
    } else {
      $data = Tiket::select([
        "tiket_nomor",
        "tiket_status",
        "tiket_poli_id",
        "tiket_tensi_nomor",
        "tiket_tensi_status",
        "tiket_tensi_acceptor",
        "tiket_pasien_dirujuk",
        "ant_poli.poli_id",
        "ant_poli.poli_nama",
        DB::raw('DATE_FORMAT(tiket_accepted,"%H:%i:%s") as tiket_accepted'),
        DB::raw(
          'DATE_FORMAT(tiket_tensi_accepted,"%H:%i:%s") as tiket_tensi_accepted'
        ),
        DB::raw("tiket_tensi_call as panggil_ulang"),
      ])
        ->join("ant_poli", "ant_tiket.tiket_tensi_id", "ant_poli.poli_id")
        ->where(["tiket_tensi_id" => Auth::user()->tensi])
        ->where("tiket_prioritas", "0")
        ->where("tiket_status", "1")
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),
          date("Y-m-d")
        )
        ->orderBy("ant_tiket.tiket_tensi_call", "DESC")
        ->orderBy("ant_tiket.tiket_tensi_nomor", "ASC")
        ->get();
    }

    return json_encode($data);
  }

  public function next(Request $request)
  {
    $poli = $request->get("poli");
    $next = $request->get("next");

    Tiket::where("tiket_tensi_nomor", $next)
      ->where(
        DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

        date("Y-m-d")
      )
      ->where(["tiket_tensi_id" => Auth::user()->tensi])
      ->update([
        "tiket_tensi_call" => 1,
        "tiket_tensi_call_datetime" => date("Y-m-d H:i:s"),
      ]);

    Tiket::where("tiket_tensi_nomor", $poli["tiket_tensi_nomor"])
      ->where(
        DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

        date("Y-m-d")
      )
      ->where(["tiket_tensi_id" => Auth::user()->tensi])
      ->update([
        "tiket_tensi_status" => empty($poli["tiket_tensi_acceptor"]) ? 2 : 1,
      ]);

    return json_encode(["res" => 1]);
  }

  public function call(Request $request)
  {
    $nomor = $request->get("nomor");
    $call = $request->get("call");

    Tiket::where("tiket_tensi_nomor", $nomor)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->update([
        "tiket_tensi_call" => $call,
        "tiket_tensi_call_datetime" => date("Y-m-d H:i:s"),
      ]);
  }

  public function end(Request $request)
  {
    $poli = $request->get("poli");

    $res = Tiket::where("tiket_tensi_nomor", $poli["tiket_tensi_nomor"])
      ->where(DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'), date("Y-m-d"))
      ->where(["tiket_tensi_id" => Auth::user()->tensi])
      ->update([
        "tiket_tensi_status" => empty($poli["tiket_tensi_acceptor"]) ? 2 : 1,
      ]);
  }

  public function attend(Request $request)
  {
    $poli = $request->get("poli");
    $value = $request->get("value");

    if ($value) {
      Tiket::where("tiket_tensi_nomor", $poli["tiket_tensi_nomor"])
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

          date("Y-m-d")
        )
        ->where(["tiket_tensi_id" => Auth::user()->tensi])
        ->update([
          "tiket_tensi_status" => $poli["tiket_tensi_status"] == 2 ? 1 : 0, //jika datang ketika sudah terlewat (terlewat => status 1 or 2)
          "tiket_tensi_acceptor" => Auth::user()->id,
          "tiket_tensi_accepted" => date("Y-m-d H:i:s"),
        ]);
    } else {
      Tiket::where("tiket_tensi_nomor", $poli["tiket_tensi_nomor"])
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

          date("Y-m-d")
        )
        ->where(["tiket_tensi_id" => Auth::user()->tensi])
        ->update([
          "tiket_tensi_status" => $poli["tiket_tensi_status"] == 1 ? 2 : 0, //jika datang ketika sudah terlewat (terlewat => status 1 or 2)
          "tiket_tensi_acceptor" => null,
          "tiket_tensi_accepted" => null,
        ]);
    }

    return json_encode(["res" => 1]);
  }

  public function summary_rujukan()
  {
    $data = Tiket::select(["ant_poli.poli_id", DB::raw("COUNT(*) as total")])
      ->join("ant_poli", "ant_tiket.tiket_tensi_id", "ant_poli.poli_id")
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->where("ant_tiket.tiket_tensi_status", 1)
      ->where("ant_tiket.tiket_pasien_dirujuk", 1)
      ->groupBy("ant_poli.poli_id")
      ->pluck("total", "ant_poli.poli_id")
      ->toArray();

    $poli = Poli::select(["poli_id", "poli_nama"])
      ->where("poli_prioritas", "0")
      ->where("poli_hide_on_register", 0)
      ->orderBy("poli_nama", "ASC")
      ->get();

    $list = [];
    $total = 0;
    foreach ($poli as $d) {
      $t = isset($data[$d->poli_id]) ? $data[$d->poli_id] : 0;
      $list[] = [
        "poli_nama" => $d->poli_nama,
        "total" => $t,
      ];
      $total += $t;
    }

    return ["list" => $list, "total" => $total];
  }

  public function list()
  {
    $data = Tiket::select(["ant_poli.poli_id", DB::raw("COUNT(*) as total")])
      ->join("ant_poli", "ant_tiket.tiket_poli_id", "ant_poli.poli_id")
      ->where(
        DB::raw('DATE_FORMAT(ant_tiket.tiket_accepted,"%Y-%m-%d")'),
        date("Y-m-d")
      )
      ->groupBy("ant_poli.poli_id")
      ->pluck("total", "ant_poli.poli_id")
      ->toArray();

    $poli = Poli::select(["poli_id", "poli_nama"])
      ->where("poli_is_tensi", 1)
      ->orderBy("poli_nama", "ASC")
      ->get();

    $list = [];
    $total = 0;
    foreach ($poli as $d) {
      $t = isset($data[$d->poli_id]) ? $data[$d->poli_id] : 0;
      $list[] = [
        "poli_id" => $d->poli_id,
        "poli_nama" => $d->poli_nama,
        "total" => $t,
      ];
      $total += $t;
    }

    return ["list" => $list, "total" => $total];
  }
}
