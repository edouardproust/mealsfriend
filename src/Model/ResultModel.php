<?php namespace App\Model;

use App\Alert;

class ResultModel {

    /** @var float Total meals eaten by all participants */
    private $total_meals;

    /** @var float Total amount spent by all participants */
    private $total_spent;

    /** @var float Average Price of a meal during the stay, for all participants */
    private $average_meal_price;

    /** @var string HTML list describing in 'human language' each step to process the refund between participants of the project */
    private $html_result_steps;

    /** @var string HTML List of balances of each participant before refund */
    private $html_balances_before;

    /** @var Alert[] Messages issued during calculation process */
    private $alerts = [];


    public function getTotalMeals(): ?float
    {
        return $this->total_meals;
    }

    public function setTotalMeals(float $total_meals): void
    {
        $this->total_meals = $total_meals;
    }

    public function getTotalSpent(): ?float
    {
        return $this->total_spent;
    }

    public function setTotalSpent(float $total_spent): void
    {
        $this->total_spent = $total_spent;
    }

    public function getAverageMealPrice(): ?float
    {
        return $this->average_meal_price;
    }

    public function setAverageMealPrice(?float $average_meal_price): void
    {
        $this->average_meal_price = $average_meal_price;
    }

    public function getHtmlResultSteps(): ?string
    {
        return $this->html_result_steps;
    }

    public function setHtmlResultSteps(?string $html_result_steps): void
    {
        $this->html_result_steps = $html_result_steps;
    }

    public function getHtmlBalancesBefore(): ?string
    {
        return $this->html_balances_before;
    }

    public function setHtmlBalancesBefore(string $html_balances_before): void
    {
        $this->html_balances_before = $html_balances_before;
    }

    public function getAlerts(): array
    {
        return $this->alerts;
    }
    
    /**
     * Add alerts to $this->alerts property
     * Saved format: $name => [$class, $content]
     *
     * @param  mixed $content The content of the alert, to be displayed on the view
     * @param  mixed $name Default: null | A name to distinguish this alert from another insite the array
     * @param  mixed $class Default: 'primary' | Use Bootstrap color classes: 'primary', 'secondary', 'info', 'warning', etc.
     * @return void
     */
    public function setAlerts(string $content, string $name = null, string $class = 'primary'): void
    {
        $this->alerts[] = new Alert($content, $name, $class);
    }
}