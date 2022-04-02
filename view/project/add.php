<?php

use App\Account\Login;
use App\Database\Table\{ParticipantTable, ProjectTable, StayTable, UserTable};

Login::redirectIfIsNotConnected($router->url('login'));

$pageTitle = "Créer un nouveau séjour";
$pageDescription = "Remplir les champs ci-dessous.";
$template->loadScript('js', 'form/add-project-select-participants');

if (!empty($_POST)) {
    $inputNames = $_POST['participants'];
    $alert = '';
    if (count($inputNames) !== count(array_unique($inputNames))) { // check if no duplicate participants selected
        $alert .= 'Vous avez sélectionné un même participant 2 fois. ';
    }
    Login::sessionStart();
    if (!in_array($_SESSION['UserModel']->getName(), $inputNames)) { // check if current user has been selected
        if (!empty($alert)) $alert .= '<br>';
        $alert .= 'Vous devez faire partie des participants';
    }
    if (empty($alert)) {
        $projectTable = new ProjectTable;
        $projectID = $projectTable->createOne($_POST); // create new ProjectModel
        (new ParticipantTable)->addIfNotUserAlready($projectID, $inputNames); // populate ProjectModel with ParticipantModel[] only if not already a UserModel fullname
        (new StayTable)->createAllForNewProject($projectTable->getOneById($projectID)); // Create all blank stays for each Participant
        $redirectUrl = $router->url('show-project', ['id' => $projectID, 'slug' => $projectTable->getSlugFromId($projectID)]);
        header('Location:' . $redirectUrl);
        exit;
    }
}

?>
<div class="mb-5">
    <!-- Form part 1 (GET) -->
    <?php if (empty($_GET)) : ?>
        <h4 class="mb-3">Partie 1/2</h4>
        <form action="" method="GET">
            <div class="">
                <div class="form-floating mb-3">
                    <select id="selectParticipantsListener" name="participants" class="form-control" placeholder="5" required>
                        <?php for ($i = 2; $i <= 12; $i++) : ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor ?>
                    </select>
                    <label>Nombre de participants</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Valider</a>
        </form>
        <!-- Form part 2 (POST) -->
    <?php else : ?>
        <h4 class="mb-3">Partie 2/2</h4>
        <form action="" method="post">
            <div class="form-floating mb-3">
                <input type="text" name="title" class="form-control" value="<?= $_POST['title'] ?? '' ?>" placeholder="Vacances Biarritz 2020" required>
                <label>Nom du séjour</label>
            </div>
            <?php if (!empty($alert)) : ?>
                <div class="alert alert-danger"><?= $alert ?></div>
            <?php endif ?>
            <?php for ($i = 1; $i <= (int)$_GET['participants']; $i++) : ?>
                <div id="selectParticipants" class="form-floating mb-3">
                    <input type="text" name="participants[]" list="allUsers" value="<?= $_POST['participants'][$i - 1] ?? '' ?>" class="form-control " placeholder="Choose one" required>
                    <datalist id="allUsers">
                        <?php foreach ((new UserTable)->getAll('firstname, lastname') as $user) : ?>
                            <option value="<?= $user->getName() ?>">
                            <?php endforeach ?>
                    </datalist>
                    <label>Nom du participant <?= $i ?></label>
                </div>
            <?php endfor ?>
            <!-- Form part 3/3 (optionnal) -->
            <div class="form-floating mb-3">
                <input type="text" name="description" class="form-control" value="<?= $_POST['description'] ?? '' ?>" placeholder="Notez quelque chose ici pour que tout le monde comprenne que de quoi il s'agit!">
                <label>Description du séjour (optionnel)</label>
            </div>
            <!-- submit -->
            <button type="submit" class="btn btn-primary">Créer le séjour</a>
        </form>
    <?php endif ?>
</div>