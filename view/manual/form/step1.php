<?php if(!empty($alerts['persons'])): ?>
    <div class="alert alert-danger small"><?= $alerts['persons'] ?></div>
<?php endif ?>
                    
<form method="post" class="d-flex justify-content-center align-items-center gap-3 mb-5">
    <div class="">
        <b>Nombre de personnes</b>
    </div>
    <div class="">
        <input type="number" name="persons" value="<?= $_POST['persons'] ?? '' ?>" class="form-control">
    </div>
    <div class="">
        <button type="submit" class="btn btn-primary">Valider</button>
    </div>
</form>