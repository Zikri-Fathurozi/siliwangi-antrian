<?php

use Illuminate\Http\Request;

$router->group(["prefix" => "v1"], function ($router) {
  $router->get("auth", "Api\AuthController@login");
  $router->get("/", "Api\MigrateController@index");

  $router->group(["middleware" => ["migrate", "role:channel"]], function (
    $router
  ) {
    $router->get("antrian/{tiket_date}", "Api\MigrateController@getAntrian");
    $router->post("pasien", "Api\MigrateController@getPasien");
  });
});
