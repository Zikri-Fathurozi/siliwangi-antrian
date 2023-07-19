<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AntrianStart extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = "antrian:start";

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = "Menjalankan sistem antrian";

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
    $this->info("
       d8888          888            d8b                        888888b.                 888  d8b          
      d88888          888            Y8P                        888   88b                888  Y8P          
     d88P888          888                                       888  .88P                888               
    d88P 888 88888b.  888888 888d888 888  8888b.  88888b.       8888888K.   .d88b.   .d88888 8888  8888b.  
   d88P  888 888  88b 888    888P    888      88b 888  88b      888   Y88b d8P  Y8b d88  888  888      88b 
  d88P   888 888  888 888    888     888 .d888888 888  888      888    888 88888888 888  888  888 .d888888 
 d8888888888 888  888 Y88b.  888     888 888  888 888  888      888   d88P Y8b.     Y88b 888  888 888  888 
d88P     888 888  888   Y888 888     888  Y888888 888  888      8888888P     Y8888    Y88888  888  Y888888 
                                                                                              888          
                                                                                             d88P          
                                                                                           888P            ");
    // $this->call("migrasi:start");
    $this->call("websocket:start");
  }
}
