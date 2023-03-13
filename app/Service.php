<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
  protected $table = "ant_service";
  protected $primaryKey = "service_code";

  protected $fillable = ["service_nama", "service_url"];

  public $timestamps = false;
}
