<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoModel extends Model
{
    protected $table = 'documento';
    protected $primaryKey  = 'iddocumento';
    public $timestamps = false;
}
