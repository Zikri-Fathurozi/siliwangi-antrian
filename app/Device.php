<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
  protected $table = "ant_device";
  protected $primaryKey = "device_code";

  protected $fillable = ["device_nama", "device_ip"];

  public $timestamps = false;
}
