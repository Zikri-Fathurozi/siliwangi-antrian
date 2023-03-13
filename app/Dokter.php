<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
  protected $table = "ant_dokter";

  protected $fillable = [
    "dokter_nik",
    "dokter_nama",
    "dokter_gender",
    "dokter_birth_date",
    "dokter_poli_id",
    "created_at",
    "updated_at",
  ];
}
