<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Us001_tipoUsuarioModel extends Model
{
    protected $table = 'us001_tipoUsuario';
    protected $primaryKey  = 'idus001_tipoUsuario';
    public $timestamps = false;

    public function TipoUsuario()
    {
        return $this->belongsTo('App\TipoUsuarioModel','idtipoUsuario','idtipoUsuario');
    }

    public function Usuarios(){
        return $this->belongsTo('App\User', 'idus001', 'idus001')->with('us001_tpofp');
    }

}
