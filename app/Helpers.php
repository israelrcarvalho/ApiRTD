<?php

/**
 * @param type $path
 * @return int
 */
function contarPaginas($path){

    if (!$fp = @fopen($path, "r")) {
        echo "eeeeeeeeeeeeeee : " . $path;
        exit;
        return 0;
    } else {
        $max = 0;
        while (!feof($fp)) {
            $line = fgets($fp, 255);
            if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                preg_match('/[0-9]+/', $matches[0], $matches2);
                if ($max < $matches2[0]) {
                    $max = $matches2[0];
                    break;
                }
            }
        }
        fclose($fp);
        if ($max < 1) {
            exec(PDF_INFO . " " . $path, $Saida);
            $TotalPaginas = 0;
            foreach ($Saida as $Info) {
                //ENCONTRA NAS LINHAS O NR DE PÁGINAS
                if (preg_match("/Pages:\s*(\d+)/i", $Info, $Matches) === 1) {
                    $max = intval($Matches[1]);
                    break;
                }
            }
        }
        return $max;
    }
}

function ConvertDOCtoPDF($Caminho)
{

    // Cria temporário
    $pathTmp = DIR_FILES . 'temp/' . $this->NomeArquivoAleatorio() . '.pdf';

    // Descobre caminho correto do método
    $PathConvert = '/usr/lib64/libreoffice/program/DocumentConverter.py';
    if (!@file_exists($PathConvert))
        $PathConvert = '/usr/lib/libreoffice/program/DocumentConverter.py';

    // Converte
    @exec("/usr/bin/python {$PathConvert} '{$Caminho}' '{$pathTmp}'");

    // Verifica consistencia do PDF
    if (@filesize($pathTmp) == 0)
        throw new Exception('Não foi possível converter para PDF.');

    // Exclui o arquivo anterior
    if (!@unlink($Caminho))
        throw new Exception('Não excluiu original (DOC).');

    // Copia o PDF para o Caminho do arquivo original
    if (!@rename($pathTmp, $Caminho))
        throw new Exception('Não moveu o arquivo PDF.');

    // Prepara o novo nome do arquivo
    $NovoNome = str_replace(strrchr($Caminho, "."), '.pdf', $Caminho);

    // Retorna novo caminho
    return $NovoNome;
}

function ConvertTIFtoPDF($Caminho)
{

    // Cria Nome Pasta Temporária
    $namePathTmp = md5(USUARIO_ID . microtime());

    // Verifica se existe a pasta de Temps
    $Path = DIR_FILES . "temp";
    if (!@file_exists($Path))
        @mkdir($Path, 0770);

    // Verifica se existe a pasta do Temporário
    $Path = DIR_FILES . "temp/{$namePathTmp}";
    if (!@file_exists($Path))
        @mkdir($Path, 0770);

    // Converte o TIF em JPEG
    exec("/var/www/rtdbrasil.com.br/plugins/nconvert/nconvert -no_auto_ext -c 1 -xall -multi -out jpeg -o {$Path}/1.jpg {$Caminho} 2>&1", $Output);

    // Lista páginas em JPEG para montar o arquivo em PDF
    $diretorio = dir($Path);
    $nomes = array();
    while ($arquivo = $diretorio->read()) {
        if (trim($arquivo) != '.' && trim($arquivo) != '..')
            $nomes[] = trim($arquivo);
    }
    $diretorio->close();
    asort($nomes);
    $names = '';
    foreach ($nomes as $n)
        $names .= "{$Path}/{$n} ";
    $names = trim($names);

    // Cria nome temporário para PDF
    $tempPDF = DIR_FILES . 'temp/' . md5(USUARIO_ID . microtime()) . '.pdf';

    // Converte os JPEGS em PDF
    exec("/var/www/rtdbrasil.com.br/plugins/nconvert/nconvert -no_auto_ext -c 1 -xall -multi -out pdf -o {$tempPDF} {$names} 2>&1", $Output);

    // Deleta pasta temporária
    $this->rrmdir($Path);

    // Prepara o novo nome do arquivo
    if (strstr(strtolower($Caminho), '.tif') && strstr(strtolower($Caminho), '.tiff'))
        $NovoNome = str_replace(strrchr($Caminho, "."), '.pdf', $Caminho);
    else
        $NovoNome = $Caminho;

    // Exclui o arquivo anterior
    if (!@unlink($Caminho))
        throw new Exception('Não excluiu original.');

    // Renomei para o novo nome do arquivo
    if (!@copy($tempPDF, $NovoNome))
        throw new Exception('Não copiou o arquivo PDF.');

    // Exclui temporário
    @unlink($tempPDF);

    // Retorna o caminho novo do PDF
    return $NovoNome;
}

function ConvertAcceptFormatPDF($Caminho) {

    // Cria temporário
    $nomeTmp  = tmpfile();
    $metadata = stream_get_meta_data($nomeTmp);
    $pathTmp  = $metadata['uri'];

    // Converte
    @exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile='{$pathTmp}' '{$Caminho}'");

    // Verifica consistencia do PDF
    if ( @ filesize($pathTmp) == 0 )
        throw new Exception('Houve erro na conversão de versionamento do PDF.');

    // Exclui o arquivo anterior
    if ( !@ unlink($Caminho) )
        throw new Exception('Não excluiu original.');

    // Renomei para o novo nome do arquivo
    if ( !@ copy($pathTmp, $Caminho) )
        throw new Exception('Não copiou o arquivo PDF.');

    // Exclui temporário
    if ( !@fclose($nomeTmp) )
        throw new Exception('Não excluiu o arquivo temporário.');

    // Finaliza função
    return true;
}
