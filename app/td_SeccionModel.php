<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_SeccionModel extends Model
{
    protected $table = 'td_seccion';
    protected $primaryKey  = 'id_seccion';
    public $timestamps = false;

    public function sector(){
        return $this->belongsTo('App\td_SectorModel', 'id_sector', 'id_sector')->with('bodega');
    }

    // public function bodega(){
    //     return $this->hasMany('App\td_BodegaModel', 'id_bodega', 'id_bodega');
    // }
}

