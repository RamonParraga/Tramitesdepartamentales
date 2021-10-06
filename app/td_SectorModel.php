<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_SectorModel extends Model
{
    protected $table = 'td_sector';
    protected $primaryKey  = 'id_sector';
    public $timestamps = false;

    public function bodega(){
        return $this->belongsTo('App\td_BodegaModel', 'id_bodega', 'id_bodega');
    }

    public function seccion(){
        return $this->hasMany('App\td_SeccionModel', 'id_seccion', 'id_seccion');
    }

    // public function bodega(){
    //     return $this->hasMany('App\td_BodegaModel', 'id_bodega', 'id_bodega');
    // }
}

