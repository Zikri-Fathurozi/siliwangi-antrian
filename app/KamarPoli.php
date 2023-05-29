<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KamarPoli extends Model
{
    protected $table = "ant_kamar_poli";
    protected $primaryKey = "tiket_id";

    protected $fillable = [
        "poli_id",
        "order"
    ];

    public function tikets()
    {
        return $this->hasMany(Tiket::class, 'pelayanan_id');
    }

    public function kamarPoli()
    {
        return $this->belongsTo(KamarPoli::class, 'kamar_poli_id')->withTrashed();
    }
}
