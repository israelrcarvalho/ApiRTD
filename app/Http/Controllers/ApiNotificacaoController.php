<?php

namespace App\Http\Controllers;

use App\Http\Models\Cancelamento;
use App\Http\Models\DocsCartorio;
use App\Http\Models\DocsCartorioCertificado;
use App\Http\Models\NotaDevolutiva;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Models\ProdutoNotificacao;

use SoapClient;

use JWTAuth;
use JWTAuthException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ApiNotificacaoController extends Controller
{

    private $pNotificacao;
    private $user;
    private $exigencia;
    private $cancelarNotificacao;
    private $docsCartorio;
    private $docsCartorioCertificado;

    public function __construct(
        ProdutoNotificacao $pNotificacao,
        NotaDevolutiva $exigencia,
        Cancelamento $cancelarNotificacao,
        DocsCartorio $docsCartorio,
        DocsCartorioCertificado $docsCartorioCertificado,
        Request $request)
    {

        $this->pNotificacao = $pNotificacao;
        $this->user = JWTAuth::toUser($request->token);
        $this->exigencia = $exigencia;
        $this->cancelarNotificacao = $cancelarNotificacao;
        $this->docsCartorio = $docsCartorio;
        $this->docsCartorioCertificado = $docsCartorioCertificado;
    }

    /**
     * 01. Cadastrar notificação
     * Método responsável por gerar uma nova notificação.
     * @param ProdutoNotificacao $prodNot
     * @param Request $request
     * URL : https://www.rtdbrasil.com.br/api/v1/webservice/gerarNotificacao
     * Verbo : POST
     * @return array com informações
     */
    public function gerarNotificacao(Request $request)
    {

        try {

            $data = $request->all();
            $auxHash = sha1(microtime());

            $validar = Validator($data, $this->pNotificacao->rulesForGerarNotificacao());

            if ($validar->fails()) {
                $msg = $validar->messages();
                return response()->json(['error' => $msg]);

            }

            $dirFiles = env('DIR_FILES') . "/clientes/" . $this->user->id_cliente . "/notificacao_extrajudicial/" . $this->pNotificacao->getNumLote($this->user->id_cliente) . "/" . $auxHash;

            $fileStatus = $this->uploadFile($request, 'arquivo', $dirFiles);

            unset($data['arquivo']);
            unset($data['token']);

            $data['id_cliente'] = $this->user->id_cliente;
            $data['num_paginas'] = $fileStatus['nPag'];

            $data['num_lote'] = $this->pNotificacao->getNumLote($this->user->id_cliente);
            $data['ordem_lote'] = $this->pNotificacao->getNoOrdem($this->user->id_cliente, $this->pNotificacao->getNumLote($this->user->id_cliente));

            $data['assinado'] = 'S';
            $data['hash'] = $auxHash;

            $retorno = $this->pNotificacao->create($data);

            return response()->json(
                [
                    'result' => 'notificacaoId: ' . $retorno->id_notificacao,
                    'Upload' => $fileStatus,], 200);

        } catch (\ErrorException $e) {

            return $e->getMesseger();

        }
    }

    /**
     * 04 Listar notificações
     * Método responsável por listar todas as notificações do usuário autenticado .
     * @link https://www.rtdbrasil.com.br/api/v1/webservice/obterNotificacoes?token={valorDoToken}
     * Verbo : GET
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function obterNotificacoes()
    {
        try {

            $aux = $this->pNotificacao->obterNotificacoes($this->user->id_cliente);

            foreach ($aux as $n) {
                $data[] = [
                    'notificacaoId' => $n->id_notificacao,
                    'pedido' => $n->id_pedido,
                    'notificado' => $n->notificado,
                    'cartorio' => (!is_null($n->cartorio)) ? $n->cartorio->c_oficio :'Nenhum cartório informado',
                    'valorEmolumento' => $n->vlr_emolumento,
                    'valorTotal' => $n->vlr_cobrado,
                    'status' => $n->status,
                ];

            }

            return response()->json(['result' => $data], 200);

        } catch (TokenInvalidException $e) {
            return ['Error: ' => $e->getMessage()];
        }
    }

    /**
     * 05 Enviar remessa de pagamento
     * Método para enviar arquivo contendo dados referente aos pagamentos
     * @link : não definido
     * @todo implemente este metodo
     */
    public function enviarRemessaPagamento()
    {
        // todo implemente este metodo
    }


    /**
     * 06 Obter exigência
     * Metodo responsável por obter as exigências de uma notificação
     * @method:obterExigencia
     * @param int $idNotificacao
     * @link https://www.rtdbrasil.com.br/api/v1/webservice/obterExigencia/{idDaNotificacao}?token={valorDoToken}
     * Verbo: GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function obterExigencia($idNotificacao)
    {
        try {

            $exigencias = $this->pNotificacao->obterNotificacaoPorId($idNotificacao, $this->user->id_cliente);

            foreach ($exigencias->obterExigencia as $exigencia) {

                $data = [
                    'exigenciaId' => $exigencia->id,
                    'data' => $exigencia->data,
                    'exigencia' => $exigencia->nota,
                ];

            }

            return response()->json(['result' => $data], 200);

        } catch (\ErrorException $e) {

            return response()->json(['error' => 'Não foi possível obter exigência(s)'], 500);
        }


    }


    /**
     * 07 Responder exigências
     * Método responsável por responder uma exigência adicionada a notificacação
     * @param Request $request , token, resposta , exigenciaId
     * @link https://www.rtdbrasil.com.br/api/v1/webservice/responderExigencia
     * Verbo: POST
     * @return \Illuminate\Http\JsonResponse
     */
    public function responderExigencia(Request $request)
    {

        try {

            if ($this->exigencia->responderExigencia($request->exigenciaId, $this->user->id_cliente, $request->resposta)) {

                return response()->json([
                    'result' => 'exigência respondida com sucesso.'
                ]);

            } else {
                return response()->json(['result' => ['erro' => 'impossível responder exigência']], 500);
            }

        } catch (\ErrorException $e) {

            return response()->json(['result' => ['erro' => 'impossível responder exigência']], 500);

        }
    }

    /**
     * 08 Cancelar Notificação
     * Método responsável por cancelar uma notificacação efetuada.
     * obs: enquanto esta não tiver sido registrada.
     * @param Request $req
     * Verbo: POST
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelarNotificacao(Request $req)
    {
        try {
            $ok = $this->pNotificacao->cancelarNotificacao($req->idNotificacao, $this->user->id_cliente);
            if ($ok) {
                $this->cancelarNotificacao->inserirCancelamento(4, $this->user->id_cliente, $req->idNotificacao, $req->motivo);
                return response()->json([
                    'result' => 'notificacaoId: ' . $req->idNotificacao,
                    'msg' => ' Notificação cancelada com sucesso.'], 200);

            } else {
                return response()->json(['erro' => 'impossível salvar'], 500);
            }

        } catch (\ErrorException $e) {
            return response()->json(['erro' => 'impossível salvar'], 500);
        }
    }


    /**
     * 09 Obter certificado de registro
     * Método responsável por obter o arquivo registrado da notificação
     * @param $idNotificacao
     * @link  : https://www.rtdbrasil.com.br/api/v1/webservice/obterArquivoResultado/{idNotificacao}
     * Verbo: GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function obterArquivoResultado($idNotificacao)
    {
        try {
            $arq = $this->pNotificacao->obterNotificacaoPorId($idNotificacao, $this->user->id_cliente);
            $pathCertificado = env('DIR_FILES') . "/" . $arq->documento->cartorio->c_cnpj . "/" . $arq->documento->lote . "/" . $arq->documento->num_arquivo . $arq->documento->tipo;
            $certificado = base64_encode(file_get_contents($pathCertificado));

            return response()->json(['result' => $certificado], 200);

        } catch (\ErrorException $e) {
            return response()->json(['erro' => 'impossível recuperar o arquivo'], 500);
        }
    }


    /**
     * 10. Obter certidão de diligência
     * Método responsável por obter a certidão resultado da diligência da notificação
     * @param $idNotificacao
     * @link: https://www.rtdbrasil.com.br/api/v1/webservice/obterArquivoCertidao/{idNotificacao}
     * Verbo HTTP: GET
     * @return \Illuminate\Http\JsonResponse
     */
    public function obterArquivoCertidao($idNotificacao)
    {
        try {

            $cert = $this->pNotificacao->obterNotificacaoPorId($idNotificacao, $this->user->id_cliente);
            $pathCertidao = env('DIR_FILES')
                . "/" . $cert->cartorio->c_cnpj
                . "/" . $cert->documento->lote
                . "/" . $cert->documento->certificado->arquivo
                . $cert->documento->certificado->tipo;

            $certidao = base64_encode(file_get_contents($pathCertidao));

            return response()->json(['result' => $certidao], 200);

        } catch (\ErrorException $e) {
            return response()->json(['result' => ['Erro' => 'impossível recuperar certidão']], 500);
        }
    }


    /**
     * 11. Obter recibo de pagamento
     * Método responsável por obter o arquivo registrado da notificação
     * Verbo HTTP: GET
     * @todo implemente este metodo
     */
    public function obterRecibo()
    {

    }


    // Metodos Auxiliares

    /**
     * Retorna uma notificação do usuário autenticado
     * @param $id
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function obterNotificacaoPorId($id, Request $request)
    {
        try {

            $data = [];
            $n = $this->pNotificacao->obterNotificacaoPorId($id, $this->user->id_cliente);

            array_push($data, [
                'notificacaoId' => $n->id_notificacao,
                'pedido' => $n->id_pedido,
                'notificado' => $n->notificado,
                'cartorio' => $n->cartorio->c_oficio,
                'valorEmolumento' => $n->vlr_emolumento,
                'valorTotal' => $n->vlr_cobrado,
                'status' => $n->status,
            ]);

            return response()->json(['result' => $data]);

        } catch (Exception $e) {
            return ['error:' => $e->getMessage()];
        }
    }


    /**
     * Metodo Responsável por fazer uma copia do p7s, removendo a assinatura.
     * @param string $arquivoP7s
     * @param string $caminhoDoNovoArquivo
     * @return mixed
     */
    public function restaurarArquivo($arquivoP7s, $caminhoDoNovoArquivo)
    {

        ini_set("soap.wsdl_cache_enabled", 0);  // Cache ativado:1 ; desativado:0
        ini_set("soap.wsdl_cache_ttl", 1);      // Cache tempo de expiracao

        $wsdl = env('URL_WEBSERVICE');

        $soap = new SoapClient($wsdl, ['cache_wsdl' => WSDL_CACHE_NONE]);

        $parametro = [
            'nomeArquivo' => $arquivoP7s,
            'nomeRetorno' => $caminhoDoNovoArquivo
        ];

        return $soap->restoreFile($parametro);

    }


    /**
     * Método responsável por fazer o upload do arquivo
     * @param Request $request
     * @param string $field campo contendo o nome do arquivo
     * @param string $path caminho do arquivo
     * @return array com informações do upload
     */
    public function uploadFile(Request $request, $field, $path = '')
    {

        $resultado = "Arquivo não enviado";

        if ($request->hasFile($field)) {

            $file = $request->file($field);

            if ($file->isValid()) {

                $basename = $file->getClientOriginalName();                                               // Pega o nome original + extensão
                $extension = $file->getClientOriginalExtension();                                         // Pega a extensão sem o ponto
                $filename = basename($basename, '.' . $extension);                                  // Pega somente o nome do arquivo
                $slugFile = str_replace('pdf', '.pdf', str_slug($filename, '-')); // slug do nome original

                // gera um nome unico para o arquivo
                $name = $this->_getUniqueFilename($path, $slugFile, $extension);

                $file->move($path, $name);
                $newPathFile = $path . '/' . $name;
                $contarPaginas = contarPaginas($newPathFile);

                $os = substr(PHP_OS, 0, 3);

                $arquivoRestaurado = $path . str_replace('.p7s', '', $name);
                $auxPathFile = $path . $name;

                if (strtolower($os) == 'win') {

                    $arquivoRestaurado = str_replace(env('DIR_FILES'), '/mnt/php_nodejs/irtd_dev', $path) . '/' . str_replace('.p7s', '', $name);
                    $auxPathFile = str_replace(env('DIR_FILES'), '/mnt/php_nodejs/irtd_dev', $path) . '/' . $name;
                }

                $this->restaurarArquivo($auxPathFile, $arquivoRestaurado);
            }

            $resultado = [

                'msg' => 'Arquivo enviado com sucesso',
                'nPag' => $contarPaginas,
            ];
        }

        return $resultado;
    }

    /**
     * obtem um nome de arquivo único
     * @param string $path
     * @param string $filename
     * @param string $extension
     * @return string com o nome do arquivo
     */
    protected function _getUniqueFilename($path, $filename, $extension)
    {
        $i = 2;
        $output = $filename . '.' . $extension;

        while (File::exists($path . '/' . $output)) {
            $output = $filename . '_' . $i . '.' . $extension;
            $i++;
        }
        return $output;
    }

}
