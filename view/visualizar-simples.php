<?php

if(empty($_SESSION['login'])) :
    header('Location: '.BASE.'/login');
endif;

?>

<?php

    if(!empty($URL[1])) :

        $Read->ExeRead("app_processamento", "WHERE processamento_id = :id", "id={$URL[1]}");
        // var_dump($Read->getResult()[0]);

        $Data = date_create($Read->getResult()[0]['processamento_timestamp']);

        $Data = date_format($Data, "d/m/Y H:i:s");

    else :
        header('Location: '.BASE);
    endif;

?>
<main class="dash_main">
    <header class="dash_main_header">
        <span class="icon-menu icon-notext dash_main_header_menu radius"></span>
        <h2 class="dash_main_header_view"><a href="#" title="Visualizar Processamento">Visualizar Processamento (<?= $Read->getResult()[0]['processamento_title']." - ".$Data ?>)</a></h2>
        <div class="dash_main_header_tools">
            <a class="icon-exit icon-notext dash_main_header_tools_item btnAjax" href="#"  title="Desconectar" data-c="Login" data-ca="logout" data-key="0"><b class="dash_main_header_tools_item_logoff">Sair</b></a>
        </div>
    </header>
    <section class='dash_view' style='display: block'>
        <div class="dash_view_desc dash_view_desc_activities">
            <a target="_blank" class="icon-bubbles4" href="#" title="Suporte">Suporte</a>
            <a target="_blank" class="icon-download" href="#" title="Download do Kit KDD">KDD Kit</a>
            <a class="icon-camera" href="#" title="Download do Kit KDD" id="export">Snapshot</a>

        </div>
        <div class="dash_view_home">
            <div class="actionsBox">
                <div class="actionsContent">
                    <button class='btn  radius icon-zoom-in icon-notext zoom zoominIt'></button>
                    <button class='btn  radius icon-radio-checked icon-notext zoom centerIt'></button>
                    <button class='btn  radius icon-zoom-out icon-notext zoom zoomoutIt'></button>
                    <button class='btn  radius icon-enlarge icon-notext zoom fullScreen'></button>
                </div>
                <div class="actionsHeader">
                    <button class="btn btn_blue btn_small icon-cogs radius controlButton">CONTROLES</button>
                </div>
            </div>
            <div class="formActions">
                <form class="processingForm dash_login_box_content_form basic_form dash_profile_section_form" action="" method="post">
                    <input type="hidden" name="callback_action" value="GetGraph">
                    <label>
                        <select name="regra_id" class='radius regra_id' style="width: 100%">
                            <option value="" selected="selected" disabled>Selecione uma Regra Geral</option>
                            <?php

                            $Read->ExeRead("app_regra", "WHERE processamento_id = :id ORDER BY regra_qtd DESC", "id={$URL[1]}");

                            foreach($Read->getResult() as $Regra) :
                                echo "<option value='{$Regra['regra_id']}'>".str_replace("|", "<-", $Regra['regra_nome'])." ({$Regra['regra_confianca']}%, {$Regra['regra_suporte']}%) [RE: {$Regra['regra_qtd']}]</option>";
                            endforeach;

                            ?>
                        </select>
                    </label>
                    <label>
                        <button class="btn btn_blue radius" style='padding: 13px 0px;'><b>PROCESSAR</b></button>
                    </label>
                </form>
            </div>
            <div class="viewGraph radius" style="position: relative">
                <div id="graph-container"></div>
            </div>
        </div>
    </section>
</main>
<script src="<?= BASE ?>/view/assets/js/src/sigma.min.js"></script>

<!-- END SIGMA IMPORTS -->
<script src="<?= BASE ?>/view/assets/js/plugins/sigma.layout.forceAtlas2/worker.js"></script>
<script src="<?= BASE ?>/view/assets/js/plugins/sigma.layout.forceAtlas2/supervisor.js"></script>
<script src="<?= BASE ?>/view/assets/js/plugins/sigma.exporters.svg/sigma.exporters.svg.js"></script>
<script src="<?= BASE ?>/view/assets/js/plugins/sigma.parsers.json/sigma.parsers.json.js"></script>
<script src="<?= BASE ?>/view/assets/js/plugins/sigma.plugins.dragNodes/sigma.plugins.dragNodes.js"></script>
<script src="<?= BASE ?>/view/assets/js/plugins/sigma.layout.noverlap/sigma.layout.noverlap.js"></script>
<script src="<?= BASE ?>/view/assets/js/plugins/sigma.plugins.animate/sigma.plugins.animate.js"></script>

<script>
$(function(){

    $('.regra_id').change(function(){
        if($('select[name="index"]').length){
            $('select[name="index"]').parent().remove();
            $('select[name="index"]').remove();
        }
    })

    $('#colorSelector').ColorPicker({
    	color: '#0000ff',
    	onShow: function (colpkr) {
    		$(colpkr).fadeIn(500);
    		return false;
    	},
    	onHide: function (colpkr) {
    		$(colpkr).fadeOut(500);
    		return false;
    	},
    	onChange: function (hsb, hex, rgb) {
    		$('#colorSelector div').css('backgroundColor', '#' + hex);
    	}
    });

    $('.processingForm').submit(function(e){
        e.preventDefault();

        var form = $(this);
        var data = form.serialize();

        $.ajax({
            url: "<?= BASE ?>/controller/Login.ajax.php",
            dataType: 'json',
            type: 'POST',
            data: data,
            success: function(data){
                console.log(data)

                if(data.selectIndex){
                    form.append(data.selectIndex);
                }
                if(data.trigger){
                    triggerNotify(data.trigger)
                }

                if(data.graph){
                    $("#graph-container").html("");

                    var s = new sigma({
                        settings: {
                            autoRescale: false
                        }
                    }),
                        cam = s.addCamera();

                    var edgeBall = 0;
                    $.map(data.graph.nodes, function(val, i){
                        if(i == 0){
                            var tam = 30;
                            var x =  200;
                            var y = 0;
                        }
                        else {
                            var tam = 15;
                            var x = -200;
                            var y = edgeBall;

                            edgeBall = edgeBall + 2;
                        }

                        // if($.inArray(val,g.nodes) !== -1){
                            s.graph.addNode({
                              id: val,
                              label: val,
                              x: x,
                              y: y,
                              size: tam,
                              color: ['#666', '#abc', '#eee', '#ff00aa'][Math.random() * 4 | 0]
                            });
                        // }

                    })

                    $.map(data.graph.edges, function(val, i){
                        var principal = i;
                        // var x = 1
                        $.map(val, function(valE, j){
                            s.graph.addEdge({
                              id: "e"+j,
                              source: i,
                              target: j,
                              size: 2,
                              color: "#EEEEEE"
                            });
                            // console.log(valE, j);
                        })

                    })

                    // console.log(g.edges);

                    // s.cameras[0].goTo({ x: 0, y: 0, angle: 0, ratio: 1 });

                    // Adding a canvas renderer

                    s.addRenderer({
                      container: 'graph-container',
                      type: 'svg',
                      camera: cam
                    })

                    s.refresh();



                    // s.cameras[0].goTo({ x: 0, y: 0, angle: 0, ratio: 1 });

                    var dragListener = sigma.plugins.dragNodes(s, s.renderers[0]);
                      dragListener.bind('startdrag', function(event) {
                      // console.log(event);
                      });
                      dragListener.bind('drag', function(event) {
                      // console.log(event);
                      });
                      dragListener.bind('drop', function(event) {
                      // console.log(event);
                      });
                      dragListener.bind('dragend', function(event) {
                      // console.log(event);
                      });

                      var config = {
                          scaleNodes: 2
                        };

                        // Configure the algorithm
                        var listener = s.configNoverlap(config);

                        // Bind all events:
                        listener.bind('start stop interpolate', function(event) {
                          console.log(event.type);
                        });

                      // Start the layout:
                      s.startNoverlap();

                      $(".zoominIt").bind("click",function(){
                          cam.goTo({
                              ratio: cam.ratio / cam.settings('zoomingRatio')
                          });

                      });
                      $(".zoomoutIt").bind("click",function(){
                          cam.goTo({
                              ratio: cam.ratio * cam.settings('zoomingRatio')
                          });
                      });



                      var height = $('#graph-container').height();
                      var width = $('#graph-container').width();

                      $(".centerIt").bind("click",function(){
                          cam.goTo({
                                x: width/2, y: height/2
                          });
                      });

                      cam.goTo({
                            x: width/2, y: height/2
                      });

                    // cam.goTo({ x: 600, y: 300, angle: 0, ratio: 2 });


                }
            }
        })

    })
})
</script>
