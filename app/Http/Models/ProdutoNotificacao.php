<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class ProdutoNotificacao extends Model
{
    protected $table = 'irtd.produto_notificacao';
    protected $primaryKey = 'id_notificacao';
    protected $guarded = ['id_notificacao'];
    public $timestamps = false;
    protected $fillable = ['notificado', 'comp', 'identificador_comum', 'identificador_unico', 'cep', 'uf', 'cidade', 'endereco',
        'numero', 'bairro', 'id_cliente', 'num_paginas', 'num_lote', 'assinado', 'hash', 'ordem_lote'];


    /**
     * Retorna o relacionamento com o model cartorio
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cartorio()
    {
        return $this->belongsTo(Cartorio::class, 'id_cartorio');
    }


    /**
     * Retorna o relacionamento com o model DocsCartorio
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function documento()
    {
        return $this->HasOne(DocsCartorio::class, 'id_origem');
    }


    /**
     * Retorna o relacionamento com o model NotaDevolutiva
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function obterExigencia()
    {
        return $this->HasMany(NotaDevolutiva::class, 'id_documento');
    }


    /**
     * regras para a notificação
     * @return array
     */
    public function rulesForGerarNotificacao()
    {

        return [
            'notificado' => 'required',
            'identificador_comum' => 'required',
            'identificador_unico' => 'required',
            'cep' => 'required',
            'uf' => 'required',
            'cidade' => 'required',
            'endereco' => 'required',
            'numero' => 'required',
            'bairro' => 'required',
            'arquivo' => 'required',
        ];
    }


    /**
     * Retorna uma lista com todas as notificações do usuario logado
     * @param int $idCliente
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function obterNotificacoes($idCliente)
    {
        return $this->where('id_cliente', $idCliente)->get();
    }

    /**
     * @param $id
     * @return Model|static
     */
    public function obterNotificacaoPorId($id, $idCliente)
    {
        return $this->where('id_cliente', $idCliente)->where('id_notificacao', $id)->get()->first();
    }

    /**
     * @param $idNotificacao
     * @param $idCliente
     * @return bool
     */
    public function cancelarNotificacao($idNotificacao, $idCliente)
    {
        $obj = $this->find($idNotificacao);
        if ($obj->id_cliente == $idCliente && $obj->controle_cartorio != 'C') {
            $obj->solicitado = 'X';
            $obj->save();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retorna o numero do lote
     * @param $clienteId
     * @return string
     */
    public function getNumLote($clienteId)
    {
        $query = $this->query()
            ->where('id_cliente', $clienteId)
            ->orderBy('num_lote', 'DESC')
            ->take(1)->first();

        $retorno = (is_null($query)) ? $retorno = str_pad(1, 8, "0", STR_PAD_LEFT) : str_pad($query->num_lote, 8, "0", STR_PAD_LEFT);

        return $retorno;

    }

    /**
     * @param $clienteId
     * @param $lote
     * @return string
     */
    public function getNoOrdem($clienteId, $lote)
    {
        $query = $this->query()
            ->select(DB::raw('(COALESCE(ordem_lote,0)+1) as ordem_lote'))
            ->where('id_cliente', $clienteId)
            ->where('num_lote', $lote)
            ->orderBy('ordem_lote', 'DESC')
            ->take(1)->first();

        $retorno = (is_null($query)) ? $retorno = str_pad(1, 8, "0", STR_PAD_LEFT) : str_pad($query->ordem_lote, 8, "0", STR_PAD_LEFT);

        return $retorno;

    }
}
