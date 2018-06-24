<?php

if(!empty($_SESSION['login'])) :
    header('Location: '.BASE);
endif;

?>
<div class="dash_login gradient_blue">
    <div class="dash_login_box">
        <div class="dash_login_box_content radius">
            <form class="dash_login_box_content_form basic_form defaultForm" method="POST">
                <input type="hidden" name="callback" value="Login">
                <input type="hidden" name="callback_action" value="create">
                <label>
                    <span class="field">Nome:</span>
                    <input type="text" name="user_name" placeholder="Seu nome:" required autofocus/>
                </label>

                <label>
                    <span class="field">Sobrenome:</span>
                    <input type="text" name="user_lastname" placeholder="Seu sobrenome:" required/>
                </label>

                <label>
                    <span class="field icon-envelop">e-mail:</span>
                    <input type="email" name="user_email" placeholder="Seu e-mail:" required/>
                </label>

                <label>
                    <span class="field icon-key">senha:</span>
                    <input type="password" name="user_password" placeholder="Sua senha:" required/>
                </label>
                <a class="dash_login_box_content_pass icon-arrow-left" href="<?= BASE ?>" title="Criar conta">Voltar para login</a>
                <button class="btn btn_blue radius">Cadastrar</button>
            </form>
        </div>
        <p class="dash_login_box_footer">Com <span class="icon-heart icon-notext font_red"></span>, <a href="https://luiz.servelo.me" target="_blank">Luiz Servelo!</a></p>
    </div>
</div>
