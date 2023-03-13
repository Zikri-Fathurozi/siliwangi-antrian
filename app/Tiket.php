<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tiket extends Model
{
  use SoftDeletes;
  use LaravelVueDatatableTrait;

  protected $table = "ant_tiket";
  protected $primaryKey = "tiket_id";
  protected $keyType = "string";
  public $timestamps = false;

  protected $fillable = [
    "tiket_id",
    "tiket_nomor",
    "tiket_date",
    "tiket_created",
    "tiket_poli_id",
    "tiket_pasien_nik",
    "tiket_pasien_keluhan",
    "tiket_deleted",
    "tiket_channel_id",
  ];

  protected $dataTableColumns = [
    "tiket_nomor" => [
      "searchable" => false,
      "orderable" => false,
    ],
    "tiket_date" => [
      "searchable" => false,
      "orderable" => false,
    ],
    "tiket_pasien_nik" => [
      "searchable" => false,
      "orderable" => false,
    ],
    "tiket_pasien_dirujuk" => [
      "searchable" => false,
      "orderable" => false,
    ],
  ];

  protected $dataTableRelationships = [
    "belongsTo" => [
      "poli" => [
        "model" => \App\Poli::class,
        "foreign_key" => "tiket_poli_id",
        "local_key" => "poli_id",
        "columns" => [],
      ],
      "channel" => [
        "model" => \App\Channel::class,
        "foreign_key" => "tiket_channel_id",
        "local_key" => "channel_id",
        "columns" => [],
      ],
    ],
  ];

  public function poli()
  {
    return $this->belongsTo(\App\Poli::class, "tiket_poli_id", "poli_id");
  }

  public function channel()
  {
    return $this->belongsTo(
      \App\Channel::class,
      "tiket_channel_id",
      "channel_id"
    );
  }

  public function doctors()
  {
    return $this->hasMany(
      \App\Dokter::class,
      "dokter_poli_id",
      "tiket_poli_id"
    );
  }
}
