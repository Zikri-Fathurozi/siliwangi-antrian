<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*
* API KHUSUS UNTUK TIKET DISPENSER DAN DISPLAY ANTRIAN
*
*
*/

//@ticket-dispenser
$router->post("poli/list-menu", "PoliController@list_menu");
$router->post("pendaftaran/register", "PendaftaranController@register");
$router->post("printer/key/{printer_code}", "PrinterController@key");
$router->post("service/key/{service_code}", "ServiceController@key");
//@end-ticket-dispenser

//@display
$router->post(
  "pendaftaran/nomor-current",
  "PendaftaranController@nomor_current"
);
$router->post("pendaftaran/summary", "PendaftaranController@summary");
$router->post("poli/list-display", "PoliController@list_display");
$router->post("poli/summary", "PoliController@summary");
$router->post("banner/list-image/{active}", "BannerController@list_image");
$router->post(
  "banner/list-video/{active}",
  "BannerController@list_video_array"
);
$router->post("banner/config", "BannerController@config");
$router->post("info/list-display", "InfoController@list_display");
//@end-display
