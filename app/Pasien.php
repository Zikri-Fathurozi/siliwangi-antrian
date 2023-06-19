<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class Pasien extends Model
{
  use LaravelVueDatatableTrait;

  protected $table = "ant_pasien";
  protected $primaryKey = "pasien_nik";

  protected $fillable = [
    "pasien_nik",
    "pasien_nomor_kk",
    "pasien_nomor_kartu",
    "pasien_nama",
    "pasien_gender",
    "pasien_birth_date",
    "pasien_address",
    "pasien_prop_kode",
    "pasien_prop_nama",
    "pasien_dati2_kode",
    "pasien_dati2_nama",
    "pasien_kec_kode",
    "pasien_kec_nama",
    "pasien_kel_kode",
    "pasien_kel_nama",
    "pasien_uid",
    "pasien_channel",
    "pasien_rt",
    "pasien_rw",
    "created_at",
    "updated_at",
  ];

  protected $dataTableColumns = [
    "pasien_nomor_kartu" => [
      "searchable" => true,
      "orderable" => false,
    ],
    "pasien_nik" => [
      "searchable" => true,
      "orderable" => false,
    ],
    "pasien_nama" => [
      "searchable" => true,
      "orderable" => false,
    ],
    "pasien_gender" => [
      "searchable" => false,
      "orderable" => false,
    ],
    "pasien_address" => [
      "searchable" => false,
      "orderable" => false,
    ],
  ];

  protected $dataTableRelationships = [];
}
