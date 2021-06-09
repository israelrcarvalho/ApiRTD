<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class DocsCartorio extends Model
{
    protected $primaryKey = 'id_doc_cartorio';
    protected $table = 'irtd.docs_cartorio';
    public $timestamps = false;


    /**
     * @param $lote
     * @return string
     */
    public function getLoteAttribute($lote)
    {
        return str_pad($lote, 8, "0", STR_PAD_LEFT);
    }

    /**
     * Retorna o relacionamento com o model cartorio
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cartorio()
    {
        return $this->belongsTo(Cartorio::class, 'id_cartorio');
    }

    /**
     * Retorna o relacionamento com o model DocsCartorioCertificado
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function certificado()
    {
        return $this->HasOne(DocsCartorioCertificado::class, 'id_doc_cartorio');
    }

}
