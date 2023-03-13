<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Device;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
  //setting
  public function list()
  {
    $data = Device::select()
      ->orderBy("device_nama", "ASC")
      ->get();
    $list = [];
    foreach ($data as $d) {
      $list[$d->device_id] = $d;
    }
    return json_encode($list);
  }

  public function save(Request $request)
  {
    $device = $request->get("device");

    if (isset($device["device_id"])) {
      $device["device_modified"] = date("Y-m-d H:i:s");
      $device["device_modifier"] = Auth::User()->email;
      $res = Device::where("device_id", $device["device_id"])->update($device);
    }

    $res = $res ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }

  public function getByCode($id)
  {
    $res = Device::select(["device_ip", "device_port"])->find($id);
    return json_encode(["res" => $res]);
  }

  public function getByGroup($group)
  {
    $res = Device::select(["device_ip", "device_port"])
      ->where("device_group", $group)
      ->get();
    return json_encode(["res" => $res]);
  }
}
