<?php

$paths = App::getPaths();
$pageTitle = $pageTitle ?: $structure->title;

echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>'.$pageTitle.' - '.tr('OpenSTAManager').'</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <meta name="robots" content="noindex,nofollow">

		<link href="'.$paths['img'].'/favicon.png" rel="icon" type="image/x-icon" />';

// CSS
foreach (App::getAssets()['css'] as $style) {
    echo '
        <link rel="stylesheet" type="text/css" media="all" href="'.$style.'"/>';
}

// Print CSS
foreach (App::getAssets()['print'] as $style) {
    echo '
        <link rel="stylesheet" type="text/css" media="print" href="'.$style.'"/>';
}

// JS
foreach (App::getAssets()['js'] as $js) {
    echo '
        <script type="text/javascript" charset="utf-8" src="'.$js.'"></script>';
}

// Impostazioni di default per gli alert
echo '
        <script>
            swal.setDefaults({
                buttonsStyling: false,
                confirmButtonClass: "btn btn-lg btn-primary",
                cancelButtonClass: "btn btn-lg",
                cancelButtonText: "'.tr('Annulla').'",
            });

            globals = {
                rootdir: \''.$rootdir.'\',
                locale: \''.$lang.'\',
                full_locale: \''.$lang.'_'.strtoupper($lang).'\',
                translations: {}
            };
        </script>';

echo '

    </head>

    <body class="skin-'.$theme.' '.$body_class.'">
        <!-- Loader principale -->
        <div id="main_loading">
            <div>
                <i class="fa fa-cog fa-spin text-danger"></i>
            </div>
        </div>

        <!-- Loader secondario -->
        <div id="mini-loader" style="display:none;">
            <div></div>
        </div>

        <!-- Loader senza overlay -->
        <div id="tiny-loader" style="display:none;"></div>';
