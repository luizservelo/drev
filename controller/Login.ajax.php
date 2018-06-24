<?php

ob_start();
session_start();

require '../model/Config.inc.php';

//RECUPERA O POST
$jSON = null;
$POST = filter_input_array(INPUT_POST, FILTER_DEFAULT);

$Action = $POST['callback_action'];
unset($POST['callback_action'], $POST['callback']);

// CRUD

$Create = new Create;
$Read = new Read;
$Update = new Update;
$Delete = new Delete;
$Upload = new Upload('../uploads/');

switch ($Action) {
    case 'doLogin':

        $Read->ExeRead("app_user", "WHERE user_email = :email", "email={$POST['user_email']}");

        if($Read->getResult()) :
            $User = $Read->getResult()[0];

            $POST['user_password'] = hash("sha512", $POST['user_password']);

            if($POST['user_password'] == $User['user_password']) :
                $jSON['trigger'] = createNotify("Tudo certo, {$User['user_name']}. Login efetuado com sucesso.", "icon-checkmark", "green", 5000);
                $jSON['success'] = true;
                $jSON['clear'] = true;
                $jSON['redirect'] = BASE;
                $_SESSION['login'] = $Read->getResult()[0];
            else :
                $jSON['trigger'] = createNotify("Senha incorreta. Verifique os dados informados.", "icon-cancel-circle", "red", 5000);
            endif;

        else :
            $jSON['trigger'] = createNotify("Usuário não encontrado. Verifique seus dados ou crie uma conta.", "icon-cancel-circle", "red", 5000);
        endif;

        break;

    case 'create' :

        // var_dump($POST);

        if(in_array("", $POST)) :
            $jSON['trigger'] = createNotify("Por favor, preencha todos os campos.", "icon-cancel-circle", "yellow", 5000);
            break;
        endif;

        $Read->ExeRead("app_user", "WHERE user_email = :email", "email={$POST['user_email']}");

        if($Read->getResult()) :
            $jSON['trigger'] = createNotify("Opss, email já cadastrado.", "icon-cancel-circle", "yellow", 5000);
        else :
            if(strlen($POST['user_password']) <= 5) :
                $jSON['trigger'] = createNotify("Senha muito curta! Para sua segurança informe uma senha com mais de 5 caracteres", "icon-key", "yellow", 5000);
            else :
                $POST['user_password'] = hash("sha512", $POST['user_password']);
                $Create->ExeCreate("app_user", $POST);

                if($Create->getResult()) :
                    $jSON['trigger'] = createNotify("Tudo certo, {$POST['user_name']}. Conta criada com sucesso.", "icon-checkmark", "green", 5000);
                    $jSON['clear'] = true;
                    $jSON['success'] = true;
                    $jSON['redirect'] = BASE;

                    $Read->ExeRead("app_user", "WHERE user_id = :id", "id={$Create->getResult()}");
                    var_dump($_SESSION);
                    $_SESSION['login'] = $Read->getResult()[0];
                    var_dump($_SESSION);

                else :
                    $jSON['trigger'] = createNotify("Algo de errado não está certo, contate o administrador.", "icon-cancel-circle", "red", 5000);
                endif;

            endif;
        endif;

        break;

    case 'logout' :
        $User = $_SESSION['login']['user_name'];
        unset($_SESSION['login']);
        $jSON['redirect'] = BASE;
        $jSON['success'] = true;
        $jSON['trigger'] = createNotify("Volte logo, {$User}!", "icon-checkmark", "green", 5000);

        break;

    case 'manage' :

        if(!empty($POST['user_password']) && strlen($POST['user_newpassword']) <= 5) :
            $jSON['trigger'] = createNotify("Por favor, sua senha precisa ter mais de 5 caracteres.", "icon-key", "red", 5000);
            break;
        endif;

        if(!empty($POST['user_newpassword']) && strlen($POST['user_newpassword']) > 5) :
            if(empty($POST['user_password'])) :
                $jSON['trigger'] = createNotify("Por favor, preencha sua senha atual para modificar a senha.", "icon-cancel-circle", "yellow", 5000);
                break;
            else :
                $POST['user_password'] = hash("sha512", $POST['user_password']);

                if($POST['user_password'] == $_SESSION['login']['user_password']) :
                    $POST['user_password'] = hash("sha512", $POST['user_newpassword']);
                    unset($POST['user_newpassword']);
                else :
                    $jSON['trigger'] = createNotify("Senha incorreta.", "icon-key", "red", 5000);
                    break;
                endif;
            endif;
        else :
            unset($POST['user_newpassword'], $POST['user_password']);
        endif;

        if(in_array("", $POST)) :
            $jSON['trigger'] = createNotify("Por favor, preencha seu nome e sobrenome.", "icon-cancel-circle", "yellow", 5000);
            break;
        else :

            $Update->ExeUpdate("app_user", $POST, "WHERE user_id = :id", "id={$_SESSION['login']['user_id']}");

            $jSON['trigger'] = createNotify("Conta atualizada com sucesso!", "icon-checkmark", "green", 5000);
            $jSON['redirect'] = BASE.'/conta';
            $Read->ExeRead('app_user', "WHERE user_id = :id", "id={$_SESSION['login']['user_id']}");

            $_SESSION['login'] = $Read->getResult()[0];

        endif;




        break;

    case 'sendImage' :

        // var_dump($_FILES);

        if (!empty($_FILES['user_thumb'])):
            $UserThumb = $_FILES['user_thumb'];
            $Read->FullRead("SELECT user_thumb FROM app_user WHERE user_id = :id", "id={$_SESSION['login']['user_id']}");
            if ($Read->getResult()):
                if (file_exists("../uploads/{$Read->getResult()[0]['user_thumb']}") && !is_dir("../uploads/{$Read->getResult()[0]['user_thumb']}")):
                    unlink("../uploads/{$Read->getResult()[0]['user_thumb']}");
                endif;
            endif;

            $Upload->Image($UserThumb, $_SESSION['login']['user_id'] . "-" . Check::Name($_SESSION['login']['user_name'] . $_SESSION['login']['user_lastname']) . '-' . time(), 600);
            if ($Upload->getResult()):
                $POST['user_thumb'] = $Upload->getResult();
                $Update->ExeUpdate("app_user", [
                    'user_thumb'    =>  $POST['user_thumb']
                ], "WHERE user_id = :id", "id={$_SESSION['login']['user_id']}");
                $jSON['trigger'] = createNotify("Imagem atualizada com sucesso!", "icon-image", "green", 5000);
                $jSON['redirect'] = BASE.'/conta';
                $Read->ExeRead('app_user', "WHERE user_id = :id", "id={$_SESSION['login']['user_id']}");

                $_SESSION['login'] = $Read->getResult()[0];
            else:
                $jSON['trigger'] = createNotify("<b>ERRO AO ENVIAR FOTO:</b> Olá {$_SESSION['login']['user_name']}, selecione uma imagem JPG ou PNG para enviar como foto!", "icon-image", "yellow", 5000);
                echo json_encode($jSON);
                return;
            endif;

        else :

            $jSON['trigger'] = createNotify("Envie uma imagem, {$_SESSION['login']['user_name']}", "icon-image", "yellow", 5000);
            break;

        endif;

        break;

    case 'processingNew' :

        $Create->ExeCreate("app_processamento", [
            'processamento_title'   => $POST['dre_title'],
            'processamento_name'    => Check::Name($POST['dre_title']),
            'user_id'               => $_SESSION['login']['user_id']
        ]);

        if($Create->getResult()) :
            $jSON['trigger'] = createNotify("Tudo certo, vamos processar seu arquivo", "icon-checkmark", "green", 5000);
            $jSON['dre'] = $Create->getResult();
        else :
            $jSON['trigger'] = createNotify("Algo de errado não está certo, contate o administrador", "icon-cancel-circle", "red", 5000);
        endif;

        $Read->ExeRead("app_processamento", "WHERE user_id = :id ORDER BY processamento_id DESC", "id={$_SESSION['login']['user_id']}");
        $ListDRE = "";
        foreach($Read->getResult() as $DRE) :
            $ListDRE .= "<div class='item'>
                <div class='desc'>
                <p class='itemTitle'>{$DRE['processamento_title']}</p>
                <p class='icon-calendar data'>".date_format(date_create($DRE['processamento_timestamp']), "d/m/Y H:i")."</p>
                </div>

                <div class='actions'>
                    <a class='icon-stats-dots  btn btn_small radius btn_blue icon-notext icon-eye' target='_blank' href='".BASE."/visualizar-simples/{$DRE['processamento_id']}'></a>
                    <span class='icon-stats-dots btn btnPreDelete btn_small radius btn_red icon-notext icon-cancel-circle' data-delete='del-{$DRE['processamento_id']}'></span>
                    <span class='icon-stats-dots btn btn_small radius btn_yellow icon-warning btnAjax' id='del-{$DRE['processamento_id']}' data-c='Login' data-ca='deleteProcessamento' data-key='{$DRE['processamento_id']}' style='display: none'>DELETAR!</span>
                </div>
            </div>";
        endforeach;

        $jSON['content'] = [
            '.returnProcessamentos' => $ListDRE
        ];

        break;

    case 'sendRegras' :

        // var_dump($POST);
        $POST['content'] = explode("\n", $POST['content']);

        $RegraGeral = extractRule($POST['content'][1]);

        $Excecoes = [];

        $Nodes = [];
        $Edges = [];

        for($i = 3; $i < count($POST['content']); $i++){

            $Excecoes[] = extractRule($POST['content'][$i]);

            if($i == 3) {

                $Nodes[] = $Excecoes[count($Excecoes) - 1]['a'];
                $Nodes[] = $Excecoes[count($Excecoes) - 1]['b'];
                // $Edges[$Excecoes[count($Excecoes) - 1]][] = ['a'];
                $Edges[$Excecoes[count($Excecoes) - 1]['a']][$Excecoes[count($Excecoes) - 1]['b']] = [
                    'suporte' => $Excecoes[count($Excecoes) - 1]['suporte'],
                    'confianca' => $Excecoes[count($Excecoes) - 1]['confianca']
                ];

            }
            else{
                $Nodes[] = $Excecoes[count($Excecoes) - 1]['b'];
                $Edges[$Excecoes[count($Excecoes) - 1]['a']][$Excecoes[count($Excecoes) - 1]['b']] = [
                    'suporte' => $Excecoes[count($Excecoes) - 1]['suporte'],
                    'confianca' => $Excecoes[count($Excecoes) - 1]['confianca']
                ];
            }

        }

        $Content['nodes'] = $Nodes;
        $Content['edges'] = $Edges;

        $Content = json_encode($Content);

        // var_dump($Content);

        $Create->ExeCreate("app_regra", [
            'regra_content' => $Content,
            'regra_qtd'     => count($POST['content']) - 3,
            'regra_suporte' => $RegraGeral['suporte'],
            'regra_confianca' => $RegraGeral['confianca'],
            'processamento_id'  => $POST['processamento_id'],
            'regra_nome'    => $RegraGeral['nome']
        ]);

        $jSON['successRegra'] = true;


        break;

    case 'processing':

        $POST['content'] = file_get_contents($_FILES['drefile']['tmp_name']);

            // Quebra em array
        $file = explode("\n", $POST['content']);
        $flagRegraGeral = 0;
        $flagExcecao = 0;

            $regrasGerais = [];

            for($i = 0; $i < count($file); $i++){

                if($flagRegraGeral) :
                    $auxRG = explode(" <- ", $file[$i]);

                    $auxRG[1] = explode(" ", $auxRG[1]);
                    $auxRG[1] = $auxRG[1][0];

                    $regrasGerais[$auxRG[0]." | ".$auxRG[1]]['nodes'][0] = "TRASHVALUE";
                    $regrasGerais[$auxRG[0]." | ".$auxRG[1]]['edges'][0] = "TRASHVALUE";

                    $flagRegraGeral = 0;
                endif;

                if($flagExcecao) :
                    if($file[$i] != "Regra:" && $file[$i] != ""):
                        // var_dump($file[$i]);

                        $aux = explode(" <- ", $file[$i]);

                        $aux[1] = explode("  ", $aux[1]);
                        $aux[1] = $aux[1][0];

                        $key = array_search($aux[0], $regrasGerais[$auxRG[0]." | ".$auxRG[1]]['nodes']);

                        if($key == 0) :
                            $regrasGerais[$auxRG[0]." | ".$auxRG[1]]['nodes'][] = $aux[0];
                            $keyA = count($regrasGerais[$auxRG[0]." | ".$auxRG[1]]['nodes']) - 1;
                        else :
                            $keyA = $key;
                        endif;

                        $key = array_search($aux[1], $regrasGerais[$auxRG[0]." | ".$auxRG[1]]['nodes']);

                        if(!$key) :
                            $regrasGerais[$auxRG[0]." | ".$auxRG[1]]['nodes'][] = $aux[1];
                            $keyB = count($regrasGerais[$auxRG[0]." | ".$auxRG[1]]['nodes']) - 1;
                        else :
                            $keyB = $key;
                        endif;

                        // Monta os Edges

                        $regrasGerais[$auxRG[0]." | ".$auxRG[1]]['edges'][$keyA][] = [$keyB];

                        // var_dump($aux);
                    endif;
                endif;

                // -----------------------------------------------------------------
                // BLOCO DE MUDANÇA DE FLAGS
                // -----------------------------------------------------------------

                if($file[$i] == "Regra:" && $file[$i] != "\n") :
                    $flagRegraGeral = 1;
                    $flagExcecao = 0;
                endif;

                if($file[$i] == "Excecoes:" && $file[$i] != "\n") :
                    $flagRegraGeral = 0;
                    $flagExcecao = 1;
                endif;


            }

            $grafo = [];
            $stringJson = "";

            foreach($regrasGerais as $Key => $Regra) :
                unset($Regra['nodes'][0],$Regra['edges'][0]);

                $auxNodes = [];
                for($i = 1; $i <= count($Regra['nodes']); $i++){
                    if($i == 1) :
                        $auxNodes[$Regra['nodes'][$i]] = [
                            'label' => $Regra['nodes'][$i],
                            'shape' => 'dot',
                            'color' => 'pink'
                        ];
                    else :
                        $auxNodes[$Regra['nodes'][$i]] = [
                            'label' => $Regra['nodes'][$i]
                        ];
                    endif;
                }
                $auxEdges = [];

                for($i = 2; $i <= count($Regra['nodes']); $i++) {
                    $auxEdges[$Regra['nodes'][1]][$Regra['nodes'][$i]] = [
                        "border" => '200'
                    ];
                }

                $Regra['nodes'] = $auxNodes;
                $Regra['edges'] = $auxEdges;
                $grafo[$Key] = [$Regra];
                // var_dump($grafo[$Key]);
            endforeach;
            // var_dump(json_encode($grafo));
            // $size = strlen($stringJson);
            //
            // $sis = substr($stringJson,0, $size-1);
            //
            // $fileName = $POST['name']."_".date('Y-m-d H:i:s').".json";
            //
            // $fp = fopen("../uploads/".$fileName, "a");
            //
            // $escreve = fwrite($fp, json_encode($grafo, JSON_PRETTY_PRINT));
            // $escreve = fwrite($fp, $stringJson);

            // fclose($fp);

            // $nome = explode("_", $fileName);
            //
            // $nome[1] = str_replace(".json", "", $nome[1]);

            $POST['dre_content'] = json_encode($grafo);
            $POST['dre_name'] = Check::Name($POST['dre_title']).date('YmdHis');
            $POST['user_id'] = $_SESSION['login']['user_id'];
            unset($POST['content']);

            $Create->ExeCreate("app_dre", $POST);

            $ListDRE = '';

            $Read->ExeRead("app_dre", "WHERE user_id = :id ORDER BY dre_id DESC", "id={$_SESSION['login']['user_id']}");

            foreach($Read->getResult() as $DRE) :
                $ListDRE .= "<div class='item'>
                    <div class='desc'>
                    <p class='itemTitle'>{$DRE['dre_title']}</p>
                    <p class='icon-calendar data'>".date_format(date_create($DRE['dre_timestamp']), "d/m/Y H:i")."</p>
                    </div>

                    <div class='actions'>
                        <a class='icon-stats-dots  btn btn_small radius btn_blue icon-notext icon-eye' target='_blank' href='".BASE."/visualizar/{$DRE['dre_id']}'></a>
                        <span class='icon-stats-dots btn btnPreDelete btn_small radius btn_red icon-notext icon-cancel-circle' data-delete='del-{$DRE['processamento_id']}'></span>
                        <span class='icon-stats-dots btn btn_small radius btn_yellow icon-warning btnAjax' id='del-{$DRE['processamento_id']}' data-c='Login' data-ca='deleteProcessamento' data-key='{$DRE['processamento_id']}' style='display: none'>DELETAR!</span>
                    </div>
                </div>";
            endforeach;

            $jSON['content'] = [
                '.returnProcessamentos' => $ListDRE
            ];
            $jSON['success'] = true;
            $jSON['trigger'] = createNotify("Arquivo processado com sucesso", "icon-checkmark", "green", 5000);
            $jSON['clear'] = true;

            // $files ="<li><a target='_blank' href='visualizador.php?file=uploads/".$fileName."'>".$nome[0]." - ".date('d/m/Y H:i:s', strtotime($nome[1]))."</a></li>";

            $jSON['clear'] = true;
            break;

    case 'GetGraph' :

        if(!empty($POST['index'])) :
            $Read->ExeRead("app_regra", "WHERE regra_id = :id", "id={$POST['regra_id']}");
            $Regra = $Read->getResult()[0];

            $RegraGraph = json_decode($Regra['regra_content'], true);

            $Edges = [];

            $Edges[$POST['index']] = $RegraGraph['edges'][$POST['index']];


            $Nodes = [];
            $Nodes[] = $POST['index'];

            foreach($Edges[$POST['index']] as $Key => $Value) :
                $Nodes[] = $Key;
            endforeach;

            $RegraGraph = [
                'nodes' => $Nodes,
                'edges' => $Edges
            ];

            // var_dump($RegraGraph);

            $jSON['graph'] = $RegraGraph;


        else :
            $Read->ExeRead("app_regra", "WHERE regra_id = :id", "id={$POST['regra_id']}");
            $Regra = $Read->getResult()[0];

            $RegraGraph = json_decode($Regra['regra_content'], true);

            // var_dump($RegraGraph['edges']);

            if(count($RegraGraph['edges']) > 1) :

                $IndexArray = [];

                $SelectIndex = "<label style='flex-basis: 100%'><select name='index' required><option value='' selected='selected' disabled>Selecione um index</option>";

                foreach($RegraGraph['edges'] as $Index => $Value) :
                    $IndexArray[] = $Index;
                    $SelectIndex .= "<option value='{$Index}'>{$Index} [RE: ".count($Value)."]</option>";
                endforeach;

                $SelectIndex .= "</select></label>";
                $jSON['index'] = $IndexArray;
                $jSON['trigger'] = createNotify("Temos mais de um index de exceção, escolha um para montar o grafo", "icon-cogs", "blue", 10000);
                $jSON['regra_id'] = $POST['regra_id'];
                $jSON['selectIndex'] = $SelectIndex;

            else :

                $jSON['graph'] = $RegraGraph;

            endif;
        endif;

        break;

    case 'deleteProcessamento' :

        $Read->ExeRead("app_processamento", "WHERE processamento_id = :key", "key={$POST['key']}");

        $Proc = $Read->getResult()[0];

        if($Proc['user_id'] == $_SESSION['login']['user_id']) :
            $Delete->ExeDelete("app_regra", "WHERE processamento_id = :key", "key={$POST['key']}");
            $Delete->ExeDelete("app_processamento", "WHERE processamento_id = :key", "key={$POST['key']}");
            $ListDRE = '';

            $Read->ExeRead("app_processamento", "WHERE user_id = :id ORDER BY processamento_id DESC", "id={$_SESSION['login']['user_id']}");

            foreach($Read->getResult() as $DRE) :
                $ListDRE .= "<div class='item'>
                    <div class='desc'>
                    <p class='itemTitle'>{$DRE['processamento_title']}</p>
                    <p class='icon-calendar data'>".date_format(date_create($DRE['processamento_timestamp']), "d/m/Y H:i")."</p>
                    </div>

                    <div class='actions'>
                        <a class='icon-stats-dots  btn btn_small radius btn_blue icon-notext icon-eye' target='_blank' href='".BASE."/visualizar-simples/{$DRE['processamento_id']}'></a>
                        <span class='icon-stats-dots btn btnPreDelete btn_small radius btn_red icon-notext icon-cancel-circle' data-delete='del-{$DRE['processamento_id']}'></span>
                        <span class='icon-stats-dots btn btn_small radius btn_yellow icon-warning btnAjax' id='del-{$DRE['processamento_id']}' data-c='Login' data-ca='deleteProcessamento' data-key='{$DRE['processamento_id']}' style='display: none'>DELETAR!</span>
                    </div>
                </div>";
            endforeach;

            $jSON['content'] = [
                '.returnProcessamentos' => $ListDRE
            ];
            $jSON['trigger'] = createNotify("Processamento deletado com sucesso", "icon-checkmark", "green", 5000);

        else :

            $jSON['trigger'] = createNotify("Erro na exclusão", "icon-skull", "red", 5000);
        endif;


        break;

    default:
        // code...
        break;
}

echo json_encode($jSON);

ob_end_flush();
