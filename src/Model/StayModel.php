<?php namespace App\Model;

use App\Database\Table\ExpenseTable;
use App\Helper;
use DateInterval;
use DateTime;

class StayModel extends Model {

    protected $id;
    protected $project_id;
    protected $participant_id;
    protected $fullname;
    private $start_date;
    private $end_date;
    private $skipped_meals;
    private $skipped_meals_notes;
    private $no_breakfasts;
    private $no_snacks;
    private $is_kid;
    private $duration;
    private $days;
    private $meals;
    private $spent;
    protected $balance;
    protected $is_creditor;

    public function getStartDate(?string $format = null): ?string
    {
        return $this->datetimeToString($this->start_date, $format);
    }

    public function getEndDate(?string $format = null): ?string
    {
        return $this->datetimeToString($this->end_date, $format);
    }

    public function getSkippedMeals(): string
    {
        if (!empty($this->skipped_meals)) {
            return (string)$this->skipped_meals;
        } else {
            return "0";
        }  
    }

    public function getSkippedMealsNotes(): ?string
    {
        if (!empty($this->skipped_meals_notes)) {
            return Helper::e($this->skipped_meals_notes);
        } else {
            return '';
        }
    }

    public function getNoBreakfasts(): int
    {
        return $this->getBoolInt('no_breakfasts');
    }

    public function getNoSnacks(): int
    {
        return $this->getBoolInt('no_snacks');
    }

    public function getIsKid(): int
    {
        return $this->getBoolInt('is_kid');
    }

    public function getDuration(): ?DateInterval
    {
        if(null === $this->duration) {
            $this->setDuration();
        }
        return $this->duration;
    }

    public function getDays(): ?int
    {
        if (!isset($this->days)) {
            $this->setDays();
        }
        return $this->days;
    }

    public function getMeals(): ?float
    {
        if(!isset($this->meals)) {
            $this->setMeals();
        }
        return $this->meals;
    }

    public function getSpent(): ?float
    {
        if(!isset($this->spent)) {
            $this->setSpent();
        }
        return $this->spent;
    }

    /** @var float $averageMealPrice Possibility to add a average meal price to reduce requests if it has already been calculated previously in script */
    public function getBalance($averageMealPrice = null): ?float
    {
        if(!isset($this->balance)) {
            $this->setBalance($averageMealPrice);
        }
        return $this->balance;
    }

    public function getIsCreditor(): ?bool
    {
        if(!isset($this->is_creditor)) {
            $this->setIsCreditor();
        }
        return $this->is_creditor;
    }

    /**
     * @param  ExpenseTable|null $expenseTable Possibility to include and ExpenseTable instance if already existing in the page, to avoid create a new instance for nothing and save performances (default: null)
     * @param  float $spent Amout spent (This param should be defined for MANUAL CALCULATION only)
     */
    public function setSpent(?ExpenseTable $expenseTable = null, float $spent = null): void
    {
        if($spent) {
            $this->spent = $spent;
        } else {
            if(!$expenseTable) $expenseTable = new ExpenseTable;
            $projectExpenses = $expenseTable->getAllByProjectId((int)$this->project_id, 'participant_id, amount, fullname');
            $spent = 0;
            foreach($projectExpenses as $expense) {
                if($expense->getParticipantID() == $this->participant_id) {
                    $spent += (float)$expense->getAmount(false);
                }
            }
            $this->spent = $spent;
        }
    }

    public function setDuration(): void
    {
        if($this->start_date === '0000-00-00 00:00:00') $this->start_date = null;
        if($this->end_date === '0000-00-00 00:00:00') $this->end_date = null;
        if(null !== $this->start_date && null !== $this->end_date) {
            $startDate = new DateTime($this->start_date);
            $endDate = new DateTime($this->end_date);
            $this->duration = $startDate->diff($endDate);
        } else {
            $this->duration = null;
        }
    }

    public function setDays(): void
    {
        $duration = $this->getDuration();
        if($duration !== null && $duration->invert === 0) {
            $this->days = $this->getDuration()->days;
        } else {
            $this->days = null;
        }
    }

    public function setMeals(float $meals = null)
    {
        if($meals) {
            $this->meals = $meals;
        } else {
            $days = $this->getDays();
            $this->meals = $days * (2 + 2/3); // 1 day = 2 + 2/3 meals (breakfast and snack are both considered as 1/3 meal)
            $this->meals -= $this->skipped_meals;
            if ($this->no_breakfasts == 1) $this->meals -= (1/3) * $days; // (option: no breakfasts)
            if ($this->no_snacks == 1) $this->meals -= (1/3) * $days; // (option: no snacks)
            if ($this->is_kid == 1) $this->meals /= 2; // (option: is kid)
        }
    }

    public function setBalance(?float $averageMealPrice): void
    {
        if(null !== $this->getSpent() && null !== $this->getMeals()) {
            $this->balance = $this->getSpent() - ($this->getMeals() * $averageMealPrice);
        } else {
            $this->balance = null;
        }
    }

    public function setIsCreditor(): void
    {
        if(null !== $this->balance) {
            if ($this->balance >= 0) {
                $this->is_creditor = true;
            } else {
                $this->is_creditor = false;
            }
        } else {
            $this->is_creditor = null;
        }
    }
    
    public function setBalanceAfterResultStep (float $balanceAfterResultStep): float
    {
        $this->balance = $balanceAfterResultStep;
        return $this->balance;
    }

    public static function getChecked(StayModel $stay, string $valueKey): ?string
    {
        $functionName = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $valueKey)));
        if ( !empty( $stay->$functionName() ) && $stay->$functionName() == 1 ) {
            return 'checked';
        } else {
            return null;
        }
    }

    public function getBoolInt(string $fieldName)
    {
        if( !empty($this->$fieldName) || $this->$fieldName !== false ) {
            return (int)$this->$fieldName;
        } else {
            return 0;
        }
    }

}