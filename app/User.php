<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    
    protected $primaryKey = 'id_cliente';
    protected $table = 'admin.pessoas';
    public $timestamps = false;
    
    protected $fillable = ['cpf_cnpj', 'password','razao_social','senha','tipo_pessoa'];
    protected $hidden = ['password', 'remember_token'];

    public function getAuthIdentifierName() {
        return 'cpf_cnpj';
    }
    
    
    
//    public function getAuthPassword() {
//        return 'password';
//    }
//    

//    protected $fillable = [
//        'name', 'email', 'password',
//    ];
//
//    protected $hidden = [
//        'password', 'remember_token',
//    ];
    
}
