$(function(){

    BASE = $('link[rel="base"]').attr('href');

    $('.controlButton').click(function(){
        $('.actionsContent').fadeToggle();
    })

    $(".fullScreen").click(function(){
        $(this).toggleClass("icon-shrink");
        $(".viewGraph").toggleClass("isFullScreen");
        // $('.processingForm').submit();
        // alert("TESTE");
    })

    $('.wc_loadimage').change(function () {
        var input = $(this);
        var target = $('.' + input.attr('name'));
        var fileDefault = target.attr('default');

        if (!input.val()) {
            target.fadeOut('fast', function () {
                $(this).attr('src', fileDefault).fadeIn('slow');
            });
            return false;
        }

        if (this.files && (this.files[0].type.match("image/jpeg") || this.files[0].type.match("image/png"))) {
            var reader = new FileReader();
            reader.onload = function (e) {
                target.fadeOut('fast', function () {
                    $(this).attr('src', e.target.result).fadeIn('fast');
                });
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            // Trigger('<div class="trigger trigger_alert trigger_ajax"><b class="icon-warning">ERRO AO SELECIONAR:</b> O arquivo <b>' + this.files[0].name + '</b> não é válido! <b>Selecione uma imagem JPG ou PNG!</b></div>');
            target.fadeOut('fast', function () {
                $(this).attr('src', fileDefault).fadeIn('slow');
            });
            input.val('');
            return false;
        }
    });

    // AJAX BUTTON

    $('body').on('click', '.btnAjax', function(e){
        e.preventDefault();

        var callback = $(this).attr('data-c');
        var callback_action = $(this).attr('data-ca');
        var key = $(this).attr('data-key');

        $.post(BASE+'/controller/'+callback+'.ajax.php', {callback: callback, callback_action: callback_action, key: key}, function (data) {
            if(data.trigger){
                triggerNotify(data.trigger);
            }
            if (data.redirect) {
                setTimeout(function () {
                    window.location.href = data.redirect;
                }, 2000);
            }
            if (data.content) {
                if (typeof (data.content) === 'string') {
                    $('.j_content').fadeTo('300', '0.5', function () {
                        $(this).html(data.content).fadeTo('300', '1');
                    });
                } else if (typeof (data.content) === 'object') {
                    $.each(data.content, function (key, value) {
                        $(key).fadeTo('300', '0.5', function () {
                            $(this).html(value).fadeTo('300', '1');
                        });
                    });
                }
            }
        }, 'json');

    })

    // Disparo de Formulário

    // $('.submitDRE').submit(function(e){
    //     e.preventDefault();
    //     var form = $(this);
    //     form.find('button').addClass('is-loading');
    //
    //     var name = form.find('input[name="dre_title"]').val();
    //
    //     var file = document.getElementById('myFile').files[0];
    //     if (file) {
    //         var reader = new FileReader();
    //         reader.readAsText(file);
    //         reader.onload = function(data) {
    //             var content = data.target.result;
    //             $.ajax({
    //                 url: BASE+'/controller/Processing.ajax.php',
    //                 dataType: 'json',
    //                 data: {action: 'processing', content: content, dre_title: name},
    //                 type: 'POST',
                    // success: function (data) {
                    //     console.log(data);
                    //     if(data.clear){
                    //         form.trigger('reset');
                    //     }
                    //     if(data.files){
                    //         $('.files').prepend(data.files);
                    //     }
                    //     if(data.trigger){
                    //         triggerNotify(data.trigger)
                    //     }
                    //     if (data.redirect) {
                    //         setTimeout(function () {
                    //             window.location.href = data.redirect;
                    //         }, 2000);
                    //     }
                    //     if (data.content) {
                    //         if (typeof (data.content) === 'string') {
                    //             $('.j_content').fadeTo('300', '0.5', function () {
                    //                 $(this).html(data.content).fadeTo('300', '1');
                    //             });
                    //         } else if (typeof (data.content) === 'object') {
                    //             $.each(data.content, function (key, value) {
                    //                 $(key).fadeTo('300', '0.5', function () {
                    //                     $(this).html(value).fadeTo('300', '1');
                    //                 });
                    //             });
                    //         }
                    //     }
                    //     form.find('button').removeClass('is-loading');
                    // }
    //             })
    //         };
    //
    //     }
    //
    // })

    $('.submitDRE').submit(function(e){
        e.preventDefault();

        var form = $(this);

        form.ajaxSubmit({
            url: BASE+'/controller/Processing.ajax.php',
            dataType: 'json',
            data: {action: 'processing'},
            type: 'POST',
            success: function (data) {
                console.log(data);
                if(data.clear){
                    form.trigger('reset');
                }
                if(data.files){
                    $('.files').prepend(data.files);
                }
                if(data.trigger){
                    triggerNotify(data.trigger)
                }
                if (data.redirect) {
                    setTimeout(function () {
                        window.location.href = data.redirect;
                    }, 2000);
                }
                if (data.content) {
                    if (typeof (data.content) === 'string') {
                        $('.j_content').fadeTo('300', '0.5', function () {
                            $(this).html(data.content).fadeTo('300', '1');
                        });
                    } else if (typeof (data.content) === 'object') {
                        $.each(data.content, function (key, value) {
                            $(key).fadeTo('300', '0.5', function () {
                                $(this).html(value).fadeTo('300', '1');
                            });
                        });
                    }
                }
                form.find('button').removeClass('is-loading');
            }
        })
    })

    $('.defaultForm').submit(function(e){
        e.preventDefault();

        var form = $(this);
        // var data = form.serialize();

        var callback = form.find('input[name="callback"]').val();
        var callback_action = form.find('input[name="callback_action"]').val();

        // PROCESSAMENTO DE ARQUIVO

        if(callback_action == "processingNew"){
            $('.sendBackground').css('display', 'flex');
            var title = $('input[name="dre_title"]').val();
            var file = document.getElementById('myFile').files[0];
            var reader = new FileReader();
            reader.readAsText(file);
            reader.onload = function(data) {
                var objeto = {};
                var content = data.target.result;
                objeto.countRegras = (content.match(/Regra:/g) || []).length;
                var contentArray = content.split("\n");
                var auxString = "";
                // console.log(contentArray);
                var arraySize = contentArray.length;

                if(1){
                    // form.find('button').prop('disabled', true).text('CARREGANDO');

                    $.ajax({
                        url: BASE+'/controller/'+callback+'.ajax.php',
                        data: {dre_title: title, callback_action: callback_action},
                        dataType: 'json',
                        type: 'POST',
                        async: false,
                        beforeSend: function(){
                            $('.sendBackground').css('display', 'flex');
                        },
                        success: function(data){
                            // console.log(data);
                            if(data.trigger){
                                triggerNotify(data.trigger)
                            }
                            if (data.content) {
                                if (typeof (data.content) === 'string') {
                                    $('.j_content').fadeTo('300', '0.5', function () {
                                        $(this).html(data.content).fadeTo('300', '1');
                                    });
                                } else if (typeof (data.content) === 'object') {
                                    $.each(data.content, function (key, value) {
                                        $(key).fadeTo('300', '0.5', function () {
                                            $(this).html(value).fadeTo('300', '1');
                                        });
                                    });
                                }
                            }
                            if(data.dre){



                                objeto.count = 0;
                                for(var i = 0; i < arraySize; i++){
                                    if(contentArray[i].length > 1){
                                        var m = auxString.length;
                                        if(m == 0){
                                            auxString = contentArray[i];
                                        }
                                        else{
                                            auxString = auxString + "\n" + contentArray[i];
                                        }
                                    }
                                    else{
                                        // POST

                                        // console.log(auxString);
                                        // console.log(i);

                                        $.when($.ajax({
                                            url: BASE+'/controller/'+callback+'.ajax.php',
                                            data: {processamento_id: data.dre, callback_action: "sendRegras", content: auxString},
                                            dataType: 'json',
                                            type: 'POST',
                                            async: true,

                                            beforeSend(a,b,c,d){
                                                $('.sendBackground').css('display', 'flex');
                                                // console.log(objeto);
                                                // document.getElementById("percentageTag").innerHTML = objeto.percentage;
                                                // $('#percentageTag').text(objeto.percentage);
                                            },
                                            success: function(data){

                                                objeto.count++;
                                                // console.log(objeto);

                                                if(objeto.count >= objeto.countRegras - 2){
                                                    $('.sendBackground').fadeOut(400);
                                                    $('.sendBackground h3').text("0%");
                                                    $('.defaultForm').trigger("reset");
                                                }
                                                else {

                                                    objeto.percentage = parseInt(objeto.count/objeto.countRegras * 100);
                                                    $('.sendBackground').css('display', 'flex');
                                                    // $('.sendBackground h3').text(percentage) + "%");

                                                }

                                            }
                                        })).then(function( data, textStatus, jqXHR ) {
                                              console.log( objeto.percentage ); // Console Percentage
                                              $("#percentageTag").text(objeto.percentage);
                                          });



                                        auxString = "";
                                        // console.log(objeto.count);
                                    }

                                }
                            }
                        }
                    })
                }
                else{

                    alert("ERRO NO ARQUIVO");

                }

            }

        }
        else{
            form.find('button').addClass('is-loading');

            form.ajaxSubmit({
                url: BASE+'/controller/'+callback+'.ajax.php',
                dataType: 'json',
                // data: data,
                type: 'POST',
                success: function (data) {

                    console.log(data);
                    if(data.clear){
                        form.trigger('reset');
                    }
                    if(data.files){
                        $('.files').prepend(data.files);
                    }
                    if(data.trigger){
                        triggerNotify(data.trigger)
                    }
                    if(!data.success){
                        form.find('button').removeClass('is-loading');
                    }
                    if (data.redirect) {
                        setTimeout(function () {
                            window.location.href = data.redirect;
                        }, 2000);
                    }
                    if (data.content) {
                        if (typeof (data.content) === 'string') {
                            $('.j_content').fadeTo('300', '0.5', function () {
                                $(this).html(data.content).fadeTo('300', '1');
                            });
                        } else if (typeof (data.content) === 'object') {
                            $.each(data.content, function (key, value) {
                                $(key).fadeTo('300', '0.5', function () {
                                    $(this).html(value).fadeTo('300', '1');
                                });
                            });
                        }
                    }
                }
            })
        }

    })

    // MENU

    $(".dash_main_header_menu").click(function () {
        if ($(".dash_nav").css("margin-left") === '-240px') {
            $(".dash_nav").animate({"margin-left": "0"}, 100).removeClass("dash_nav_hide_menu");
        } else {
            $(".dash_nav").animate({"margin-left": "-240px"}, 100).addClass("dash_nav_hide_menu");
        }
    });

    canvasFix('#janela', '.viewGraph');

    // LL

    $('body').on('click', '.btnPreDelete', function(){
        $(this).fadeOut(0);
        var id = $(this).attr("data-delete");
        $('#'+id).css('display', 'inline');
    })

    // $(window).resize(function(){
    //     canvasFix('#janela', '.viewGraph');
    //     console.log('canvasFix called');
    // })

})

function canvasFix(selector, reference){
    var width = $(reference).width();
    var height = $(reference).height();

    $(selector).attr("width", width);
    $(selector).attr("height", height);

}

function triggerNotify(data) {

    // icon | color | title | time

    var triggerContent = "<div class='trigger_notify trigger_notify_" + data.cor + "' style='left: 100%; opacity: 0;'>";
    triggerContent += "<p class='" + data.icone + "'>" + data.mensagem + "</p>";
    triggerContent += "<span class='trigger_notify_timer'></span>";
    triggerContent += "</div>";

    if(!$('.trigger_notify_box').length){
        $('body').prepend("<div class='trigger_notify_box'></div>");
    }

    $('.trigger_notify_box').prepend(triggerContent);
    $('.trigger_notify').stop().animate({'left': '0', 'opacity': '1'}, 200, function(){
        $(this).find('.trigger_notify_timer').animate({'width': '100%'}, data.timer, 'linear', function(){
            $(this).parent('.trigger_notify').animate({'left': '100%', 'opacity': '0'}, function(){
                $(this).remove();
            });
        });
    });

    $('body').on('click', '.trigger_notify', function(){
        $(this).animate({'left': '100%', 'opacity': '0'}, function(){
            $(this).remove();
        });
    });
}

function _atualizaProgressBar(i, total, selector){
    var result = i/total*100;
    console.log()
}
