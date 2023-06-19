<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Tiket;
use App\Pasien;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MigrateController extends Controller
{
  public function index()
  {
    return response()->json(["message" => "Koneksi Tersambung"], 200);
  }

  public function getAntrian($tiket_date)
  {
    $validator = Validator::make(
      ["tiket_date" => $tiket_date],
      [
        "tiket_date" => ["required", "date_format:Y-m-d"],
      ]
    );
    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    $data = Tiket::where(
      DB::raw('DATE_FORMAT(tiket_date,"%Y-%m-%d")'),
      $tiket_date
    )->get();

    return response()->json(
      [
        "data" => $data,
      ],
      200
    );
  }

  public function getPasien(Request $request)
  {
    $validator = Validator::make($request->all(), [
      "nik" => ["required"],
    ]);
    if ($validator->fails()) {
      return $this->responseWithError($validator->getMessageBag(), 500);
    }

    $data = Pasien::whereIn("pasien_nik", $request->nik)->get();

    return response()->json(
      [
        "data" => $data,
      ],
      200
    );
  }
}
