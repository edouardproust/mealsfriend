<?php namespace App\Calculation;

use App\Model\ResultModel;
use App\Model\StayModel;

class CalculationManual extends Calculation {

    /** @var int Number of participants */
    protected $participantsNr;

    public function __construct(int $participantsNr)
    {
        $stays = [];
        parent::__construct($stays);
        $this->participantsNr = $participantsNr;
    }

    public function getResult(): ResultModel
    {
        for($i=0; $i<$this->participantsNr; $i++) {
            $this->stays[] = new StayModel();
        }
        $this->hydrateStays('firstname', 'spent', 'meals');
        $this->hydrateModel('total_spent', 'total_meals', 'average_meal_price');
        $this->hydrateStays('balance', 'is_creditor');
        $this->setCreditorsAndDebtorsList(true);
        $this->hydrateModel('html_result_steps');
        if($this->resultModel->getHtmlResultSteps() == null) {
            $this->resultModel->setAlerts('Les participants ont dépensé autant chacun. Aucun remboursement à effectuer!', 'primary', 'no-refund');
        }
        return $this->resultModel;
    }

    private function hydrateStays(string ...$dataToPush): void
    {
        foreach($this->stays as $key => $stay) {
            foreach($dataToPush as $param) {
                switch ($param) {
                    case 'firstname': $stay->setFullname($_POST['firstname'][$key]); break;
                    case 'spent': $stay->setSpent(null, $_POST['spent'][$key]); break;
                    case 'meals': $stay->setMeals($_POST['meals'][$key]); break;
                    case 'balance': $stay->setBalance($this->resultModel->getAverageMealPrice()); break;
                    case 'is_creditor': $stay->setIsCreditor(); break;
                }
            }
        }
    }

}