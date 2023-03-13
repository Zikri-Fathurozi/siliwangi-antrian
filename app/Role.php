<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  protected $table = "ant_role";
  protected $primaryKey = "role_id";
  public $incrementing = false;
  public $timestamps = false;
}
