<?php

if(empty($_SESSION['login'])) :
    header('Location: '.BASE.'/login');
endif;

?>

<?php

    if(!empty($URL[1])) :

        $Read->ExeRead("app_dre", "WHERE dre_id = :id", "id={$URL[1]}");
        // var_dump($Read->getResult()[0]);

        $jSON = $Read->getResult()[0]['dre_content'];
        $file = json_decode($jSON, true);

        $Data = date_create($Read->getResult()[0]['dre_timestamp']);

        $Data = date_format($Data, "d/m/Y H:i:s");

    else :
        header('Location: '.BASE);
    endif;

?>

<main class="dash_main">
    <header class="dash_main_header">
        <span class="icon-menu icon-notext dash_main_header_menu radius"></span>
        <h2 class="dash_main_header_view"><a href="#" title="Visualizar Processamento">Visualizar Processamento (<?= $Read->getResult()[0]['dre_title']." - ".$Data ?>)</a></h2>
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
            <div class="formActions">
                <form class="processingForm dash_login_box_content_form basic_form dash_profile_section_form" action="" method="post">
                    <label>
                        <select name="generalRule radius" style="width: 100%">
                            <option value="" selected="selected" disabled>Selecione uma Regra Geral</option>
                            <?php
                            foreach($file as $Key => $Other) :
                                echo "<option value='{$Key}'>".str_replace("|", "<-", $Key)."</option>";
                            endforeach;
                            ?>
                        </select>
                    </label>
                    <label>
                        <button class="btn btn_blue radius" style='padding: 13px 0px;'><b>PROCESSAR</b></button>
                    </label>
                </form>
            </div>
            <div class="viewGraph radius">
                <!-- Aqui entra o canvas -->
                <canvas id="janela"></canvas>
            </div>
            <div class="graphActions">
                <form class="simular" action="" method="post">
                    <textarea name="name" id="return" placeholder="jSON retornado"></textarea>
                    <button class="button is-danger btn_medium btn_blue radius" name="button">SIMULAR</button>
                </form>
            </div>
        </div>
    </section>
</main>

<script type="text/javascript">
    var jsonFile = {};

    var data = {};
    $(function(){

        var jsonBD = '<?= $jSON ?>';

        jsonBD = JSON.parse(jsonBD);

        updateObject(jsonBD);



        $('.processingForm').submit(function(e){
            e.preventDefault();

            var form = $(this);

            var obj = form.find('select').val();

            $.each(jsonFile.data, function(index, data){

                if(index == obj){
                    console.log(index, data[0]);

                    // repulsão 1.000 a força repelindo nós uns dos outros
                    // rigidez 600 a rigidez das bordas
                    // atrito 0,5 a quantidade de amortecimento no sistema
                    // gravidade 0 uma força adicional atraindo nós para a origem
                    // fps 55 quadros por segundo
                    // dt 0.02 timestep para usar para pisar a simulação
                    // precisão 0,6 precisão vs. cálculos de velocidade em vigor

                    var sys = arbor.ParticleSystem(10000,10000, 1, true, 155, 2, 0);
                    sys.screenPadding(30,30,30,30);
                    sys.renderer = Renderer("#janela");
                    //
                    // sys.addEdge('a','b');
                    // sys.addEdge('a','c');
                    // sys.addEdge('a','d');
                    // sys.addEdge('a','e');

                    $('#return').val(JSON.stringify(data[0], undefined, 4));
                    //
                    sys.graft(data[0]);
                }
            })

        })

        $('.simular').submit(function(e){
            e.preventDefault();
            var form = $(this);
            var data = JSON.parse($("#return").val());
            console.log(data);

            var sys = arbor.ParticleSystem(40,10000, 1, false, 155, 2, 0);
            // sys.parameters({gravity:120});
            sys.renderer = Renderer("#janela");

            $('#return').val(JSON.stringify(data, undefined, 4));

            sys.graft(data);

        })

    })

    function updateObject(objeto){
        jsonFile.data = objeto;
        console.log(jsonFile);
    }
    function updateNodes(objeto){
        data.nodes = objeto;
        console.log(data.nodes);
    }



</script>
