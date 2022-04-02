<?php namespace App\Calculation;

use App\Alert;
use App\Model\ProjectModel;
use App\Model\ResultModel;
use Mpdf\MpdfException;

class ResultExport {

    /** @var ProjectModel */
    private $project;
    /** @var ResultModel */
    private $result;
    /** @var Expense[] */
    private $expenses;
    /** @var Stay[] */
    private $stays;
    /** @var string The formatted document to be exported, containing all the needed data */
    private $documentToExport;

    public function __construct(ProjectModel $project, ResultModel $result, array $expenses, array $stays)
    {
        $this->project = $project;
        $this->result = $result;
        $this->expenses = $expenses;
        $this->stays = $stays;
        $this->formatDocument();
        $this->export();
    }

    private function formatDocument(): void
    {
        $p = $this->project;
        $r = $this->result;
        $e = $this->expenses;
        $s = $this->stays;

        $data = '<h1>Séjour « ' . $p->getTitle() . ' »</h1>';
        $data .= '<p style="font-size:12px">Avec ' . $p->getParticipantsListHtml() . ' | Créé le ' . $p->getCreatedAt('d/m/Y') . ' par ' . $p->getAuthorName() . '</p>';
        $data .= '<hr>';
        $data .= '<h2>Etapes de remboursement</h2>';
        $data .= $r->getHtmlResultSteps();
        $data .= '<h2>Balance de chacun avant Remboursement</h2>';
        $data .= $r->getHtmlBalancesBefore();
        $data .= '<h2>Données générales</h2>';
        $data .= $this->getGlobalData();
        $data .= '<h2>Dates des séjours & options</h2>';
        $data .= $this->getStaysTableHtml();
        $data .= '<h2>Liste des dépenses</h2>';
        $data .= $this->getExpensesTableHtml();
        $this->documentToExport = $data;
    }
    
    /**
     * Export PDF file
     *
     * @return none|Alert
     */
    public function export()
    {
        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($this->documentToExport);
            $mpdf->Output("results.pdf", "D");
        } catch (\Mpdf\MpdfException $e) {
            return new Alert("Une erreur est survenue lors de l'export du fichier. Détail de l'erreur: ".$e->getMessage(), 'danger', 'export-exception');
        }
    }

    private function getStaysTableHtml(): string
    {
        ob_start(); ?>
        <?= self::getStyling() ?>
        <table>
            <thead>
                <tr>
                    <th class="equal-width"><b>Participant</th>
                    <th class="equal-width"><b>Dates</b></th>
                    <th class="equal-width"><b>Repas sautés</b></th>
                    <th class="equal-width"><b>Options</b></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($this->stays as $s): ?>
                    <tr>
                        <td class="equal-width">
                            <?= $s->getName() ?>
                        </td>
                        <td class="equal-width">
                            Du <?= $s->getStartDate('d/m/Y') ?> au <?= $s->getEndDate('d/m/Y') ?> (<?= $s->getDays() ?> jours)
                        </td>
                        <td class="equal-width">
                            <?= $s->getSkippedMeals() ?> repas
                            <?php if(!empty($s->getSkippedMealsNotes())): ?>
                                <div><?= $s->getSkippedMealsNotes() ?></div>
                            <?php endif ?>
                        </td>
                        <td class="equal-width">
                            <?php if($s->getNoBreakfasts()): ?>
                                <div>Pas de petit-déjeuners</div>
                            <?php endif ?>
                            <?php if($s->getNoSnacks()): ?>
                                <div>Pas de goûters</div>
                            <?php endif ?>
                            <?php if($s->getIsKid()): ?>
                                <div>Demie-portion</div>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <?php return ob_get_clean();
    }

    private function getExpensesTableHtml(): string
    {
        ob_start(); ?>
        <?= self::getStyling() ?>
        <table>
            <thead>
                <tr>
                    <th><b>#</b></th>
                    <th><b>Date</b></th>
                    <th><b>Payé par</b></th>
                    <th><b>Montant</b></th>
                    <th><b>Notes</b></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = count($this->expenses) ?>
                <?php foreach($this->expenses as $e): ?>
                    <tr>
                        <td class='sm-width txt-center'>
                            <span style="font-size:10px"><?= $i ?></span>
                        </td>
                        <td class='md-width'>
                            <?= $e->getSpentAt('d/m/Y') ?>
                        </td>
                        <td class='md-width'>
                            <?= $e->getName() ?>
                        </td>
                        <td class='md-width'>
                            <?= $e->getAmount() ?>
                        </td>
                        <td class='lg-width'>
                            <?= $e->getNotes() ?>
                        </td>
                    </tr>
                    <?php $i-- ?>
                <?php endforeach ?>
            </tbody>
        </table>
        
        <?php return ob_get_clean();
    }

    private static function getStyling(): string
    {
        ob_start(); ?>
        <style>
            table {
                width:100%;
                border:1px solid #000;
                border-collapse:collapse;
                font-size:12px;
            }
            th, td {
                border: 1px solid #000;
                border-collapse: collapse;
                padding: 6px;
            }
            th.equal-width, td.equal-width {
                width: 25%;
            }
            th.sm-width, td.sm-width {
                width: 5%;
            }
            th.md-width, td.md-width {
                width: 15%;
            }
            th.lg-width, td.lg-width {
                width: 50%;
            }
            th.txt-center, td.txt-center {
                text-align: center;
            }
        </style>
        <?php return ob_get_clean();
    }

    private function getGlobalData(): string
    {
        $r = $this->result;
        ob_start(); 
        ?>
        <ul>
            <li>Total repas consommés: <?= round($r->getTotalMeals(), 2) ?> repas</li>
            <li>Total dépensé: <?= round($r->getTotalSpent(), 2) ?>€</li>
            <li>Prix moyen d'un repas: <?= round($r->getAverageMealPrice(), 2) ?>€</li>
        </ul>
        <?php 
        return ob_get_clean();
    }

}