<?php

namespace App\Http\Controllers;

use App\User;
use App\Channel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class ChannelController extends Controller
{
  public function list()
  {
    $data = User::select(["id", "name", "email"])
      ->where("role", "channel")
      ->orderBy("name")
      ->get();

    return response()->json($data);
  }

  public function del(Request $request)
  {
    $id = $request->get("id");
    $res = User::where("id", $id)->delete() ? "SUCCESS" : "FAILED";

    return json_encode(["res" => $res]);
  }

  public function save(Request $request)
  {
    $user = $request->get("user");

    if (isset($user["id"])) {
      $user["password"] = Hash::make($user["password"]);
      $res = User::where("id", $user["id"])->update($user);
    } else {
      $user["password"] = Hash::make($user["password"]);
      $user["role"] = "channel";
      try {
        $res = User::create($user);
      } catch (\Illuminate\Database\QueryException $e) {
        $errorCode = $e->errorInfo[1];
        if ($errorCode == "1062") {
          $res = false;
        }
      }
    }

    $res = $res ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }

  public function list_type()
  {
    $data = Channel::all();
    return response()->json($data);
  }
}
