<?php namespace App\Calculation;

use App\Helper;
use App\Model\ResultModel;
use App\Model\{ResultStepModel, StayBeforeModel};
use DateTime;

class Calculation
{

    /** @var ResultModel */
    protected $resultModel;
    /** @var ExpenseModel[] */
    /** @var StayModel[] */
    protected $stays;
    /** @var StayBeforeModel[] Save of StayModel[] before updating objects */
    private $stays_before;
    /** @var StayModel[] Array of StayModel objects whose is_creditor property is on TRUE */
    private $creditors;
    /** @var StayModel[] Array of StayModel objects whose is_creditor property is on FALSE */
    private $debtors;
    
    public function __construct(array $stays, array $expenses = [])
    {
        $this->resultModel = new ResultModel;
        $this->expenses = $expenses;
        $this->stays = $stays;
        $this->creditors = [];
        $this->debtors = [];
    }
    
    /**
     * Process calculation
     * - Set ResultModel properties: $total_spent, $average_meal_price, $creditors, $debtors
     * - Populate $stays property with more informations: $meals, $duration, $days, $spent, $balance, $is_creditor 
     *
     * @param bool $manualCalculation Default: false | Set True is the calculation mode is manual
     * @return array[] [$infos, $resultHtml] 
     * - $infos: infos alerts (the users made mistakes or has to finish filling the table for the calculation to be able to start) OR empty array [] (the calculation is ready to start)
     * - $resultSteps: Html code to display steps.
     */
    public function getResult(): ResultModel
    {
        $rm = $this->resultModel;
        foreach($this->stays as $stay) {
            if( $stay->getDuration() === null ) {
                $rm->setAlerts('Certaines dates d\'arrivée et de départ doivent encore être remplies afin de pouvoir procéder au calcul.', 'primary','dates-error');
            }
        }
        if(empty($rm->getAlerts())) {
            $this->hydrateModel('total_spent', 'total_meals', 'average_meal_price');
            $this->hydrateStays();
            $this->hydrateModel('date-alerts');
            if($rm->getTotalSpent() != 0) {
                $this->setCreditorsAndDebtorsList(true);
                $this->hydrateModel('html_result_steps');
                if($rm->getHtmlResultSteps() == null) {
                    $rm->setHtmlResultSteps('<span class="text-danger">Les participants ont dépensé autant chacun. Aucun remboursement à effectuer!</span>');
                }
            } else {
                $rm->setAlerts('Les dates ont bien été enregistrées. Pour procéder au calcul, veuillez ajouter des dépenses ci-dessus (partie "Liste des dépenses").', 'primary', 'no-expenses');
            }
        }
        return $this->resultModel;
    }
    
    /**
     * Hydrate ResultModel misc. data
     *
     * @param string $dataToPush Allowed params: 'total_spent', 'total_meals', 'average_meal_price', 'date-alerts', 'html_result_steps' (Add as many as you like seperated by commas)
     * @return array[] return infos alerts (the users made mistakes or has to finish filling the table for the calculation to be able to start)
     *                  OR empty array [] (the calculation is ready to start)
     */
    public function hydrateModel(string ...$dataToPush): void
    {
        foreach($dataToPush as $param) {
            switch($param) {
                case 'total_spent': $this->hydrateModelWithTotalSpent(); break;
                case 'total_meals': $this->hydrateModelWithTotalMeals(); break;
                case 'average_meal_price': $this->hydrateModelWithAverageMealPrice(); break;
                case 'date-alerts': $this->hydrateModelWithDateAlerts(); break;
                case 'html_result_steps': $this->hydrateModelWithHtmlResultSteps(); break;
                default: throw new \Exception('Parameters used in hydrateModel() doesn\'t exist. Please refer to function info to get the complete list of allowed parameters.');
            }
        }
    }

    // Set class properties

    /** 
     * Save a StayBeforeModel[] as checkpoint, from a StayModel[], before modyfing it.
     * @param stayModel[]
     * @return StayBeforeModel[] 
     */
    private function setStaysBefore(array $stays, ?float $averageMealPrice): void
    {
        $checkpointArray = [];
        foreach($stays as $stay)
        {
            $checkpointArray[] = new StayBeforeModel(
                $stay->getName(), 
                $stay->getIsCreditor(), 
                $stay->getBalance($averageMealPrice)
            );
        }
        $this->stays_before = $checkpointArray;
    }

    protected function setCreditorsAndDebtorsList(bool $saveStaysBefore = false)
    {
        foreach($this->stays as $stay) {
            if($stay->getIsCreditor()) {
                $this->creditors[] = $stay;
            } else {
                $this->debtors[] = $stay; 
            }
        }
        if($saveStaysBefore) {
            $this->setStaysBefore(
                $this->stays, 
                $this->resultModel->getAverageMealPrice()
            );
            $this->hydrateModelWithHtmlBalancesBefore();
        }
    }

    // Hydrate StayModel[]

    private function hydrateStays(): void
    {
        foreach($this->stays as $stay) {
            $stay->setSpent();
            $stay->setMeals();
            $stay->setBalance($this->resultModel->getAverageMealPrice());
            $stay->setIsCreditor();
        }
    }

    // Hydrate ResultModel
    

    private function hydrateModelWithTotalMeals(): void
    {
        $totalMeals = 0;
        foreach($this->stays as $stay) {
            $totalMeals += (float)$stay->getMeals();
        }
        $this->resultModel->setTotalMeals($totalMeals);
    }

    private function hydrateModelWithTotalSpent(): void
    {
        $totalSpent = 0;
        if(!empty($this->expenses)) {
            foreach($this->expenses as $expense) {
                $totalSpent += (float)$expense->getAmount();
            }
        } elseif(!empty($this->stays)) {
            foreach($this->stays as $stay) {
                $totalSpent += (float)$stay->getSpent();
            }
        }
        $this->resultModel->setTotalSpent($totalSpent);
    }

    private function hydrateModelWithAverageMealPrice(): void
    {
        $ts = $this->resultModel->getTotalSpent();
        $tm = $this->resultModel->getTotalMeals();
        if(!$ts) $this->hydrateModelWithTotalSpent();
        if(!$tm) $this->hydrateModelWithTotalMeals();
        if($ts && $tm) {
            $this->resultModel->setAverageMealPrice($ts / $tm);  
        } else {
            $this->resultModel->setAverageMealPrice(null);
        }
    }

    private function hydrateModelWithDateAlerts(): void
    {
        foreach($this->stays as $stay) {
            // Vars
            $sName = $stay->getName();
            $sSlug = Helper::stringToSlug($stay->getName());
            $startDate = new DateTime($stay->getStartDate());
            $endDate = new DateTime($stay->getEndDate());
            $yearLimit = '10';
            $dateMin = new DateTime('-'.$yearLimit.' years');
            $dateMax = new DateTime('+'.$yearLimit.' years');
            // Set alerts
            if( $endDate < $startDate ) {
                $this->resultModel->setAlerts("La date de départ pour $sName ne peut être antérieure à la date d'arrivée.", "danger", "date-inverted-$sSlug");
            }
            if( $endDate == $startDate ) {
                $this->resultModel->setAlerts("Les dates de départ et d'arrivée pour $sName ne peuvent être identiques.", "danger", "date-same-$sSlug"); 
            }
            if( $startDate < $dateMin || $endDate < $dateMin ) {
                if($startDate < $dateMin) $dateName = "date d'arrivée";
                else $dateName = "date de départ";
                $this->resultModel->setAlerts("La $dateName pour $sName ne peut remonter à plus de $yearLimit ans.", "danger", "date-futur-start-$sSlug");
            }
            if( $startDate > $dateMax || $endDate > $dateMax) {
                if($startDate > $dateMax) $dateName = "date d'arrivée";
                else $dateName = "date de départ";
                $this->resultModel->setAlerts("La $dateName pour $sName ne peut être dans plus de $yearLimit ans.", "danger", "date-futur-start-$sSlug");
            }
        }
    }

    private function hydrateModelWithHtmlResultSteps(): void
    {
        // check if no steps to calculate
        if(!$this->creditors || !$this->debtors) {
            $this->resultModel->setHtmlResultSteps(null);
        } else {
            // vars
            $resultSteps = '';
            $creditors = $this->creditors;
            $debtors = $this->debtors;
            $amp = $this->resultModel->getAverageMealPrice();
            // creditors loop
            for ($c=0; $c < count($creditors); $c++) {
                // vars
                $creditor = $creditors[$c];
                $credBal = $creditor->getBalance($amp);
                $credName =  $creditor->getName();
                $d = 0; // (start with debtor index 0)
                $debtor = $debtors[$d];
                $debtBal = $debtor->getBalance($amp);
                $debName = $debtor->getName();
                // calculation
                while ($credBal > 0.001) { // until current creditor hasn't been fully refunded...
                    if($debtBal >= 0) { // If current debtor paid his debt...
                        // vars
                        $d++; 
                        $debtor = $debtors[$d];
                        $debtBal = $debtor->getBalance($amp); // ...skip him for the next debtor
                        $debName = $debtor->getName();
                    } else { // else, refund the current creditor
                        $sum = $credBal + $debtBal;
                        if($credBal > abs($debtBal)) { // if debtor balance is not enough to fully refund current creditor
                            $resultSteps .= (new ResultStepModel($debName, round(abs($debtBal), 2), $credName))->getHtml(); // save step as an object
                            $credBal = $creditor->setBalanceAfterResultStep($sum); // creditor balance refunded PARTIALLY by the FULL debtor's balance
                            $debtBal = $debtor->setBalanceAfterResultStep(0); // debtor balance set to 0
                        } else {
                            $resultSteps .= (new ResultStepModel($debName, round($credBal, 2), $credName))->getHtml(); // save step as an object
                            $debtBal = $debtor->setBalanceAfterResultStep($sum); // debtor balance reduced PARTIALLY  by the FULL creditor's balance
                            $credBal = $creditor->setBalanceAfterResultStep(0); // creditor balance set to 0

                        }
                    }
                }
            }
            $this->resultModel->setHtmlResultSteps('<ol>'.$resultSteps.'</ol>');
        }
    }

    private function hydrateModelWithHtmlBalancesBefore(): void
    {
        $balancesBefore = '';
        foreach ($this->stays_before as $sb) { // We use 'ParticipantModel->before' to get start state values
            $balancesBefore .= $sb->getHtmlBalance();
        }
        $this->resultModel->setHtmlBalancesBefore('<ul>'.$balancesBefore.'</ul>');
    }


}