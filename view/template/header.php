<?php
use App\Account\Login;

$pageTitle = $pageTitle ?? SITE_NAME;
$isAdmin = Login::isAdmin();

// Settings variables
    // Colors
    $darkBg = SITE_COLOR_BG === 'dark';
    $textMuted = $darkBg ? 'text-muted' : '';

?>
<!DOCTYPE html>
<html lang="en" class="h-100">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="shortcut icon" type="image/jpg" href="/img/logo/<?= SITE_FAVICON ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Google fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Lato&family=Open+Sans&display=swap" rel="stylesheet">
        
        <title><?= $pageTitle ?></title>
        <?php $template->executeScripts('header') ?>
    </head>

    <body class="d-flex flex-column h-100">

        <header class="p-3 bg-<?= SITE_COLOR_BG ?> text-white">
            <div class="container">
                <ul class="nav d-flex flex-wrap align-items-center justify-content-md-between justify-content-center list-unstyled">
                    <div class="d-flex flex-wrap justify-content-center align-items-center">
                        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                            <?php $template->getLogo() ?>
                        </a>
                        <?php foreach($templateVars->getMenuHeaderMain() as $linkTitle => $routerLinkName): ?>
                            <li><a href="<?= $router->url($routerLinkName) ?>" class="nav-link text-white <?= $routerLinkName === @$router->getMatch()['name'] ? 'active': '' ?>"><?= $linkTitle ?></a></li>
                        <?php endforeach ?>
                    </div>
                    <?php foreach($templateVars->getMenuHeaderSecondary() as $linkTitle => $routerLinkName): ?>
                        <li><a href="<?= $router->url($routerLinkName) ?>" class="nav-link text-white <?= $routerLinkName === @$router->getMatch()['name'] ? 'active': '' ?>"><?= $linkTitle ?></a></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </header>