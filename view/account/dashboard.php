<?php

use App\Account\Login;
use App\Database\Table\ProjectTable;
use App\Helper;

Login::redirectIfIsNotConnected($router->url('login'));

$pageTitle = Helper::greetingsBasedOnTime($_SESSION['UserModel']->getFirstname());
$projectTable = new ProjectTable;
$activeProjectsWithUser = $projectTable->getProjectsWhereUserAppears(false);
$archivedProjectsWithUser = $projectTable->getProjectsWhereUserAppears(true);
$h2Active = count($activeProjectsWithUser) > 1 ? "Séjours en cours" : "Séjour en cours";
$h2Archived = count($archivedProjectsWithUser) > 1 ? "Séjours archivés" : "Séjour archivé";

?>
<!-- active projects -->
<div class="mb-5">
    <div class="d-flex mb-4 justify-content-between align-items-center">
        <h2 class="">Séjours en cours</h2>
        <a href="<?= $router->url('add-project') ?>" class="btn btn-primary">Créer un nouveau séjour</a>
    </div>
    <?php if (!empty($activeProjectsWithUser)) : ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($activeProjectsWithUser as $project) : ?>
                <?php if (in_array($_SESSION['UserModel']->getID(), $projectTable->extractUsers($project))) : ?>
                    <div class="col">
                        <?php require VIEW_PATH . DS . 'project' . DS . 'card.php' ?>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    <?php else : ?>
        <p class="text-muted small">Aucun séjour pour l'instant.<br>Pour qu'un séjour s'affiche ici, vous pouvez créer un nouveau séjour dont vous serez l'auteur, ou demander à l'auteur d'un séjour existant de vous ajouter au sien.</p>
    <?php endif ?>
    <!-- archived projects -->
</div>
<div class="mb-5">
    <div class="d-flex mb-4 justify-content-between align-items-center">
        <h2 class="">Séjours archivés</h2>
    </div>
    <?php if (!empty($archivedProjectsWithUser)) : ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($archivedProjectsWithUser as $project) : ?>
                <?php if (in_array($_SESSION['UserModel']->getID(), $projectTable->extractUsers($project))) : ?>
                    <div class="col">
                        <?php require VIEW_PATH . DS . 'project' . DS . 'card.php' ?>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
        </div>
    <?php else : ?>
        <p class="text-muted small">Aucun séjour archivé pour l'instant. Un séjour peut être archivé ou supprimé uniquement par son auteur.</p>
    <?php endif ?>
</div>