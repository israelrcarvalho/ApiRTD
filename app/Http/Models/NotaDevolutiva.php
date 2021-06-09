<?php

namespace App\Http\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class NotaDevolutiva extends Model
{

    protected $table = 'irtd.nota_devolutiva';
    public $timestamps = false;


    /**
     * @param $idExigencia
     * @param $idCliente
     * @param $resposta
     * @return bool
     */
    public function responderExigencia($idExigencia, $idCliente, $resposta){

        try{

               $obj = $this->find($idExigencia) ;
            if($obj){
               $obj->select("nota_devolutiva.*")
                    ->where('id',$idExigencia)
                    ->join('irtd.produto_notificacao','id_notificacao','=','id_documento')
                    ->where('id_cliente',$idCliente)->get()->first();

                $novaResposta = $this->find($obj->id) ;
                $novaResposta->resposta_cliente = $resposta ;
                $novaResposta->save();

                return true ;
            } else {

                return false ;
            }

        } catch (\ErrorException $e) {
            return false ;
        }

    }

    /**
     * Formata a data na apresentação
     * @param $data
     * @return string
     */
    public function getDataAttribute($data)
    {
        return Carbon::parse($data)->format('d/m/Y');
    }

}
