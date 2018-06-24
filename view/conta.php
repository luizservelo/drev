
<?php

if(empty($_SESSION['login'])) :
    header('Location: '.BASE.'/login');
endif;

?>

<?php
$image = !empty($_SESSION['login']['user_thumb']) ? BASE.'/tim.php?src=uploads/'.$_SESSION['login']['user_thumb'].'&h=500&w=500' : BASE.'/tim.php?src=images/no_avatar.jpg&h=500&w=500';
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
        <section class="dash_profile" style="margin: 0px;">
            <article class="dash_profile_section">
                <header class="dash_profile_section_header">
                    <h3>Minha Foto:</h3>
                    <p>Esta é sua foto de perfil na plataforma.</p>
                </header>
                <div class="dash_profile_section_content dash_profile_section_content_photo">
                    <div class="dash_profile_section_content_photo_img"><img class="rounded user_thumb" src="<?= $image ?>" alt="Minha Foto" title="Minha Foto"></div>
                    <div class="dash_profile_section_content_photo_env">
                        <form method="POST" class="defaultForm" enctype="multipart/form-data">
                            <input type="hidden" name="callback" value="Login">
                            <input type="hidden" name="callback_action" value="sendImage">
                            <label>
                                <span class="dash_profile_section_content_photo_env_link">Selecionar Minha Foto</span>
                                <input type="file" name="user_thumb" class="user_thumb_avatar wc_loadimage">
                            </label>
                            <button class='btn btn_blue radius icon-image btn_small' style='width: auto; padding: 8px 17px; margin-top: 10px;'>ENVIAR IMAGEM</button>
                        </form>
                        <p>Uma imagem de 500x500px em arquivo JPG ou PNG.</p>
                    </div>
                </div>
            </article>

            <form  method="post" action="" class="defaultForm" enctype="multipart/form-data">
                <input type="hidden" name="callback" value="Login">
                <input type="hidden" name="callback_action" value="manage">
                <article class="dash_profile_section">
                    <header class="dash_profile_section_header">
                        <h3>Meus dados:</h3>
                    </header>

                    <div class="dash_profile_section_form">
                        <label>
                            <span>Primeiro nome:</span>
                            <input type="text" name="user_name" value="<?= $_SESSION['login']['user_name'] ?>" placeholder="Seu primeiro nome:" required="">
                        </label>

                        <label>
                            <span>Sobrenome:</span>
                            <input type="text" name="user_lastname" value="<?= $_SESSION['login']['user_lastname'] ?>" placeholder="Seu sobrenome:" required="">
                        </label>
                    </div>
                </article>

                <article class="dash_profile_section">
                    <header class="dash_profile_section_header">
                        <h3>Minha senha:</h3>
                        <p>Para modificar sua senha, informe sua senha atual e também sua nova senha.</p>
                    </header>

                    <div class="dash_profile_section_form">
                        <label>
                            <span>Senha atual:</span>
                            <input class="" type="password" name="user_password" value="" placeholder="Sua senha atual:">
                        </label>

                        <label>
                            <span>Nova senha:</span>
                            <input class="" type="password" name="user_newpassword" placeholder="Sua nova senha: ">
                        </label>
                    </div>
                </article>

                <div class="dash_profile_section_form_save">
                    <button class="btn btn_blue radius">SALVAR MEUS DADOS</button>
                </div>
            </form>
        </section>
    </section>
</main>
