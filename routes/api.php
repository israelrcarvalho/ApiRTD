<?php

/**
 * Descrição: Método responsável por autenticar e gerar o token para o usuário
 * Método:login
 */
$this->post('login', 'UserController@login');

$this->group(['prefix' => 'v1'], function () {

    $this->group(['middleware' => 'jwt.auth'], function () {

        /**
         * 1. Cadastrar notificação
         *    Descrição: Método responsável por gerar uma nova notificação.
         *    Método: gerarNotificacao
         *    URL : https://www.rtdbrasil.com.br/api/v1/webservice/gerarNotificacao
         *    Verbo : POST
         */
        $this->post('webservice/gerarNotificacao', 'ApiNotificacaoController@gerarNotificacao');


        /**
         * 4. Listar notificações
         *    Descrição : Método responsável por listar todas as notificações do usuário autenticado .
         *    @method : obterNotificacoes
         *    Verbo : GET
         *    @link: https://www.rtdbrasil.com.br/api/v1/webservice/obterNotificacoes?token={valorDoToken}
         */

        $this->get('webservice/obterNotificacoes', 'ApiNotificacaoController@obterNotificacoes');

        /**
         *
         */
        $this->get('webservice/obternotificacao/{id}', 'ApiNotificacaoController@obterNotificacaoPorId');

        /**
         * 6. Obter exigência
         *    Descrição : Metodo responsável por obter a exigência adicionada a uma notificação em especifico.
         *    Método : obterExigencia
         *    URL : https://www.rtdbrasil.com.br/api/v1/webservice/obterExigencia
         *    Verbo: GET
         */

        $this->get('webservice/obterExigencia/{idNotificacao}', 'ApiNotificacaoController@obterExigencia');

        /**
         * 7. Responder exigências
         *    Descrição : Método responsável por responder uma exigência adicionada a notificacação.
         *    Método : responderExigencia
         *    URL : https://www.rtdbrasil.com.br/api/v1/webservice/responderExigencia
         *    Verbo: POST
         */
        $this->post('webservice/responderExigencia', 'ApiNotificacaoController@responderExigencia');

        /**
         * 8. Cancelar Notificação
         *    Descrição: Método responsável por cancelar uma notificacação efetuada, enquanto está não tiver sido registrada.
         *    Método : cancelarNotificacao
         *    URL : https://www.rtdbrasil.com.br/api/v1/webservice/cancelarNotificacao
         *    Verbo : POST
         */

        $this->post('webservice/cancelarNotificacao', 'ApiNotificacaoController@cancelarNotificacao');

        /**
         * 9. Obter certificado de registro
         *    Descrição : Método responsável por obter o arquivo registrado da notificação.
         *    Método : obterArquivoResultado
         *    @link: https://www.rtdbrasil.com.br/api/v1/webservice/obterArquivoCertidao/{idNotificacao}
         *    Verbo: GET
         */

        $this->get('webservice/obterArquivoResultado/{idNotificacao}', 'ApiNotificacaoController@obterArquivoResultado');

        /**
         * 10. Obter certidão de diligência
         *     Descrição: Método responsável por obter a certidão resultado da diligência da notificação
         *     Método: obterArquivoCertidao
         *     URL: https://www.rtdbrasil.com.br/api/v1/webservice/obterArquivoCertidao
         *     Verbo: GET
         */
        $this->get('webservice/obterArquivoCertidao/{idNotificacao}', 'ApiNotificacaoController@obterArquivoCertidao');

    });
});