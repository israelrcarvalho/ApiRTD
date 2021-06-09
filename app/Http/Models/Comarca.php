<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Comarca extends Model
{
    protected $primaryKey = 'id_comarca';
    protected $table = 'admin.comarcas';
    public $timestamps = false;
    protected $fillable = ['id_cartorio', 'id_cidade'];

}
