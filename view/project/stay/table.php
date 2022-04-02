<?php

use App\Alert;
use App\Model\StayModel;

// Save form into database
if (!empty($_POST)) {
    foreach ($stayTable->getIdsByProject($router->getParams('id')) as $stayID) {
        $stayTable->updateOnebyID($_POST, $stayID);
    }
    $section = '#stays';
    header('Location:' . $router->url('show-project', ['id' => $router->getParams('id'), 'slug' => $router->getParams('slug')]) . $section);
    exit;
}

// Form fields' values
foreach ($stays as $stay) {
    $i = $stay->getID();
    $dateFormat = 'Y-m-d';
    $startDate[$i] = !empty($stay->getStartDate()) ? (new DateTime($stay->getStartDate()))->format($dateFormat) : null;
    $endDate[$i] = !empty($stay->getEndDate()) ? (new DateTime($stay->getEndDate()))->format($dateFormat) : null;
    $skippedMeals[$i] = $stay->getSkippedMeals();
    $skippedMealsNotes[$i] = $stay->getSkippedMealsNotes();
    $checked['no_breakfasts'][$i] = StayModel::getChecked($stay, 'no_breakfasts');
    $checked['no_snacks'][$i] = StayModel::getChecked($stay, 'no_snacks');
    $checked['is_kid'][$i] = StayModel::getChecked($stay, 'is_kid');
}

?>
<!-- table -->
<div class="table-responsive">
    <table class="table table-striped text-center">
        <thead>
            <tr valign="middle">
                <th style="width:20%" scope="col">Participant</th>
                <th style="width:15%" scope="col">Date d'arrivée (*)</th>
                <th style="width:15%" scope="col">Date de départ (*)</th>
                <th style="width:30%" colspan="2">Repas sautés</th>
                <th style="width:20%" scope="col">Options</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stays as $stay) : $i = $stay->getID() ?>
                <tr valign="middle">
                    <td>
                        <?= $stay->getName() ?>
                    </td>
                    <td>
                        <input type="date" name="start_date[<?= $i ?>]" class="form-control" value="<?= $startDate[$i] ?>">
                    </td>
                    <td>
                        <input type="date" name="end_date[<?= $i ?>]" value="<?= $endDate[$i] ?>" class="form-control">
                    </td>
                    <td style="width:8%" class="pe-1">
                        <label class="small text-muted">Nombre</label>
                        <input type="number" name="skipped_meals[<?= $i ?>]" value="<?= $skippedMeals[$i] ?>" id="skipped_meals" class="form-control">
                    </td>
                    <td style="width:22%" class="ps-0">
                        <label class="small text-muted">Précisions</label>
                        <input type="text" name="skipped_meals_notes[<?= $i ?>]" value="<?= $skippedMealsNotes[$i] ?>" id="skipped_meals_notes" class="form-control">
                    </td>
                    <td style="text-align:left">
                        <div>
                            <label class="small text-muted">
                                <input type="checkbox" name="no_breakfasts[<?= $i ?>]" <?= $checked['no_breakfasts'][$i] ?> id="no_breakfasts" class="form-check-input">
                                Pas de petit-déjeuners
                            </label>
                        </div>
                        <div>
                            <label class="small text-muted">
                                <input type="checkbox" name="no_snacks[<?= $i ?>]" <?= $checked['no_snacks'][$i] ?> id="no_snacks" class="form-check-input">
                                Pas de goûters
                            </label>
                        </div>
                        <div>
                            <label class="small text-muted">
                                <input type="checkbox" name="is_kid[<?= $i ?>]" <?= $checked['is_kid'][$i] ?> id="is_kid" class="form-check-input">
                                Demie-portion
                            </label>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>