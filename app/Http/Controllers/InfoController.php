<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Info;

class InfoController extends Controller
{
  public function list()
  {
    $data = Info::select()
      ->orderBy("info_text", "ASC")
      ->get();
    $list = [];
    foreach ($data as $d) {
      $list[$d->info_id] = $d;
    }
    return json_encode($list);
  }

  public function list_display()
  {
    $data = Info::select()
      ->where("info_status", "1")
      ->orderBy("info_text", "ASC")
      ->get();
    $list = [];
    foreach ($data as $d) {
      $list[] = $d->info_text;
    }
    return json_encode($list);
  }

  public function del(Request $request)
  {
    $id = $request->get("info_id");
    $res = Info::where("info_id", "=", $id)->delete() ? "SUCCESS" : "FAILED";

    return json_encode(["res" => $res]);
  }

  public function save(Request $request)
  {
    $info = $request->get("info");

    $info["info_status"] = isset($info["info_status"])
      ? $info["info_status"]
      : 0;

    if (isset($info["info_id"])) {
      $info["info_modified"] = date("Y-m-d H:i:s");
      $info["info_modifier"] = Auth::User()->email;
      $res = Info::where("info_id", $info["info_id"])->update($info);
    } else {
      $info["info_created"] = date("Y-m-d H:i:s");
      $info["info_author"] = Auth::User()->email;
      $res = Info::create($info);
    }

    $res = $res ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }
}
