<?php
ob_start();
session_start();

require './model/Config.inc.php';

$Read = new Read;

//TRATAMENTO DE ROTAS
$getURL = strip_tags(trim(filter_input(INPUT_GET, 'url', FILTER_DEFAULT)));
$setURL = (empty($getURL) ? 'index' : $getURL);
$URL = explode('/', $setURL);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title><?= APP_TITLE ?></title>
        <link rel="base" href="<?= BASE ?>">
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1.0,user-scalable=0">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,400,700">
        <?php

        // GET CSS

        if (file_exists("view/assets/css")):
            foreach (scandir("view/assets/css") as $cssFile) :
                if (file_exists("view/assets/css/{$cssFile}") && !is_dir("view/assets/css/{$cssFile}") && pathinfo("view/assets/css/{$cssFile}")['extension'] == 'css'):
                    echo '<link rel="stylesheet" href="'.BASE.'/view/assets/css/' . $cssFile . '"/>';
                endif;
            endforeach;
        endif;

        // GET JS

        if (file_exists("view/assets/js")):
            foreach (scandir("view/assets/js") as $jsFile):
                if (file_exists("view/assets/js/{$jsFile}") && !is_dir("view/assets/js/{$jsFile}") && pathinfo("view/assets/js/{$jsFile}")['extension'] == 'js'):
                    echo '<script src="'.BASE.'/view/assets/js/' . $jsFile . '"></script>';
                endif;
            endforeach;
        endif;

        ?>

    </head>
    <body>
        <div class="dash">
            <?php
            if(file_exists("view/{$URL[0]}.php")) :

                if($URL[0] != 'login' && $URL[0] != 'criar-conta') :
                    require "view/inc/menu.php";
                endif;

                require "view/{$URL[0]}.php";

            else :
                header("Location: ".BASE."/404");
            endif;

            ?>
        </div>
    </body>
</html>

<?php

if (!file_exists('.htaccess')):
    $htaccesswrite = "RewriteEngine On\r\nOptions All -Indexes\r\n\r\n# WC WWW Redirect.\r\n#RewriteCond %{HTTP_HOST} !^www\. [NC]\r\n#RewriteRule ^ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\r\n\r\n# WC HTTPS Redirect\r\nRewriteCond %{HTTP:X-Forwarded-Proto} !https\r\nRewriteCond %{HTTPS} off\r\nRewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\r\n\r\n# WC URL Rewrite\r\nRewriteCond %{SCRIPT_FILENAME} !-f\r\nRewriteCond %{SCRIPT_FILENAME} !-d\r\nRewriteRule ^(.*)$ index.php?url=$1\r\n\r\nphp_value memory_limit 500M\r\nphp_value post_max_size 500M\r\nphp_value upload_max_filesize 500M";
    $htaccess = fopen('.htaccess', "w");
    fwrite($htaccess, str_replace("'", '"', $htaccesswrite));
    fclose($htaccess);
endif;

ob_end_flush();
