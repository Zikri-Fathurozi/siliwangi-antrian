<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
  protected $table = "ant_printer";
  protected $primaryKey = "printer_code";

  protected $fillable = [
    "printer_nama",
    "printer_url_service",
    "printer_alias",
  ];

  public $timestamps = false;
}
