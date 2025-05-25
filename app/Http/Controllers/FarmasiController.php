<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Tiket;
use App\Poli;
use Illuminate\Support\Facades\DB;
use Exception;

class FarmasiController extends Controller
{
    // private $poli_umum_id = 1;

  public function displayFarmasi()
  {
    $poli = Poli::all();

    return view('antrian.display-farmasi', [
        'poli' => $poli,
    ]);
  }

    public function antrian()
{
    $data = Tiket::select([
            'ant_tiket.tiket_nomor',
            'ant_tiket.tiket_status',
            'ant_tiket.tiket_poli_id',
            'ant_tiket.tiket_farmasi_nomor',
            'ant_tiket.tiket_farmasi_status',
            'ant_tiket.tiket_farmasi_acceptor',
            'ant_tiket.tiket_pasien_dirujuk',
            'ant_poli.poli_id',
            'ant_poli.poli_nama',
            DB::raw('DATE_FORMAT(ant_tiket.tiket_accepted, "%H:%i:%s") as tiket_accepted'),
            DB::raw('DATE_FORMAT(ant_tiket.tiket_farmasi_accepted, "%H:%i:%s") as tiket_farmasi_accepted'),
            DB::raw('ant_tiket.tiket_farmasi_call as panggil_ulang'),
        ])
        ->join('ant_poli', 'ant_tiket.tiket_farmasi_id', '=', 'ant_poli.poli_id')
        ->where('ant_tiket.tiket_farmasi_id', Auth::user()->poli)
        ->where('ant_tiket.tiket_status', '1')
        ->whereDate('ant_tiket.tiket_accepted', date('Y-m-d'))
        ->orderByDesc('ant_tiket.tiket_farmasi_call')
        ->orderBy('ant_tiket.tiket_farmasi_nomor', 'ASC')
        ->get();

    return response()->json($data);
}

  public function next(Request $request)
  {
    $poli = $request->get("poli");
    $next = $request->get("next");

    Tiket::where("tiket_farmasi_nomor", $next)
      ->where(
        DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

        date("Y-m-d")
      )
      ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
      ->update([
        "tiket_farmasi_call" => 1,
        "tiket_farmasi_call_datetime" => date("Y-m-d H:i:s"),
      ]);

    Tiket::where("tiket_farmasi_nomor", $poli["tiket_farmasi_nomor"])
      ->where(
        DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

        date("Y-m-d")
      )
      ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
      ->update([
        "tiket_farmasi_status" => empty($poli["tiket_farmasi_acceptor"]) ? 2 : 1,
      ]);

    return json_encode(["res" => 1]);
  }

  public function call(Request $request)
  {
    $nomor = $request->get("nomor");
    $call = $request->get("call");

    Tiket::where("tiket_farmasi_nomor", $nomor)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->update([
        "tiket_farmasi_call" => $call,
        "tiket_farmasi_call_datetime" => date("Y-m-d H:i:s"),
      ]);
  }

  public function end(Request $request)
  {
    $poli = $request->get("poli");

    $res = Tiket::where("tiket_farmasi_nomor", $poli["tiket_farmasi_nomor"])
      ->where(DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'), date("Y-m-d"))
      ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
      ->update([
        "tiket_farmasi_status" => empty($poli["tiket_farmasi_acceptor"]) ? 2 : 1,
      ]);
  }

  public function attend(Request $request)
  {
    $poli = $request->get("poli");
    $value = $request->get("value");

    if ($value) {
      Tiket::where("tiket_farmasi_nomor", $poli["tiket_farmasi_nomor"])
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

          date("Y-m-d")
        )
        ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
        ->update([
          "tiket_farmasi_status" => $poli["tiket_farmasi_status"] == 2 ? 1 : 0, //jika datang ketika sudah terlewat (terlewat => status 1 or 2)
          "tiket_farmasi_acceptor" => Auth::user()->id,
          "tiket_farmasi_accepted" => date("Y-m-d H:i:s"),
        ]);
    } else {
      Tiket::where("tiket_farmasi_nomor", $poli["tiket_farmasi_nomor"])
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

          date("Y-m-d")
        )
        ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
        ->update([
          "tiket_farmasi_status" => $poli["tiket_farmasi_status"] == 1 ? 2 : 0, //jika datang ketika sudah terlewat (terlewat => status 1 or 2)
          "tiket_farmasi_acceptor" => null,
          "tiket_farmasi_accepted" => null,
        ]);
    }

    return json_encode(["res" => 1]);
  }

  public function summary_rujukan()
  {
    $data = Tiket::select(["ant_poli.poli_id", DB::raw("COUNT(*) as total")])
      ->join("ant_poli", "ant_tiket.tiket_farmasi_id", "ant_poli.poli_id")
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->where("ant_tiket.tiket_farmasi_status", 1)
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
      ->where("poli_is_farmasi", 1)
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
