<?php

$router->get("/", "Api\TicketController@index");
$router->post("register", "Api\TicketController@createRegisterTicket");
