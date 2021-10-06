<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class td_TipoTareaModel extends Model
{
    //
    protected $table = 'td_tipo_tarea';
    protected $primaryKey  = 'idtipo_tarea';
    public $timestamps = false;
}
