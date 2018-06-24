<?php

$image = !empty($_SESSION['login']['user_thumb']) ? BASE.'/tim.php?src=uploads/'.$_SESSION['login']['user_thumb'].'&h=500&w=500' : BASE.'/tim.php?src=images/no_avatar.jpg&h=500&w=500';

?>
<div class="sendBackground">

    <p>Processando arquivo, por favor aguarde</p>
    <img src="<?= BASE ?>/images/load_w.gif" alt="">
    <h3><span id="percentageTag">0</span>%</h3>
</div>
<nav class="dash_nav">
    <header class="dash_nav_header">
        <img class="dash_nav_header_thumb rounded" src="<?= $image ?>"/>
        <h1 class="dash_nav_header_name"><?= $_SESSION['login']['user_name'] ?> <?= $_SESSION['login']['user_lastname'] ?></h1>
        <p class='dash_nav_header_short icon-dot icon-notext'><?= $_SESSION['login']['user_email'] ?></p>
    </header>
    <ul class="dash_nav_ul">
        <li class="dash_nav_ul_li"><a class="icon-stats-dots dash_nav_ul_li_a" href="<?= BASE ?>" title="Atividades">Processamentos</a></li>
        <li class="dash_nav_ul_li"><a class="icon-plus dash_nav_ul_li_a" href="<?= BASE ?>/novo-processamento" title="Minha conta">Novo processamento</a></li>
        <li class="dash_nav_ul_li"><a class="icon-user dash_nav_ul_li_a" href="<?= BASE ?>/conta" title="Minha conta">Minha conta</a></li>
    </ul>
</nav>
