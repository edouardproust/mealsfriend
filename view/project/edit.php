<?php

use App\Account\Login;
use App\Database\Table\{ParticipantTable, ProjectTable, UserTable};

Login::redirectIfIsNotConnected($router->url('login'));

$pageTitle = "Modifier le séjour";

$projectTable = new ProjectTable;
$project = $projectTable->getOneByID($router->getParams('id'));
$nonUsers = ParticipantTable::getNonUsersOnes($project);

$alert = '';
if(!empty($_POST)) {
    $alert = $projectTable->updateOne($project, $_POST, $router->url('show-project', ['id' => $project->getID(), 'slug' => $projectTable->getSlugFromId($project->getID())]));
}

// Fields values
$titleValue = !empty($_POST) ? $_POST['title'] : $project->getTitle();
$descriptionValue = !empty($_POST) ? $_POST['description'] : $project->getDescription();
?>
<?php if(!empty($alert)): ?>
    <div class="alert alert-danger"><?= $alert ?></div>
<?php endif ?>
<div class="mb-5">
    <form action="" method="post">
        <div class="form-floating mb-3">
            <input type="text" name="title" class="form-control" value="<?= $titleValue ?>" placeholder="Vacances Biarritz 2020">
            <label>Nom du séjour</label>
        </div>
        <div class="form-floating mb-3">
            <input type="text" name="description" class="form-control" value="<?= $descriptionValue ?>" placeholder="Description">
            <label>Description du séjour</label>
        </div>
            <?php if(count($nonUsers) > 0): ?>
                <h5 class="my-4">Remplacer un participant par un utilisateur enregistré</h5>
                <?php $p = 0 ?>
                <?php foreach(ParticipantTable::getNonUsersOnes($project) as $participant): ?>
                    <?php $selectedID = @$_POST['user_id'][$p]; $p++ ?>
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-auto">
                            <label for="participantToUser" class="col-form-label"><?= $participant->getName() ?></label>
                        </div>
                        <div class="col-auto">
                            <select id="participantToUser" name="user_id[]" class="form-control">
                                <option value="">Selectionner un utilisateur</option>
                                <?php foreach((new UserTable)->getAllOtherThanProjectUsers($project->getID()) as $user): ?>
                                    <?php $selected = !empty($selectedID) && $selectedID == $user->getID() ? ' selected' : '' ?>
                                    <option value="<?= $user->getID() ?>"<?= $selected ?>><?= $user->getName() ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</a>
    </form>
</div>