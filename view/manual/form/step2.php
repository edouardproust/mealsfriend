<form method="post" class="mb-5">
        <?php for($i = 0; $i < $persons; $i++): ?>
            <input type="hidden" name="step2" value="1">
            <input type="hidden" name="persons" value="<?= $_POST['persons'] ?? '' ?>">
            <div class="row border p-2 m-0 mb-3 align-items-center">
                <div class="col col-md-3">
                    <b>Personne n°<?= $i+1 ?></b>
                </div>
                <div class="col col-md-3">
                    <label class="form-label small text-muted">Prénom</label>
                    <input type="text" name="firstname[<?= $i ?>]" placeholder="Jean" value="<?= $_POST['firstname'][$i] ?? '' ?>" class="form-control mb-3">
                </div>
                <div class="col col-md-3">      
                    <label class="form-label small text-muted">Total repas consommés</label>     
                    <?php if(!empty($alerts['meals'][$i])): ?>
                        <div class="alert alert-danger small"><?= $alerts['meals'][$i] ?></div>
                    <?php endif ?>
                    <input type="number" name="meals[<?= $i ?>]" placeholder="23" value="<?= $_POST['meals'][$i] ?? '' ?>" class="form-control mb-3">
                </div>
                <div class="col col-md-3">
                    <label class="form-label small text-muted">Total dépensé (€)</label>
                    <?php if(!empty($alerts['spent'][$i])): ?>
                        <div class="alert alert-danger small"><?= $alerts['spent'][$i] ?></div>
                    <?php endif ?>
                    <input type="number" step="0.01" name="spent[<?= $i ?>]" placeholder="200" value="<?= $_POST['spent'][$i] ?? '' ?>" class="form-control mb-3">
                </div>
            </div>

        <?php endfor ?>
        <div class="d-flex justify-content-between">
            <a href="<?= $router->url('manual') ?>" class="btn btn-outline-primary ">← Retour</a>
            <button type="submit" class="btn btn-primary">Calculer →</button>
        </div>
    </form>