<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Http\Controllers\WebSocketController;

class WebSocketServer extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = "websocket:start";

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = "Command description";

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $port = 8080;
    $server = IoServer::factory(
      new HttpServer(new WsServer(new WebSocketController())),
      8080
    );
    $this->info(
      date("Y-m-d H:i:s") .
        " [websocket-antrian] info: websocket listening on :::" .
        $port
    );
    $this->info(
      date("Y-m-d H:i:s") .
        " [websocket-antrian] warning: websocket untuk sistem antrian, jangan dimatikan saat proses antrian sedang berjalan"
    );

    $server->run();
  }
}
