<?php

use App\Alert;
use App\Calculation\CalculationManual;

$pageTitle = "Saisie manuelle";
$pageDescription = "Comptes avec entrée manuelle des dépenses et des repas consommés par chacun.";

$persons = $result = null;
$alerts = [];

if(!empty($_POST)) {
    $persons = (int)$_POST['persons'];
    if (@$_POST['step2']) {
        $result = (new CalculationManual($persons))->getResult();
        $alerts = $result->getAlerts();
    }
    // alerts
    if($persons !== null && $persons < 2) {
        $alerts[] = new Alert('Le nombre de personnes doit être supérieur à 2', 'danger', 'persons');
    }
}

?>
<!-- Result -->
<?php if($alerts): ?>
    <?= Alert::showAll($alerts) ?>
<?php endif ?>
<?php if ($result && !Alert::findByName('no-refund', $alerts) ): ?>
    <?php require TEMPLATE_PATH . '/result.php' ?>
    <?php require 'form/step2.php' ?>
<?php else: ?>
    <?php if( !$persons || Alert::findByName('persons', $alerts) ): ?>
        <?php require 'form/step1.php' ?>
    <?php else: ?>
        <?php require 'form/step2.php' ?>
    <?php endif ?>
<?php endif ?>
