<?php

/*
 * BANCO DE DADOS
 */
define('SIS_DB_HOST', 'localhost'); //Link do banco de dados
define('SIS_DB_USER', 'root'); //Usuário do banco de dados
define('SIS_DB_PASS', ''); //Senha  do banco de dados
define('SIS_DB_DBSA', 'tcc'); //Nome  do banco de dados

/*
 * SISTEMA
 */
define('BASE', 'https://localhost/drev3');
define('APP_TITLE', 'DREV');
define('APP_DESCRIPTION', 'VISUALIZADOR DE REGRAS DE EXCEÇÃO');

/*
  AUTO LOAD DE CLASSES
 */

function MyAutoLoad($Class) {
    $cDir = ['Conn', 'Helpers', 'Models', 'WorkControl'];
    $iDir = null;

    foreach ($cDir as $dirName):
        if (!$iDir && file_exists(__DIR__ . '/' . $dirName . '/' . $Class . '.class.php') && !is_dir(__DIR__ . '/' . $dirName . '/' . $Class . '.class.php')):
            include_once (__DIR__ . '/' . $dirName . '/' . $Class . '.class.php');
            $iDir = true;
        endif;
    endforeach;
}

spl_autoload_register("MyAutoLoad");

/*
 * Exibe erros lançados
 */

function Erro($ErrMsg, $ErrNo = null) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? 'trigger_info' : ($ErrNo == E_USER_WARNING ? 'trigger_alert' : ($ErrNo == E_USER_ERROR ? 'trigger_error' : 'trigger_success')));
    echo "<div class='trigger {$CssClass}'>{$ErrMsg}<span class='ajax_close'></span></div>";
}

/*
 * Exibe erros lançados por ajax
 */

function AjaxErro($ErrMsg, $ErrNo = null) {
    $CssClass = ($ErrNo == E_USER_NOTICE ? 'trigger_info' : ($ErrNo == E_USER_WARNING ? 'trigger_alert' : ($ErrNo == E_USER_ERROR ? 'trigger_error' : 'trigger_success')));
    return "<div class='trigger trigger_ajax {$CssClass}'>{$ErrMsg}<span class='ajax_close'></span></div>";
}

/*
 * personaliza o gatilho do PHP
 */

function PHPErro($ErrNo, $ErrMsg, $ErrFile, $ErrLine) {
    echo "<div class='trigger trigger_error'>";
    echo "<b>Erro na Linha: #{$ErrLine} ::</b> {$ErrMsg}<br>";
    echo "<small>{$ErrFile}</small>";
    echo "<span class='ajax_close'></span></div>";

    if ($ErrNo == E_USER_ERROR):
        die;
    endif;
}

set_error_handler('PHPErro');

function createNotify($mensagem, $icone, $cor, $timer = NULL){

    if(empty($timer)) :
        $timer = 5000;
    endif;

    return $notifyArray = [
        'mensagem'  =>  $mensagem,
        'icone'     =>  $icone,
        'cor'       =>  $cor,
        'timer'     =>  $timer
    ];
}

function extractRule($string){

    $returnArray = [];

    $array = explode("  ", $string);

    $returnArray['nome'] = str_replace(" <- "," | ",$array[0]);
    $aux = str_replace(" ","",$returnArray['nome']);
    $aux = explode("|",$returnArray['nome']);
    $returnArray['a'] = substr($aux[0], 0, -1);
    $returnArray['b'] = substr($aux[1], 1);

    $aux = str_replace("(","", $array[1]);
    $aux = str_replace(")","", $array[1]);
    $aux = str_replace("%","", $array[1]);
    $aux = str_replace("(","", $array[1]);
    $aux = explode(",",$aux);

    $returnArray['confianca'] = floatval($aux[0]);
    $returnArray['suporte'] =  floatval($aux[1]);


    return $returnArray;

}
