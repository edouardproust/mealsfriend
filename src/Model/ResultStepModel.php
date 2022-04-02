<?php namespace App\Model;

class ResultStepModel {

    public $debtor;
    public $amount;
    public $creditor;

    public function __construct(string $debtor, float $amount, string $creditor)
    {
        $this->debtor = $debtor;
        $this->amount = $amount;
        $this->creditor = $creditor;
    }

    /**
     * Return HTML list of refund steps
     */
    public function getHtml(): string
    { 
        return '<li>' . $this->debtor . ' donne <b>' . $this->amount . '€</b> à ' . $this->creditor . '.</li>';
    }

}