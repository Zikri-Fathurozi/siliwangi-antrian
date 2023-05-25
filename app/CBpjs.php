<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CBpjs extends Model
{
    protected $table = "c_bpjs";
    protected $primaryKey = "id";

    protected $fillable = [
        'kode_provider',
        'username',
        'password',
        'consid',
        'secret',
        'user_key',
        'url'
    ];
}
