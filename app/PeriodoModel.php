<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PeriodoModel extends Model
{
    
    protected $table = 'td_periodo';
    protected $primaryKey  = 'idperiodo';
    public $timestamps = false;
}
