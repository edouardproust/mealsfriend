<?php
$template->loadScript('js', 'login/show-password', true);

?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php elseif(!empty($alert)): ?> 
    <div class="alert alert-danger"><?= $alert ?></div>
<?php endif ?>
<form action="" method="post">
    <div class="row mb-4">
        <div class="col col-md-4">
            <div class="form-floating mb-3">
                <input type="text" name="firstname" value="<?= $firstnameValue ?>" class="form-control" placeholder="Pedro" required>
                <label>Prénom</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="lastname" value="<?= $lastnameValue ?>" class="form-control" placeholder="De la Vega" required>
                <label>Nom</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" name="username" value="<?= $usernameValue ?>" class="form-control" placeholder="pedro123" required>
                <label>Nom d'utilisateur</label>
                <div class="form-text">Utilisé pour vous connecter à votre compte</div>
            </div>
            <div class="form-floating mb-3">
                <input id="loginPassword" type="password" name="password" value="<?= $passwordValue ?>" class="form-control" $passwordRequired>
                <label>Nouveau mot de passe</label>
                <span class="loginEye">
                    <i class="fas fa-eye" id="eyeOpen"></i>
                    <i class="fas fa-eye-slash" id="eyeClosed"></i>
                </span>
                <div class="form-text">Ne le perdez pas! (un système de récupération de mot de passe par email est en cours de réalisation)</div>
            </div>
            <div class="form-floating mb-3">
                <input type="email" name="email" value="<?= $emailValue ?>" class="form-control" placeholder="pedroelfamoso@email.com" required>
                <label>Email</label>
                <div class="form-text">Pour récupérer le mot de passe oublié et plus (à venir)</div>
            </div>
            <button type="submit" class="btn btn-primary"><?= $submitBtnText ?></a>
        </div>
    </div>
</form>