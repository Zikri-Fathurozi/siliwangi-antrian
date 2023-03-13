<?php

use Illuminate\Http\Request;

$router->group(["prefix" => "v1"], function ($router) {
  $router->get("auth", "Api\AuthController@login");
  $router->get("/", "Api\BPJSController@index");

  $router->group(["middleware" => ["channel", "role:channel"]], function (
    $router
  ) {
    // status antrian
    $router->get(
      "antrean/status/{kodepoli}/{tanggalantrian}",
      "Api\BPJSController@getStatusAntrian"
    );

    // daftar antrian
    $router->post("antrean", "Api\BPJSController@registerAntrian");

    // sisa antrian
    $router->get(
      "antrean/sisapeserta/{nomorkartu_jkn}/{kodepoli}/{tanggalperiksa}",
      "Api\BPJSController@getSisaAntrian"
    );

    // peserta baru
    $router->post("peserta", "Api\BPJSController@registerPeserta");

    // batal antrian
    $router->put("antrean/batal", "Api\BPJSController@unregisterAntrian");
  });
});
