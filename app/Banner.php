<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
  protected $table = "ant_banner";
  protected $primaryKey = "banner_id";

  protected $fillable = [
    "banner_path",
    "banner_type",
    "banner_title",
    "banner_desc",
    "banner_status",
    "banner_id",
    "banner_mime",
  ];

  public $timestamps = false;
}
