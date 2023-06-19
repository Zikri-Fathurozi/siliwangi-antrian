<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
  protected $table = "ant_poli";
  protected $primaryKey = "poli_id";

  protected $fillable = [
    "poli_nama",
    "poli_color",
    "poli_icon",
    "poli_deskripsi",
    "poli_status",
    "poli_kuota",
    "poli_dayopen",
    "poli_closehour",
  ];

  public $timestamps = false;
}
