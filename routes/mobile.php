<?php

use Illuminate\Http\Request;

$router->group(["prefix" => "v1"], function ($router) {
  $router->post("auth", "Api\AuthController@loginWithGoogle");
  $router->get("/", "Api\MobileController@index");

  $router->group(["middleware" => ["mobile"]], function ($router) {
    // status antrian
    $router->get(
      "antrean/status/{kodepoli}/{tanggalantrian}",
      "Api\MobileController@getStatusAntrian"
    );

    // register antrian
    $router->post("antrean", "Api\MobileController@registerAntrian");

    // antrian terdekat
    $router->get(
      "antrean/terdekat/me",
      "Api\MobileController@getAntrianTerdekat"
    );

    // antrian all antrian milik user login
    $router->get("antrean", "Api\MobileController@getMyAntrian");

    // antrian by id
    $router->get("antrean/{id}", "Api\MobileController@getAntrianById");

    // get profile peserta yang sedang login
    $router->get("peserta/me", "Api\MobileController@getCurrentPeserta");

    // update profile peserta yang sedang login
    $router->put("peserta/me", "Api\MobileController@updateCurrentPeserta");

    // batal antrian
    $router->delete("antrean/{id}", "Api\MobileController@unregisterAntrian");
  });
});
