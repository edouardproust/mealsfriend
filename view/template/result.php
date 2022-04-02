<?php

use App\Field;
use App\Modal\FormModal;

?>
<div class="mb-5" id="result">
    <div class="alert alert-primary">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Résultats</h2>
            <div>
                <a type="button" href="?export-file=1" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-file-download"></i>
                    <span class="ms-2">Télécharger</span>
                </a>
                <?php if(isset($_SESSION['UserModel'])): ?>
                    <?php (new FormModal(
                        'send-by-email',
                        [
                            new Field('recipients', 'text', true, 'Destinataires', '', 'Entrez les emails de chaque destinataires, séparés par une virgule (,).'),
                            new Field('my-email', 'email', true, 'Votre email', $_SESSION['UserModel']->getEmail()),
                            new Field('my-name', 'text', true, 'Votre nom complet', $_SESSION['UserModel']->getName()),
                            new Field('subject', 'text', true, 'Objet de l\'email', 'Le calcul des repas est prêt!'),
                            new Field('message', 'textarea', true, 'Votre message', 'Bonjour, voici les résultats du calcul des repas pour le séjour « '.$project->getTitle().'». Il faut que nous fassions les remboursements rapidement! Voici l\'adresse du projet: '.$_SERVER['HTTP_REFERER']),
                        ],
                        '?test=1', // a changer lorsque systeme d'envoi email relié
                        'Envoyer par email',
                        'Tous les champs sont obligatoires. Vos destinataires recevront le résultat du calcul pour ce séjour en pièce jointe (format PDF).'
                    ))->showTrigger(
                        '<i class="fas fa-paper-plane"></i><span class="ms-2">Envoyer par email</span>', 
                        'button', 
                        'btn btn-outline-primary') ?>
                <?php endif ?>
            </div>
        </div>
        <hr>
        <ul>
            <li>Total repas consommés: <?= round($result->getTotalMeals(),2) ?> repas</li>
            <li>Total dépensé: <?= $result->getTotalSpent() ?>€</li>
            <li>Prix moyen d'un repas: <?= round($result->getAverageMealPrice(), 2) ?>€</li>
        </ul>
        <h4>Balances de chacun avant remboursement:</h4>
        <?= $result->getHtmlBalancesBefore() ?>
        <h4>Etapes de remboursement</h4>
        <?= $result->getHtmlResultSteps() ?? '<p>Pas d\'étapes de remboursement.</p>' ?>
    </div>
</div>