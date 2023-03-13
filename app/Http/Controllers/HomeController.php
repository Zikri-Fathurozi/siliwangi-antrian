<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware("auth", [
      "except" => ["displayByGedung"],
    ]);
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function home()
  {
    $role = Auth::User()->role;
    if ($role == "admin" || $role == "superadmin") {
      return redirect()->route("setting");
    } elseif ($role == "pendaftaran") {
      return redirect()->route("loket.pendaftaran");
    } elseif ($role == "poli") {
      return redirect()->route("loket.poli");
    } elseif ($role == "tensi") {
      return redirect()->route("loket.tensi");
    } else {
      return abort(404);
    }
  }

  public function displayByGedung($gedung)
  {
    return view("antrian.display-poli", ["gedung" => $gedung]);
  }
}
