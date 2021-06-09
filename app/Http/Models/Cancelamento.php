<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Cancelamento extends Model
{
    protected $primaryKey = 'cancelamento_id';
    protected $table = 'irtd.cancelamentos';
    public $timestamps = false;
    protected $fillable = ['id_servico', 'motivacao','id_executor'];


     /**
     * Insere um registro na tabela cancelamento
     * @param int $idServico
     * @param int $idExecutor
     * @param int $idNotificacao
     * @param string $motivo
     * @return bool
     */
    public function inserirCancelamento($idServico,$idExecutor,$idNotificacao,$motivo)
    {
        $this->id_servico = $idServico;
        $this->id_executor = $idExecutor;
        $this->id_solicitacao_servico = $idNotificacao;
        $this->motivacao = $motivo;
        $this->save();
        return true ;
    }


}
