<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_UsuarioRolModel extends Model
{
    //
    protected $table = 'td_usuario_rol';
    protected $primaryKey  = 'idusuario_rol';
    public $timestamps = false;
}
