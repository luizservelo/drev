<?php

if(empty($_SESSION['login'])) :
    header('Location: '.BASE.'/login');
endif;

?>

<main class="dash_main">
    <header class="dash_main_header">
        <span class="icon-menu icon-notext dash_main_header_menu radius"></span>
        <h2 class="dash_main_header_view"><a href="#" title="Meus Processamentos">Meus Processamentos</a></h2>
        <div class="dash_main_header_tools">
            <a class="icon-exit icon-notext dash_main_header_tools_item btnAjax" href="#"  title="Desconectar" data-c="Login" data-ca="logout" data-key="0"><b class="dash_main_header_tools_item_logoff">Sair</b></a>
        </div>
    </header>
    <section class='dash_view' style='display: block'>
        <div class="dash_view_desc dash_view_desc_activities">
            <a target="_blank" class="icon-bubbles4" href="#" title="Suporte">Suporte</a>
            <a target="_blank" class="icon-download" href="#" title="Download do Kit KDD">KDD Kit</a>
        </div>
        <div class="dash_view_home">
            <div class="">
                <h1 class='dash_view_activities_title icon-stats-dots'>MEUS PROCESSAMENTOS</h1>
                <p class='dash_view_activities_newclasses_title'>Confira sua lista de processamentos</p>
            </div>
            <div class="processamentos returnProcessamentos">
                <?php

                $Read->ExeRead("app_processamento", "WHERE user_id = :id", "id={$_SESSION['login']['user_id']}");

                foreach($Read->getResult() as $DRE) :
                    echo "<div class='item'>
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

                ?>
            </div>

        </div>
    </section>
</main>
