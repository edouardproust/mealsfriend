<?php

use App\Database\Table\ParticipantTable;

$participants = (new ParticipantTable)->getAllByProjectId($router->getParams('id'));

$spent_at = @$expense ? $expense->getSpentAt('Y-m-d') : date('Y-m-d');
$amount = @$expense ? $expense->getAmount(false): '';
$notes = @$expense ? $expense->getNotes() : '';
$validButtonText = @$expense ? 'Modifier': 'Ajouter';

?>
<form action="" method="post">
<div class="row mb-4">
<div class="col col-md-4">

    <div class="form-floating mb-3">
        <input type="date" name="spent_at" value="<?= $spent_at ?>" class="form-control" placeholder="date" required>
        <label>Date</label>
    </div>
    <div class="form-floating mb-3">
        <select name="participant_id" class="form-control" required>
            <?php foreach ($participants as $participant): 
                $isPayeer = @$expense && (int)$expense->getParticipantID() === $participant->getID();
                $isCurrentUser = $_SESSION['UserModel']->getID() === $participant->getUserID();
                $selected = $isPayeer || $isCurrentUser ? 'selected' : '' ?>
                <option value="<?= $participant->getID() ?>" <?= $selected ?>>
                    <?= $participant->getName() ?>
                </option>
            <?php endforeach ?>
        </select>
        <label>Payeur</label>
    </div>
    <div class="form-floating mb-3">
        <input type="number"  name="amount" value="<?= $amount ?>" step="0.01" class="form-control" placeholder="19.99" required>
        <label>Montant</label>
        <div class="form-text">Entrer le montant dépensé en <?= SITE_CURRENCY ?></div>
    </div>
    <div class="form-floating mb-3">
        <input type="text" name="notes" value="<?= $notes ?>" class="form-control" placeholder="Ajouter une note">
        <label>Note (optionnel)</label>
        <div class="form-text">Indications supplémentaires</div>
    </div>
    <button type="submit" class="btn btn-primary"><?= $validButtonText ?></a>

</div>
</div>
</form>