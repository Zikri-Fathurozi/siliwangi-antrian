<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Service;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
  //setting
  public function list()
  {
    $data = Service::select()
      ->orderBy("service_nama", "ASC")
      ->get();
    $list = [];
    foreach ($data as $d) {
      $list[$d->service_id] = $d;
    }
    return json_encode($list);
  }

  public function save(Request $request)
  {
    $service = $request->get("service");

    if (isset($service["service_id"])) {
      $service["service_modified"] = date("Y-m-d H:i:s");
      $service["service_modifier"] = Auth::User()->email;
      $res = Service::where("service_id", $service["service_id"])->update(
        $service
      );
    }

    $res = $res ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }

  public function key($id)
  {
    $res = Service::select(["service_url", "service_enabled"])
      ->where("service_code", $id)
      ->first();
    return json_encode(["res" => $res]);
  }
}
