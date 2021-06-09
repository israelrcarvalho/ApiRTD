<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $primaryKey = 'id_cliente';
    protected $table = 'admin.pessoas';
    public $timestamps = false;
    protected $fillable = ['cpf_cnpj', 'senha'];

    /*
    public function obterPessoa() {

        try {
            return response()->json($this->all());
        } catch (Exception $e) {
            return ['' => $e->getMessage()];
        }
    }
    */
}
