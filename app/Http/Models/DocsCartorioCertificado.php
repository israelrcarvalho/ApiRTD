<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocsCartorioCertificado extends Model
{
    protected $primaryKey = 'id_doc_cartorio_cert';
    protected $table = 'irtd.docs_cartorio_certificado';
    public $timestamps = false;


    /**
     * @param $idNotificacao
     * @return mixed
     */
    public function obterArquivoCertidao($idNotificacao){

//        return $this->where('id_origem',$idNotificacao)->where('id_tipo_registro',4)->get()->first() ;
    }



}
