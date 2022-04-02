<?php

use App\Account\Login;
use App\Alert;

Login::redirectIfIsNotConnected($router->url('login'));
$me = $_SESSION['UserModel'];

$pageTitle = "Mon compte";

// test envoi email

$id = 0;
if (!empty($_GET['email-test'])) {
    $id = random_int(1, 1000);
    $to = $me->getEmail();
    $subject = "Test d'envoi email (ID " . $id . ")";
    $message = "<h1>Tout fonctionne!</h1>\r\n";
    $message .= "Ceci est un test permettant de vérifier que vous recevez bien les emails en provenance de Mealsfriend.com\r\n";
    $message .= "ID de ce message: " . $id;
    $headers = "From: mealsfriend.com <contact@mealsfriend.com>\r\n";
    $headers .= "Reply-to: contact@mealsfriend.com\r\n";
    $headers .= "Content-type: text/html\r\n";
    $sent = mail($to, $subject, $message, $headers);
    if ($sent) {
        $alerts[] = new Alert("Email de test envoyé à " . $me->getEmail() . " (ID " . $id . ").<div class='small'>Si vous n'avez rien reçu dans 5 minutes, veuillez vérifier votre dossier spams. Si votre dossier spam est vide, alors une erreur a eu lieu au niveau du serveur: merci de contacter l'administrateur du site.</div>", "success");
    } else {
        $alerts[] = new Alert("L'envoi de l'email de test vers " . $me->getEmail() . " a échoué.<div class='small'>Ce serveur n'est pas configuré pour envoyer des emails. Merci de contacter l'administrateur sur site.</div>", "danger");
    }
}

?>
<?php if (@$alerts) : ?>
    <?= Alert::showAll($alerts) ?>
<?php endif ?>

<ul class="list-unstyled">
    <li>
        <h5><?= $me->getName() ?></h5>
    </li>
    <li>Identifiant: <b><?= $me->getUsername() ?></b></li>
    <li><?= $me->getEmail() ?></li>
    <li>Inscrit le <?= $me->getCreatedAt('d/m/Y, \à H\hi') ?></li>
    <li>ID utilisateur: #<?= $_SESSION['UserModel']->getID() ?></li>
</ul>
<div class="mb-5">
    <div class="mb-2">
        <a href="<?= $router->url('edit-user') ?>" class="btn btn-outline-primary">Modifier mes informations</a>
    </div>
    <div>
        <a href="?email-test=1" class="btn btn-outline-primary">Tester la réception des emails</a>
    </div>
</div>
<div class="mb-3">
    <a href="<?= $router->url('logout') ?>" class="btn btn-primary">Se déconnecter</a>
</div>
<div>
    <a href="<?= $router->url('delete-user') ?>" class="btn btn-danger">Supprimer le compte</a>
    <span class="ms-2 text-danger small">Attention, cette action est irréversible!</span>
</div>