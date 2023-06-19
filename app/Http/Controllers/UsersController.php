<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
  use RegistersUsers;

  public function list()
  {
    $data = User::select([
      "users.id",
      "users.name",
      "users.email",
      "users.role",
      "users.poli",
      "users.loket",
      "users.prioritas",
      "users.tensi",
      "users.nomor_kamar",
      DB::raw('IFNULL(ant_poli.poli_nama, "-") AS poli_nama'),
    ])
      ->with(["user_role"])
      ->leftJoin("ant_poli", function ($join) {
        $join
          ->on("ant_poli.poli_id", "=", "users.poli")
          ->orOn("ant_poli.poli_id", "=", "users.tensi");
      })
      ->where("id", "!=", Auth::User()->id)
      ->whereNotIn("role", ["channel", "mobile"])
      ->orderBy("role")
      ->orderBy("name");

    if (Auth::User()->role == "admin") {
      $data = $data->where("role", "!=", "superadmin");
    }

    $data = $data->get();
    $array = [];
    foreach ($data as $row) {
      $row->prioritas = $row->prioritas == "1" ? true : false;
      $array[$row->id] = $row;
    }

    return json_encode($array);
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
      $res = User::where("id", $user["id"])->update($user);
    } else {
      $user["password"] = Hash::make("1234");
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

  public function me()
  {
    $user = Auth::User();
    return json_encode($user);
  }

  public function change_profile(Request $request)
  {
    $user = $request->get("user");

    if (isset($user["id"])) {
      if (isset($user["password"])) {
        $user["password"] = Hash::make($user["password"]);
      }
      $res = User::where("id", $user["id"])->update($user);
    }

    $res = $res ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }

  public function check_password(Request $request)
  {
    $user = $request->get("user");
    $result = false;

    if (isset($user["id"])) {
      if (
        $res = User::select()
          ->where("id", $user["id"])
          ->first()
      ) {
        if (Hash::check($user["password_old"], $res["password"])) {
          $result = true;
        }
      }
    }
    $res = $result ? "SUCCESS" : "FAILED";
    return json_encode(["res" => $res]);
  }
}
