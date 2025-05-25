<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Auth;

// prettier-ignore-start
$router
  ->view("/ticket-dispenser", "antrian.ticket-dispenser")
  ->name("ticket.dispenser")
  ->middleware("device.code:tiket");

  $router
  ->view("/ticket-dispenser-new", "antrian.ticket-dispenser-new")
  ->name("ticket.dispensernew")
  ->middleware("device.code:tiket");

$router
  ->view("/display-pendaftaran", "antrian.display-pendaftaran")
  ->name("display.pendaftaran")
  ->middleware("device.code:display-pendaftaran");

$router
  ->get("/display-poli/{gedung}", "HomeController@displayByGedung")
  ->name("display.poli")
  ->middleware("device.group:display-gedung-poli");

  $router
  ->get('/display-farmasi', "FarmasiController1@displayFarmasi")
  ->name('display.farmasi')
  ->middleware("device.code:display-farmasi");

$router
  ->get("/display-kamar/{gedung}/{poli}", "HomeController@displayByKamar")
  ->name("display.kamar")
  ->middleware("device.group:display-gedung-kamar");

//loket-app
$router->group(["middleware" => ["auth"]], function ($router) {
  $router->get("/", "HomeController@home");
  $router->get("home", "HomeController@home")->name("home");

  $router
    ->view("/loket/pendaftaran", "loket.pendaftaran")
    ->middleware("role:pendaftaran")
    ->name("loket.pendaftaran");
  $router
    ->view("/loket/poli", "loket.poli")
    ->middleware("role:poli")
    ->name("loket.poli");
    $router
    ->view("/loket/farmasi", "loket.farmasi")
    ->middleware("role:farmasi")
    ->name("loket.farmasi");
    $router
    ->view("/loket/kamar", "loket.kamar")
    ->middleware("role:kamar")
    ->name("loket.kamar");
  $router
    ->view("/loket/tensi", "loket.tensi")
    ->middleware("role:tensi")
    ->name("loket.tensi");

  $router->view("/setting/profile", "setting.profile")->name("setting.profile");

  $router->group(["middleware" => "role:admin"], function ($router) {
    $router->group(["prefix" => "setting"], function ($router) {
      $router->view("/", "setting.home")->name("setting");
      $router->view("printer", "setting.printer")->name("setting.printer");
      $router->view("device", "setting.device")->name("setting.device");
      $router
        ->view("service", "setting.service")
        ->middleware("role:superadmin")
        ->name("setting.service");
    });

    $router->group(["prefix" => "master"], function ($router) {
      $router->view("users", "setting.users")->name("master.users");
      $router->view("poli", "setting.poli")->name("master.poli");
      $router
        ->view("channel", "setting.channel")
        ->middleware("role:superadmin")
        ->name("master.channel");
    });

    $router->group(["prefix" => "display"], function ($router) {
      $router->view("info", "setting.info")->name("display.info");
      $router->view("banner", "setting.banner")->name("display.banner");
    });

    $router->group(["prefix" => "refresh"], function ($router) {
      $router->view("browser", "setting.update")->name("refresh.browser");
    });

    $router->group(["prefix" => "report"], function ($router) {
      $router->view("visit", "report.visit")->name("report.visit");
      $router->view("rujukan", "report.rujukan")->name("report.rujukan");
      $router
        ->view("grafik-visit", "report.grafik-visit")
        ->name("report.grafik-visit");

      $router->view("antrian", "report.antrian")->name("report.antrian");
      $router->view("pasien", "report.pasien")->name("report.pasien");
    });
  });

  // api setting
  $router->post("users/list", "UsersController@list");
  $router->post("users/del", "UsersController@del");
  $router->post("users/save", "UsersController@save");
  $router->post("users/me", "UsersController@me");
  $router->post("users/change_profile", "UsersController@change_profile");
  $router->post("users/check_password", "UsersController@check_password");

  //channel user
  $router->post("channel/list", "ChannelController@list");
  $router->post("channel/del", "ChannelController@del");
  $router->post("channel/save", "ChannelController@save");
  //channel type
  $router->post("channel/list/type", "ChannelController@list_type");

  $router->post("poli/list-all", "PoliController@list_all");
  $router->post("poli/del", "PoliController@del");
  $router->post("poli/save", "PoliController@save");

  $router->post("farmasi/list-all", "FarmasiController@list_all");
  $router->post("farmasi/del", "FarmasiController@del");
  $router->post("farmasi/save", "FarmasiController@save");

  $router->post("printer/list", "PrinterController@list");
  $router->post("printer/save", "PrinterController@save");

  $router->post("service/list", "ServiceController@list");
  $router->post("service/save", "ServiceController@save");

  $router->post("device/list", "DeviceController@list");
  $router->post("device/save", "DeviceController@save");

  $router->post("info/list", "InfoController@list");
  $router->post("info/del", "InfoController@del");
  $router->post("info/save", "InfoController@save");

  $router->post("banner/list-all", "BannerController@list_all");
  $router->post("banner/list-image/{active}", "BannerController@list_image");
  $router->post("banner/list-video/{active}", "BannerController@list_video");
  $router->post("banner/del", "BannerController@del");
  $router->post("banner/save", "BannerController@save");

  $router->post("banner/config", "BannerController@config");
  $router->post("banner/config/save", "BannerController@save_config");
  //end setting

  $router->post("pendaftaran/antrian", "PendaftaranController@antrian");
  $router->post("pendaftaran/next", "PendaftaranController@next");
  $router->post("pendaftaran/call", "PendaftaranController@call");
  $router->post("pendaftaran/end", "PendaftaranController@end");
  $router->post(
    "pendaftaran/detail/{nik}",
    "PendaftaranController@antrian_detail"
  );

  // loket tensi
  $router->post("tensi/antrian", "TensiController@antrian");
  $router->post("tensi/next", "TensiController@next");
  $router->post("tensi/call", "TensiController@call");
  $router->post("tensi/end", "TensiController@end");
  $router->post("tensi/attend", "TensiController@attend");
  $router->post("tensi/summary-rujuk", "TensiController@summary_rujukan");

  // pengaturan petugas
  $router->post("tensi/list", "TensiController@list");

  // loket poli
  $router->post("poli/antrian", "PoliController@antrian");
  $router->post("poli/next", "PoliController@next");
  $router->post("poli/call", "PoliController@call");
  $router->post("poli/end", "PoliController@end");
  $router->post("poli/attend", "PoliController@attend");
  $router->post("poli/rujuk", "PoliController@rujuk");
  $router->post("poli/summary-rujuk", "PoliController@summary_rujukan");

  $router->post("poli/list", "PoliController@list");
  $router->post("poli/get/{id}", "PoliController@get");
  $router->post("poli/register", "PoliController@register");

    // loket farmasi
  $router->post("farmasi/antrian", "FarmasiController@antrian");
  $router->post("farmasi/next", "FarmasiController@next");
  $router->post("farmasi/call", "FarmasiController@call");
  $router->post("farmasi/end", "FarmasiController@end");
  $router->post("farmasi/attend", "FarmasiController@attend");
  $router->post("farmasi/rujuk", "FarmasiController@rujuk");
  $router->post("farmasi/summary-rujuk", "FarmasiController@summary_rujukan");

  $router->post("farmasi/list", "FarmasiController@list");
  $router->post("farmasi/get/{id}", "FarmasiController@get");
  $router->post("farmasi/register", "FarmasiController@register");

  // Kamar Poli
  $router->post("kamar/antrian", "KamarController@antrian");
  $router->post("kamar/next", "KamarController@next");
  $router->post("kamar/call", "KamarController@call");
  $router->post("kamar/end", "KamarController@end");
  $router->post("kamar/attend", "KamarController@attend");
  $router->post("kamar/rujuk", "KamarController@rujuk");
  $router->post("kamar/summary-rujuk", "KamarController@summary_rujukan");

  $router->post("kamar/list", "KamarController@list");
  $router->post("kamar/get/{id}", "KamarController@get");
  $router->post("kamar/register", "KamarController@register");

  $router->post("report/visit", "ReportController@visit");
  $router->post("report/rujukan", "ReportController@rujukan");
  $router->get("report/antrian/datatable", "ReportController@antrian");
  $router->get("report/pasien/datatable", "ReportController@pasien");
});

Auth::routes();
// prettier-ignore-end
