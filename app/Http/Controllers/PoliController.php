<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Tiket;
use App\Poli;
use Illuminate\Support\Facades\DB;
use Exception;

class PoliController extends Controller
{
  private $poli_umum_id = 1;

  public function list_menu()
  {
    $data = Poli::select([
      "poli_color",
      "poli_icon",
      "poli_deskripsi",
      "poli_id",
      "poli_kuota",
      "poli_nama",
      "poli_prefix",
      "poli_closehour",
      "poli_prioritas",
      "poli_hide_on_prioritas",
      DB::raw(
        "FIND_IN_SET(DAYOFWEEK(NOW()),replace(poli_dayopen, ';', ',')) as buka"
      ),
    ])
      ->where("poli_status", 1)
      ->where("poli_hide_on_register", 0)
      ->orderBy("poli_id", "asc")
      ->get();

    $list = [];
    foreach ($data as $d) {
      $list[$d->poli_id] = $d;
    }

    //check kuota
    $query = "SELECT
				p.poli_id,

				CASE WHEN p.poli_kuota = 0 THEN 0
				ELSE (p.poli_kuota - count(t.tiket_id))
				END as sisa

				FROM ant_poli p
				LEFT JOIN ant_tiket t ON
					p.poli_id = t.tiket_poli_id AND
					DATE_FORMAT(t.tiket_date,'%Y-%m-%d') = :date AND
					tiket_status != '2' AND tiket_poli_status != '2'
				GROUP BY p.poli_id";
    $data = DB::select($query, ["date" => date("Y-m-d")]);

    foreach ($data as $row) {
      if (isset($list[$row->poli_id])) {
        $poli_kuota = $list[$row->poli_id]->poli_kuota;

        if ($poli_kuota <= 0) {
          $sisa = 1000;
        } else {
          $sisa = $row->sisa;
          $list[$row->poli_id]->poli_deskripsi .= " (sisa kuota {$sisa})";
        }
        $list[$row->poli_id]->sisa_kuota = $sisa;
      }
    }

    return json_encode($list);
  }

  public function summary(Request $request)
  {
    $req = $request->all();

    $poli = Poli::select(["poli_id", "poli_nama", "poli_is_tensi"])
      ->where("poli_prioritas", "0")
      ->where("poli_gedung", $req["poli_gedung"])
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
      ->where("poli_gedung", $req["poli_gedung"])
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

  public function register(Request $request)
  {
    $poli = $request->get("poli");
    $value = $request->get("value");

    $tiket = Tiket::where(
      DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'),
      date("Y-m-d")
    )
      ->where("tiket_nomor", $poli["tiket_nomor"])
      ->first();

    if ($value) {
      $tiket->tiket_status = $poli["tiket_status"] == 2 ? 1 : 0; //jika register ketika sudah terlewat (terlewat => status 2)
      $tiket->tiket_acceptor = Auth::user()->id;
      $tiket->tiket_accepted = date("Y-m-d H:i:s");
      $tiket->tiket_poli_nomor = $poli["tiket_nomor"];

      // jika masuk tensi dulu maka nomor tensi juga diisi
      if (!is_null($tiket->tiket_tensi_id)) {
        $tiket->tiket_tensi_nomor = $poli["tiket_nomor"];
      }
    } else {
      $tiket->tiket_status = 0;
      $tiket->tiket_acceptor = null;
      $tiket->tiket_accepted = null;
      $tiket->tiket_poli_nomor = null;
      $tiket->tiket_tensi_nomor = null;
    }
    $tiket->save();

    return $poli["tiket_nomor"];
  }

  public function antrian()
  {
    if (Auth::user()->prioritas == "1") {
      $data = Tiket::select([
        "tiket_nomor",
        "tiket_status",
        "tiket_poli_nomor",
        "tiket_poli_status",
        "tiket_poli_acceptor",
        "tiket_pasien_dirujuk",
        "ant_poli.poli_id",
        "ant_poli.poli_nama",
        DB::raw('DATE_FORMAT(tiket_accepted,"%H:%i:%s") as tiket_accepted'),
        DB::raw(
          'DATE_FORMAT(tiket_poli_accepted,"%H:%i:%s") as tiket_poli_accepted'
        ),
        DB::raw("tiket_poli_call as panggil_ulang"),
      ])
        ->join("ant_poli", "ant_tiket.tiket_poli_id", "ant_poli.poli_id")
        ->where(["tiket_poli_id" => Auth::user()->poli])
        ->where("tiket_prioritas", "1")
        ->where("tiket_status", "1")
        ->where("tiket_tensi_status", "1")
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),
          date("Y-m-d")
        )
        ->orderBy("ant_tiket.tiket_poli_call", "DESC")
        ->orderBy("ant_tiket.tiket_prioritas", "DESC")
        ->orderBy("ant_tiket.tiket_poli_nomor", "ASC")
        ->get();
    } else {
      $data = Tiket::select([
        "tiket_nomor",
        "tiket_status",
        "tiket_poli_nomor",
        "tiket_poli_status",
        "tiket_poli_acceptor",
        "tiket_pasien_dirujuk",
        "ant_poli.poli_id",
        "ant_poli.poli_nama",
        DB::raw('DATE_FORMAT(tiket_accepted,"%H:%i:%s") as tiket_accepted'),
        DB::raw(
          'DATE_FORMAT(tiket_poli_accepted,"%H:%i:%s") as tiket_poli_accepted'
        ),
        DB::raw("tiket_poli_call as panggil_ulang"),
      ])
        ->join("ant_poli", "ant_tiket.tiket_poli_id", "ant_poli.poli_id")
        ->where(["tiket_poli_id" => Auth::user()->poli])
        ->where("tiket_prioritas", "0")
        ->where("tiket_status", "1")
        ->where("tiket_tensi_status", "1")
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),
          date("Y-m-d")
        )
        ->orderBy("ant_tiket.tiket_poli_call", "DESC")
        ->orderBy("ant_tiket.tiket_poli_nomor", "ASC")
        ->get();
    }

    return json_encode($data);
  }

  public function next(Request $request)
  {
    $poli = $request->get("poli");
    $next = $request->get("next");

    Tiket::where("tiket_poli_nomor", $next)
      ->where(
        DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

        date("Y-m-d")
      )
      ->where(["tiket_poli_id" => Auth::user()->poli])
      ->update([
        "tiket_poli_call" => 1,
        "tiket_poli_call_datetime" => date("Y-m-d H:i:s"),
      ]);

    Tiket::where("tiket_poli_nomor", $poli["tiket_poli_nomor"])
      ->where(
        DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

        date("Y-m-d")
      )
      ->where(["tiket_poli_id" => Auth::user()->poli])
      ->update([
        "tiket_poli_status" => empty($poli["tiket_poli_acceptor"]) ? 2 : 1,
      ]);

    return json_encode(["res" => 1]);
  }

  public function call(Request $request)
  {
    $nomor = $request->get("nomor");
    $call = $request->get("call");

    Tiket::where("tiket_poli_nomor", $nomor)
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->update([
        "tiket_poli_call" => $call,
        "tiket_poli_call_datetime" => date("Y-m-d H:i:s"),
      ]);
  }

  public function end(Request $request)
  {
    $poli = $request->get("poli");

    $res = Tiket::where("tiket_poli_nomor", $poli["tiket_poli_nomor"])
      ->where(DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'), date("Y-m-d"))
      ->where(["tiket_poli_id" => Auth::user()->poli])
      ->update([
        "tiket_poli_status" => empty($poli["tiket_poli_acceptor"]) ? 2 : 1,
      ]);
  }

  public function attend(Request $request)
  {
    $poli = $request->get("poli");
    $value = $request->get("value");

    if ($value) {
      Tiket::where("tiket_poli_nomor", $poli["tiket_poli_nomor"])
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

          date("Y-m-d")
        )
        ->where(["tiket_poli_id" => Auth::user()->poli])
        ->update([
          "tiket_poli_status" => $poli["tiket_poli_status"] == 2 ? 1 : 0, //jika datang ketika sudah terlewat (terlewat => status 1 or 2)
          "tiket_poli_acceptor" => Auth::user()->id,
          "tiket_poli_accepted" => date("Y-m-d H:i:s"),
        ]);
    } else {
      Tiket::where("tiket_poli_nomor", $poli["tiket_poli_nomor"])
        ->where(
          DB::raw('DATE_FORMAT(tiket_accepted,"%Y-%m-%d")'),

          date("Y-m-d")
        )
        ->where(["tiket_poli_id" => Auth::user()->poli])
        ->update([
          "tiket_poli_status" => $poli["tiket_poli_status"] == 1 ? 2 : 0, //jika datang ketika sudah terlewat (terlewat => status 1 or 2)
          "tiket_poli_acceptor" => null,
          "tiket_poli_accepted" => null,
        ]);
    }

    return json_encode(["res" => 1]);
  }

  public function rujuk(Request $request)
  {
    $poli = $request->get("poli");
    $value = $request->get("value");

    Tiket::where("tiket_poli_nomor", $poli["tiket_poli_nomor"])
      ->where(["tiket_poli_id" => Auth::user()->poli])
      ->update([
        "tiket_pasien_dirujuk" => $value,
      ]);

    return json_encode(["res" => 1]);
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
      ->where("poli_prioritas", 0)
      ->where("poli_hide_on_register", 0)
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

  public function summary_rujukan()
  {
    $data = Tiket::select(["ant_poli.poli_id", DB::raw("COUNT(*) as total")])
      ->join("ant_poli", "ant_tiket.tiket_poli_id", "ant_poli.poli_id")
      ->where(DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'), date("Y-m-d"))
      ->where("ant_tiket.tiket_poli_status", 1)
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

  //setting
  public function list_all()
  {
    $data = Poli::select()
      ->orderBy("poli_nama", "ASC")
      ->get();
    $list = [];
    foreach ($data as $k => $d) {
      $d["poli_dayopen"] = empty($d["poli_dayopen"])
        ? []
        : explode(";", $d["poli_dayopen"]);
      $list[$d->poli_id] = $d;
    }
    return json_encode($list);
  }

  public function del(Request $request)
  {
    $id = $request->get("poli_id");
    $res = Poli::where("poli_id", $id)->delete() ? "SUCCESS" : "FAILED";

    return json_encode(["res" => $res]);
  }

  public function save(Request $request)
  {
    $poli = $request->get("poli");

    if (is_array($poli["poli_dayopen"])) {
      sort($poli["poli_dayopen"]);
    }

    $poli["poli_kuota"] = isset($poli["poli_kuota"]) ? $poli["poli_kuota"] : 0;
    $poli["poli_status"] = isset($poli["poli_status"])
      ? $poli["poli_status"]
      : 0;
    $poli["poli_dayopen"] = implode(";", $poli["poli_dayopen"]);

    if (isset($poli["poli_id"])) {
      $poli["poli_modified"] = date("Y-m-d H:i:s");
      $poli["poli_modifier"] = Auth::User()->email;
      $res = Poli::where("poli_id", $poli["poli_id"])->update($poli);
    } else {
      $poli["poli_created"] = date("Y-m-d H:i:s");
      $poli["poli_author"] = Auth::User()->email;
      $res = Poli::create($poli);
    }

    $res = $res ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }
}
