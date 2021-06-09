<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Cartorio extends Model
{
    protected $primaryKey = 'c_id';
    protected $table = 'admin.cartorios';
    public $timestamps = false;
}
