<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Info extends Model {
	
	protected $table = 'ant_info';
    protected $primaryKey = 'info_id';
    
    protected $fillable = [
        'info_text',
        'info_status'
    ];

    public $timestamps = false;
}
