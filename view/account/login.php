<?php

use App\Account\Login;

Login::redirectIfIsConnected($router->url('dashboard'));

$pageTitle = "Se connecter";
$pageDescription = "Accédez à vos séjours partagés.";

$template->loadScript('js', 'login/show-password', true);

$alert = null;
if(!empty($_POST)) {
   $alert = Login::process($_POST, $router->url('dashboard'));
}

?>
<div class="row mb-4">
    <div class="col col-md-4">
        <?php if($alert): ?>
            <div class="alert alert-danger"><?= $alert ?></div>
        <?php endif ?>
        <form action="" method="post">
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" placeholder="perdo123" required>
                <label>Nom d'utilisateur</label>
            </div>
            <div class="form-floating mb-3">
                <input id="loginPassword" type="password" name="password" class="form-control" placeholder="*****" required>
                <span class="loginEye">
                    <i class="fas fa-eye" id="eyeOpen"></i>
                    <i class="fas fa-eye-slash" id="eyeClosed"></i>
                </span>
                <label>Mot de passe</label>
            </div>
            <button type="submit" class="btn btn-primary">Connexion</a>
        </form>
    </div>
</div>
<p>
    <span>Nouvel utilisateur ? </span>
    <a href="<?= $router->url('add-user') ?>">Créer un compte</a>
</p>
