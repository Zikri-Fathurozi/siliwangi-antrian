<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use App\Tiket;
use App\Pasien;
use App\Poli;
use Illuminate\Support\Facades\DB;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;

class ReportController extends Controller
{
  public function visit(Request $request)
  {
    $month = $request->get("month");
    $year = $request->get("year");

    $data = Tiket::select([
      "ant_poli.poli_id",
      DB::raw("DAY(ant_tiket.tiket_accepted) as day"),
      DB::raw("COUNT(*) as total"),
    ])
      ->join("ant_poli", "ant_tiket.tiket_poli_id", "ant_poli.poli_id")
      ->where(DB::raw("YEAR(ant_tiket.tiket_accepted)"), $year)
      ->where(DB::raw("MONTH(ant_tiket.tiket_accepted)"), $month)
      ->groupBy("ant_poli.poli_id", DB::raw("DAY(ant_tiket.tiket_accepted)"))
      ->get();

    $all = Tiket::select([
      DB::raw("DAY(ant_tiket.tiket_accepted) as day"),
      DB::raw("COUNT(*) as total"),
    ])
      ->where(DB::raw("YEAR(ant_tiket.tiket_accepted)"), $year)
      ->where(DB::raw("MONTH(ant_tiket.tiket_accepted)"), $month)
      ->groupBy(DB::raw("DAY(ant_tiket.tiket_accepted)"))
      ->get();

    $total = 0;

    if (count($all) > 0) {
      foreach ($all as $d) {
        $total += $d->total;
      }
      $total = round($total / count($all));
    }

    $poli = [];
    foreach ($data as $d) {
      if (isset($poli[$d->poli_id][$d->day])) {
        $poli[$d->poli_id][$d->day] += $d->total;
      } else {
        $poli[$d->poli_id][$d->day] = $d->total;
      }
    }

    $avg = [];
    foreach ($poli as $index => $days) {
      foreach ($days as $total) {
        if (isset($avg[$index])) {
          $avg[$index] += $total;
        } else {
          $avg[$index] = $total;
        }
      }
      $avg[$index] = round($avg[$index] / count($days));
    }

    $poli = Poli::select(["poli_id", "poli_nama", "poli_color"])
      ->where("poli_id", "!=", 4)
      ->orderBy("poli_nama", "ASC")
      ->get();

    $list = [];
    foreach ($poli as $d) {
      $t = isset($avg[$d->poli_id]) ? $avg[$d->poli_id] : 0;
      $list[] = [
        "poli_id" => $d->poli_id,
        "poli_nama" => $d->poli_nama,
        "poli_color" => $d->poli_color,
        "total" => $t,
      ];
    }

    return [
      "all" => $total,
      "poli" => $list,
    ];
  }

  public function rujukan(Request $request)
  {
    $month = $request->get("month");
    $year = $request->get("year");

    $data = Tiket::select([
      "ant_poli.poli_id",
      DB::raw("DAY(ant_tiket.tiket_accepted) as day"),
      DB::raw("COUNT(*) as total"),
    ])
      ->join("ant_poli", "ant_tiket.tiket_poli_id", "ant_poli.poli_id")
      ->where(DB::raw("YEAR(ant_tiket.tiket_accepted)"), $year)
      ->where(DB::raw("MONTH(ant_tiket.tiket_accepted)"), $month)
      ->where("ant_tiket.tiket_pasien_dirujuk", 1)
      ->groupBy("ant_poli.poli_id", DB::raw("DAY(ant_tiket.tiket_accepted)"))
      ->get();

    $all = Tiket::select([
      DB::raw("DAY(ant_tiket.tiket_accepted) as day"),
      DB::raw("COUNT(*) as total"),
    ])
      ->where(DB::raw("YEAR(ant_tiket.tiket_accepted)"), $year)
      ->where(DB::raw("MONTH(ant_tiket.tiket_accepted)"), $month)
      ->where("ant_tiket.tiket_pasien_dirujuk", 1)
      ->groupBy(DB::raw("DAY(ant_tiket.tiket_accepted)"))
      ->get();

    $total = 0;

    if (count($all) > 0) {
      foreach ($all as $d) {
        $total += $d->total;
      }
      $total = round($total / count($all));
    }

    $poli = [];
    foreach ($data as $d) {
      if (isset($poli[$d->poli_id][$d->day])) {
        $poli[$d->poli_id][$d->day] += $d->total;
      } else {
        $poli[$d->poli_id][$d->day] = $d->total;
      }
    }

    $avg = [];
    foreach ($poli as $index => $days) {
      foreach ($days as $total) {
        if (isset($avg[$index])) {
          $avg[$index] += $total;
        } else {
          $avg[$index] = $total;
        }
      }
      $avg[$index] = round($avg[$index] / count($days));
    }

    $poli = Poli::select(["poli_id", "poli_nama", "poli_color"])
      ->where("poli_id", "!=", 4)
      ->orderBy("poli_nama", "ASC")
      ->get();

    $list = [];
    foreach ($poli as $d) {
      $t = isset($avg[$d->poli_id]) ? $avg[$d->poli_id] : 0;
      $list[] = [
        "poli_id" => $d->poli_id,
        "poli_nama" => $d->poli_nama,
        "poli_color" => $d->poli_color,
        "total" => $t,
      ];
    }

    return [
      "all" => $total,
      "poli" => $list,
    ];
  }

  public function antrian(Request $request)
  {
    $length = $request->input("length");
    $sortBy = "tiket_date";
    $orderBy = "desc";
    $searchValue = $request->input("search");
    $relationships = ["poli", "channel"];

    $query = Tiket::eloquentQuery($sortBy, $orderBy, "", $relationships);
    $filters = json_decode($request->filters);
    if (isset($filters)) {
      if ($filters->tiket_date != "") {
        $query->where(
          DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'),
          $filters->tiket_date
        );
      }

      if ($filters->poli_id != "") {
        $query->where("tiket_poli_id", $filters->poli_id);
      }

      if ($filters->channel_id != "") {
        $query->where("tiket_channel_id", $filters->channel_id);
      }

      if ($filters->dirujuk != "") {
        $query->where("tiket_pasien_dirujuk", $filters->dirujuk);
      }
    }

    $query->orderBy("tiket_created", "asc");

    $data = $query->paginate($length);

    return new DataTableCollectionResource($data);
  }

  public function pasien(Request $request)
  {
    $length = $request->input("length");
    $sortBy = "pasien_nama";
    $orderBy = "asc";
    $searchValue = $request->input("search");
    $relationships = [];

    $query = Pasien::eloquentQuery(
      $sortBy,
      $orderBy,
      $searchValue,
      $relationships
    );

    $data = $query->paginate($length);

    return new DataTableCollectionResource($data);
  }
}
