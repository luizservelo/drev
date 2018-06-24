<?php

if(!empty($_SESSION['login'])) :

    header("Location: ".BASE);

endif;

?>

<div class="dash_login gradient_green">
    <div class="dash_login_box">
        <div class="dash_login_box_content radius">
            <form class="dash_login_box_content_form basic_form defaultForm" method="POST">
                <input type="hidden" name="callback" value="Login">
                <input type="hidden" name="callback_action" value="doLogin">
                <label>
                    <span class="field icon-envelop">e-mail:</span>
                    <input type="email" name="user_email" placeholder="Seu e-mail:" required autofocus/>
                </label>

                <label>
                    <span class="field icon-key">senha:</span>
                    <input type="password" name="user_password" placeholder="Sua senha:" required/>
                </label>
                <a class="dash_login_box_content_pass icon-user-plus" href="<?= BASE ?>/criar-conta" title="Criar conta">Criar Conta</a>
                <button class="btn btn_blue radius">LOGIN</button>
            </form>
        </div>
        <p class="dash_login_box_footer">Com <span class="icon-heart icon-notext font_red"></span>, <a href="https://luiz.servelo.me" target="_blank">Luiz Servelo!</a></p>
    </div>
</div>
