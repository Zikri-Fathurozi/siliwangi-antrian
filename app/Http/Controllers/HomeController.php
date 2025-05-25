<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Poli;

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
      "except" => ["displayByGedung", 'displayByKamar'],
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
    } elseif ($role == "farmasi") {
      return redirect()->route("loket.farmasi");
    } elseif ($role == "tensi") {
      return redirect()->route("loket.tensi");
    } elseif ($role == "kamar") {
      return redirect()->route("loket.kamar");
    } else {
      return abort(404);
    }
  }

  public function displayByGedung($gedung)
  {
    return view("antrian.display-poli", ["gedung" => $gedung]);
  }

  public function displayByKamar($gedung, $poli)
  {
    return view("antrian.display-kamar", [
      'gedung' => $gedung,
      "poli" => $poli
    ]);
  }
}
