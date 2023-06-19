<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Printer;
use Illuminate\Support\Facades\DB;

class PrinterController extends Controller
{
  //setting
  public function list()
  {
    $data = Printer::select()
      ->orderBy("printer_nama", "ASC")
      ->get();
    $list = [];
    foreach ($data as $d) {
      $list[$d->printer_id] = $d;
    }
    return json_encode($list);
  }

  public function save(Request $request)
  {
    $printer = $request->get("printer");

    if (isset($printer["printer_id"])) {
      $printer["printer_modified"] = date("Y-m-d H:i:s");
      $printer["printer_modifier"] = Auth::User()->email;
      $res = Printer::where("printer_id", $printer["printer_id"])->update(
        $printer
      );
    }

    $res = $res ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }

  public function key($id)
  {
    $res = Printer::select(["printer_url_service", "printer_alias"])
      ->where("printer_code", $id)
      ->first();
    return json_encode(["res" => $res]);
  }
}
