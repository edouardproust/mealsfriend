<?php

use App\Account\Login;
use App\Alert;
use App\Database\Table\{ExpenseTable, ProjectTable, StayTable};
use App\Modal\{ConfirmModal, InfoModal};
use App\Calculation\{Calculation, ResultExport};


// Redirections
// is not connected 
Login::redirectIfIsNotConnected($router->url('login'));
$projectTable = new ProjectTable;
// is a not a project participant
$projectTable::redirectIfNotAParticipant($router->getParams('id'), $router->url('dashboard'));
// $_GET['slug'] param doesn't match ProjectModel's slug
$project = $projectTable->getOneById($router->getParams('id')); // Get project data
//dd($router->getParams('slug'), $project->getSlug());
if ($router->getParams('slug') !== $project->getSlug()) {
    $redirectUrl = $router->url('show-project', ['id' => $project->getID(), 'slug' => $project->getSlug()]);
    header('Location:' . $redirectUrl);
    exit;
}
// Get expenses list
$expenses = (new ExpenseTable)->getAllByProjectId($project->getID());
// Get stays list
$stayTable = new StayTable;
$stays = $stayTable->getAllByProjectId($project->getID());
// View
$pageTitle = $project->getTitle();

// Get result
$result = (new Calculation($stays, $expenses))->getResult();
// Get alerts
$resultAlerts = $result->getAlerts();

?>
<!-- PROJECT HEADER -->

<div class="mb-5">
    <h1><?= $pageTitle ?></h1>
    <p class="lead mb-2"><?= $project->getDescription() ?></p>
    <div class="d-flex small align-items-center gap-3">
        <div class="text-muted">Créé par <?= $project->getAuthorName() ?> le <?= $project->getCreatedAt("d/m/Y") ?></div>
        <?php if ($project->getAuthorID() === $_SESSION['UserModel']->getID()) : // Only author can edit or delete 
        ?>
            <div class="project-icons">
                <a href="<?= $router->url('edit-project', ['id' => $project->getID()]) ?>" class="text-primary"><i class="project-icon-edit fas fa-pencil-alt"></i></a>
                <?php if ($project->getArchived()) : ?>
                    <a href="<?= $router->url('archive-project', ['id' => $project->getID()]) ?>" class="text-secondary"><i class="project-icon-unarchive fas fa-folder-minus"></i></a>
                <?php else : ?>
                    <a href="<?= $router->url('archive-project', ['id' => $project->getID()]) ?>" class="text-secondary"><i class="project-icon-archive fas fa-folder-plus"></i></a>
                <?php endif ?>
                <?php (new ConfirmModal('delete-project', $router->url('delete-project', ['id' => $project->getID()])))
                    ->showTrigger('<i class="project-icon-delete fas fa-trash"></i>', 'link', 'text-danger') ?>
            </div>
        <?php endif ?>
    </div>
</div>

<!-- RESULT -->

<?php if (!empty($result) && empty($resultAlerts)) : ?>
    <div class="mb-5" id="result">
        <?php if (!empty($_GET['export-file'])) : ?>
            <?php $exportAlert = (new ResultExport($project, $result, $expenses, $stays))->export() ?>
        <?php endif ?>
        <?php if (isset($exportAlert)) : ?>
            <?= $exportAlert->show() ?>
        <?php endif ?>
        <?php require TEMPLATE_PATH . '/result.php' ?>
    </div>
<?php endif ?>

<!-- EXPENSES -->

<!-- title -->
<div class="mb-5">
    <div class="d-flex mb-4 justify-content-between align-items-center">
        <h2>Listes des dépenses</h2>
        <?php $btnUrl = $router->url('add-expense', ['slug' => $project->getSlug(), 'id' => $project->getID()]) ?>
        <a href="<?= $btnUrl ?>" class="btn btn-primary">Ajouter une dépense</a>
    </div>
</div>
<!-- table -->
<?php require 'expense/table.php' ?>

<!-- STAYS -->

<div class="mt-5">
    <form action="" method="post">
        <!-- title -->
        <div class="d-flex mb-4 justify-content-between align-items-center">
            <h2 id="stays">Dates de départ et d'arrivée</h2>
            <div>
                <?php ob_start() ?>
                <div class="small text-muted mb-3">
                    Entrez les dates dans le tableau pour chaque personne, puis cliquez sur le bouton "Enregistrer les dates". Les colonnes marquées d'un (*) doivent être entièrement remplies pour que le calcul puisse être réalisé.
                </div>
                <ul class="text-muted small">
                    <li><b>Le calcul s'effectue en temps réel dès lors que les dates de départ et d'arrivée de chacun ont été remplies.</b> Vous pouvez par la suite changer les options pour affiner le calcul, ou bien les dates si vous vous êtes trompé(e).</li>
                    <li><b>Par défaut, une journée complète comprend 1 petit-dejeuner, 1 déjeuner, 1 goûter et 1 dîner.</b> Les petit-déjeuner et goûters comptent pour 1/3 du prix d'un repas normal (déjeuner et dîner).</li>
                    <li><b>Le calculateur compte par défaut une pension complète pour les jours d'arrivée et de départ.</b> Si un participant arrive l'après-midi par exemple (il a donc sauté le repas de midi), entrez "1" dans le champs "Repas sautés" afin de signifier que le repas de midi n'a pas été consommé. (Dans ce cas, le petit-dejeuner devra être compté malgré tout car il n'est pas possible d'entrer des nombres décimaux dans le champs "Repas sautés".)</li>
                    <li><b>L'option "demie-portion" correspond bien aux enfants</b>, car ils mangent en général moins que les adultes. L'option divise par 2 le prix à payer. C'est à vous de décider si l'enfant est suffisamment jeune pour entrer dans cette catégorie ou non.</li>
                    <li><b>Invitation d'amis:</b> Si un participant souhaite inviter des amis, il peut décider de prendre les repas consommés par ces derniers à sa charge. Dans ce cas, il faut soustraire le nombre de repas consommés par les amis au nombre de "repas sautés" par ce participant. Le chiffre qui résulte de cette soustraction peut être positif ou négatif.</li>
                </ul>
                <?php $modalContent = ob_get_clean() ?>
                <?php (new InfoModal('stays-infos', 'Mode d\'emploi', $modalContent))
                    ->showTrigger('<i class="fas fa-question-circle fa-lg"></i>', 'button', 'btn-icon help btn btn-outline-primary') ?>
                <button type="submit" class="btn btn-primary">Enregistrer les dates</button>
            </div>
        </div>
        <!-- infos & alerts -->
        <?php if (!empty($resultAlerts)) : ?>
            <?= Alert::showAll($resultAlerts) ?>
        <?php endif ?>
        <!-- table -->
        <?php require 'stay/table.php' ?>
    </form>
</div>