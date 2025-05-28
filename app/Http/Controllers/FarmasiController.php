<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Tiket;
use App\Poli;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class FarmasiController extends Controller
{
    private $poli_umum_id = 1;

  public function displayFarmasi()
  {
    $poli = Poli::all();

    return view('antrian.display-farmasi', [
        'poli' => $poli,
    ]);
  }
  
  public function nomor_current()
  {
    $nomor = "-";

    $tiket = Tiket::select("tiket_farmasi_nomor")
      ->where("tiket_farmasi_call", ">", 0)
      ->where("tiket_farmasi_status", "=", 0)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->orderBy("tiket_farmasi_call_datetime", "DESC")
      ->first();
      $nomor = empty($tiket->tiket_farmasi_nomor) ? $nomor : $tiket->tiket_farmasi_nomor;
      return $nomor;
    }
    
  public function all_nomor()
  {
    $tiket = Tiket::select("tiket_farmasi_nomor")
      ->where("tiket_farmasi_status", "=", 0)
      ->where("tiket_poli_status", ">", 0)
      ->where("tiket_farmasi_call", "=", 0)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->orderBy("tiket_farmasi_call_datetime", "DESC")
      ->get();

    Log::info($tiket);
    return $tiket;
  }

  public function antrian()
  {
      $data = Tiket::select([
        "tiket_nomor",
        "tiket_status",
        "tiket_poli_id",
        "tiket_farmasi_id",
        "tiket_farmasi_nomor",
        "tiket_farmasi_status",
        "tiket_farmasi_acceptor",
        "tiket_pasien_dirujuk",
        "ant_poli.poli_id",
        "ant_poli.poli_nama",
              DB::raw('DATE_FORMAT(tiket_accepted, "%H:%i:%s") as tiket_accepted'),
              DB::raw('DATE_FORMAT(tiket_farmasi_accepted, "%H:%i:%s") as tiket_farmasi_accepted'),
              DB::raw('tiket_farmasi_call as panggil_ulang'),
          ])
          ->join('ant_poli', 'ant_tiket.tiket_poli_id', '=', 'ant_poli.poli_id')
          ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
          ->where('tiket_status', '1')
          ->where('tiket_poli_status', '1')
          // ->whereDate('ant_tiket.tiket_farmasi_accepted', date('Y-m-d'))
          // ->orderByDesc('ant_tiket.tiket_farmasi_call')
          ->orderBy('ant_tiket.tiket_farmasi_nomor', 'ASC')
          ->get();

      return response()->json($data);
  }

  public function next(Request $request)
  {
    $farmasi = $request->get("farmasi");
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

    Tiket::where("tiket_farmasi_nomor", $farmasi["tiket_farmasi_nomor"])
      ->where(
        DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

        date("Y-m-d")
      )
      ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
      ->update([
        "tiket_farmasi_status" => empty($farmasi["tiket_farmasi_acceptor"]) ? 2 : 1,
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
    $farmasi = $request->get("farmasi");

    $res = Tiket::where("tiket_farmasi_nomor", $farmasi["tiket_farmasi_nomor"])
      ->where(DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'), date("Y-m-d"))
      ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
      ->update([
        "tiket_farmasi_status" => empty($farmasi["tiket_farmasi_acceptor"]) ? 2 : 1,
      ]);
  }

  public function attend(Request $request)
  {
    $farmasi = $request->get("farmasi");
    $value = $request->get("value");
    Log::info($request->all());
    if ($value) {
      Log::info('eksekusi bagian true');
      Tiket::where("tiket_farmasi_nomor", $farmasi["tiket_farmasi_nomor"])
      ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),
          
          date("Y-m-d")
        )
        ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
        ->update([
          "tiket_farmasi_status" => $farmasi["tiket_farmasi_status"] == 2 ? 1 : 0, //jika datang ketika sudah terlewat (terlewat => status 1 or 2)
          "tiket_farmasi_acceptor" => Auth::user()->id,
          "tiket_farmasi_accepted" => date("Y-m-d H:i:s"),
        ]);
        Log::info('selesai eksekusi bagian true');
      } else {
        Tiket::where("tiket_farmasi_nomor", $farmasi["tiket_farmasi_nomor"])
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

          date("Y-m-d")
        )
        ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
        ->update([
          "tiket_farmasi_status" => $farmasi["tiket_farmasi_status"] == 1 ? 2 : 0, //jika datang ketika sudah terlewat (terlewat => status 1 or 2)
          "tiket_farmasi_acceptor" => null,
          "tiket_farmasi_accepted" => null,
        ]);
    }
    
    return json_encode(["res" => 1]);
  }

  public function rujuk(Request $request)
  {
    $farmasi = $request->get("farmasi");
    $value = $request->get("value");

    Tiket::where("tiket_farmasi_nomor", $farmasi["tiket_farmasi_nomor"])
      ->where(["tiket_farmasi_id" => Auth::user()->farmasi])
      ->update([
        "tiket_pasien_dirujuk" => $value,
      ]);

    return json_encode(["res" => 1]);
  }

  
  public function summary(Request $request)
  {
    $req = $request->all();

    $poli = Poli::select(["poli_id", "poli_nama", "poli_is_tensi"])
      ->where("poli_prioritas", "0")
      ->where("poli_gedung", $req["poli_gedung"] ?? 1)
      ->orderBy("poli_nama", "ASC")
      ->get();

    $list = [];
    foreach ($poli as $d) {
      $field_poli_call = "tiket_poli_call";
      $field_poli_id = "tiket_poli_id";

      if ($d->poli_is_tensi == "1") {
        $field_poli_call = "tiket_tensi_call";
        $field_poli_id = "tiket_tensi_id";
      }

      if (
        $this->poli_umum_id == $d->poli_id &&
        config("antrian.umum_prioritas")
      ) {
        $sisa = Tiket::select()
          ->join("ant_poli", "ant_tiket.{$field_poli_id}", "ant_poli.poli_id")
          ->where("ant_tiket.{$field_poli_call}", 0)
          ->where("ant_poli.poli_id", $d->poli_id)
          ->where("ant_tiket.tiket_prioritas", "0")
          ->where(
            DB::raw('DATE_FORMAT(ant_tiket.tiket_accepted,"%Y-%m-%d")'),
            date("Y-m-d")
          )
          ->groupBy("ant_poli.poli_id")
          ->count();

        $list[] = [
          "poli_id" => $d->poli_id,
          "poli_nama" => $d->poli_nama,
          "sisa" => $sisa,
        ];

        $sisa = Tiket::select()
          ->join("ant_poli", "ant_tiket.{$field_poli_id}", "ant_poli.poli_id")
          ->where("ant_tiket.{$field_poli_call}", 0)
          ->where("ant_poli.poli_id", $d->poli_id)
          ->where("ant_tiket.tiket_prioritas", "1")
          ->where(
            DB::raw('DATE_FORMAT(ant_tiket.tiket_accepted,"%Y-%m-%d")'),
            date("Y-m-d")
          )
          ->groupBy("ant_poli.poli_id")
          ->count();

        $list[] = [
          "poli_id" => $d->poli_id,
          "poli_nama" => "UMUM PRIORITAS",
          "sisa" => $sisa,
        ];
      } else {
        $sisa = Tiket::select()
          ->join("ant_poli", "ant_tiket.{$field_poli_id}", "ant_poli.poli_id")
          ->where("ant_tiket.{$field_poli_call}", 0)
          ->where("ant_poli.poli_id", $d->poli_id)
          ->where(
            DB::raw('DATE_FORMAT(ant_tiket.tiket_accepted,"%Y-%m-%d")'),
            date("Y-m-d")
          )
          ->groupBy("ant_poli.poli_id")
          ->count();

        $list[] = [
          "poli_id" => $d->poli_id,
          "poli_nama" => $d->poli_nama,
          "sisa" => $sisa,
        ];
      }
    }

    return json_encode($list);
  }

  public function summary_rujukan()
  {
    $data = Tiket::select(["ant_poli.poli_id", DB::raw("COUNT(*) as total")])
      ->join("ant_poli", "ant_tiket.tiket_poli_id", "ant_poli.poli_id")
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

  
  public function list_display(Request $request)
  {
    $req = $request->all();

    $poli = Poli::select([
      "poli_id",
      "poli_color",
      "poli_nama",
      "poli_audio",
      "poli_gedung",
      "poli_is_tensi",
    ])
      // ->where("poli_gedung", $req["poli_gedung"])
      ->where("poli_prioritas", "0")
      ->orderBy("poli_nama", "ASC")
      ->get();

    $list = [];
    foreach ($poli as $d) {
      $field_poli_call = "tiket_poli_call";
      $field_poli_id = "tiket_poli_id";
      $field_nomor = "tiket_poli_nomor";
      $field_poli_call_datetime = "tiket_poli_call_datetime";

      if ($d->poli_is_tensi == "1") {
        $field_poli_call = "tiket_tensi_call";
        $field_poli_id = "tiket_tensi_id";
        $field_nomor = "tiket_tensi_nomor";
        $field_poli_call_datetime = "tiket_tensi_call_datetime";
      }

      if (
        $d->poli_id == $this->poli_umum_id &&
        config("antrian.umum_prioritas")
      ) {
        // poli umum
        $tiket = Tiket::select(["tiket_poli_nomor", "tiket_tensi_nomor"])
          ->where($field_poli_call, ">", 0)
          ->where($field_poli_id, $d->poli_id)
          ->where("tiket_prioritas", "0")
          ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
          ->orderBy($field_poli_call_datetime, "DESC")
          ->first();

        $nomor = $tiket ? $tiket->$field_nomor : "-";
        $d->tiket_poli_nomor = $nomor;
        $list[$d->poli_id] = $d;

        // poli umum prioritas
        $tiket = Tiket::select(["tiket_poli_nomor", "tiket_tensi_nomor"])
          ->where($field_poli_call, ">", 0)
          ->where($field_poli_id, $d->poli_id)
          ->where("tiket_prioritas", "1")
          ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
          ->orderBy($field_poli_call_datetime, "DESC")
          ->first();

        $nomor = $tiket ? $tiket->$field_nomor : "-";
        $list[12] = (object) [
          "poli_id" => 12,
          "poli_color" => "",
          "poli_nama" => "UMUM PRIORITAS",
          "poli_audio" => $d->poli_audio,
          "poli_gedung" => $d->poli_gedung,
          "tiket_poli_nomor" => $nomor,
        ];
      } else {
        $tiket = Tiket::select(["tiket_poli_nomor", "tiket_tensi_nomor"])
          ->where($field_poli_call, ">", 0)
          ->where($field_poli_id, $d->poli_id)
          ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
          ->orderBy($field_poli_call_datetime, "DESC")
          ->first();

        $nomor = $tiket ? $tiket->$field_nomor : "-";
        $d->tiket_poli_nomor = $nomor;
        $list[$d->poli_id] = $d;
      }
    }

    return json_encode($list);
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

  public function get($id)
  {
    $res = Poli::where("poli_id", $id)->first();
    if ($res) {
      $res["poli_dayopen"] = empty($res["poli_dayopen"])
        ? []
        : explode(";", $res["poli_dayopen"]);
    }

    return json_encode($res);
  }

}
