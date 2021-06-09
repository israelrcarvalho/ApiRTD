<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Central RTDPJBrasil</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Bootstrap core CSS -->
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    </head>
    <body>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">CentralRTDPJBrasil</a>
            </div>
        </div>

    </nav>

    <div class="container" style="margin-top: 120px;">

        <p>endPoints de acesso</p>

        <table class="table table-responsive table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th>URL</th>
                <th>VERB HTTP</th>
                <th>DESCRIÇÃO</th>
                <th>ESTRUTURA</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">00</th>
                <td>/api/login</td>
                <td>POST</td>
                <td>Responsável por fazer o login e obter o token</td>
                <td>
                <pre>
{ "token": "eyJ0eXAiOig..." }
                    </pre>
                </td>
            </tr>
            <tr>
                <th scope="row">01</th>
                <td>api/v1/webservice/gerarNotificacao</td>
                <td>POST</td>
                <td>Responsável por gerar uma nova notificação</td>
                <td>
<pre>
{
    "result": "notificacaoId: 13473",
    "Upload": {
        "msg": "Arquivo enviado com sucesso",
        "nPag": "19"
    }
}
</pre>
                </td>
            </tr>
            <tr>
                <th scope="row">04</th>
                <td><a href="api/v1/webservice/obterNotificacoes?token={valorDoToken}">api/v1/webservice/obterNotificacoes?token={valorDoToken}</a></td>
                <td>GET</td>
                <td>Lista todas as notificações do usuário autenticado</td>
                <td>
<pre>
{
    "result": [
        {
            "notificacaoId": 13375,
            "pedido": null,
            "notificado": "Fulano",
            "cartorio": "Cartorio de Teste 01",
            "valorEmolumento": "0.00",
            "valorTotal": "0.00",
            "status": "Aguardando informar custas"
        }
    ]
}
</pre>
                </td>
            </tr>
            <tr>
                <th scope="row">06</th>
                <td><a href="api/v1/webservice/obterExigencia/{idDaNotificacao}?token={valorDoToken}">api/v1/webservice/obterExigencia/{idDaNotificacao}?token={valorDoToken}</a></td>
                <td>GET</td>
                <td>Responsável por obter as exigências de uma notificação</td>
                <td>
                <pre>
{
    "result": {
        "exigenciaId": 3602,
        "data": "09/08/2016",
        "exigencia": "Favor verificar <br />        a documentação"
    }
}
                </pre>
                </td>
            </tr>
            <tr>
                <th scope="row">07</th>
                <td><a href="api/v1/webservice/responderExigencia">api/v1/webservice/responderExigencia</a></td>
                <td>POST</td>
                <td>Responsável por responder uma exigência adicionada a notificacação</td>
                <td>
<pre>
{
    "result": "exigência respondida com sucesso."
}
</pre>
                </td>
            </tr>
            <tr>
                <th scope="row">08</th>
                <td><a href="api/v1/webservice/cancelarNotificacao">api/v1/webservice/cancelarNotificacao</a></td>
                <td>POST</td>
                <td>Responsável por cancelar uma notificacação efetuada. Se a mesma não esteja registrada.</td>
                <td>
<pre>
{
    "result": "notificacaoId: 13321",
    "msg": " Notificação cancelada com sucesso."
}
</pre>
                </td>
            </tr>
            <tr>
                <th scope="row">09</th>
                <td><a href="api/v1/webservice/obterArquivoResultado/{idNotificacao}">api/v1/webservice/obterArquivoResultado/{idNotificacao}</a></td>
                <td>GET</td>
                <td>Responsável por obter o arquivo registrado da notificação</td>
                <td>
<pre>
{
    "result": "Arquivo do registro codificado<br/>
     em base 64."
}
</pre>
                </td>
            </tr>
            <tr>
                <th scope="row">10</th>
                <td><a href="api/v1/webservice/obterArquivoCertidao/{idNotificacao}">api/v1/webservice/obterArquivoCertidao/{idNotificacao}</a></td>
                <td>GET</td>
                <td> responsável por obter a certidão resultado da diligência da notificação.</td>
                <td>
<pre>
{
    "result": "Arquivo da certidão codificado<br/>
     em base 64
}
</pre>
                </td>
            </tr>
            </tbody>
        </table>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    </body>
</html>
