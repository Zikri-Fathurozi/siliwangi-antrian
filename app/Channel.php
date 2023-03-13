<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
  protected $table = "ant_channel";
  protected $primaryKey = "channel_id";

  public $timestamps = false;
}
