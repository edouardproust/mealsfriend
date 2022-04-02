<?php namespace App\Model;

final class StayBeforeModel extends StayModel {

    protected $fullname;
    protected $is_creditor;
    protected $balance;

    public function __construct(string $fullname, bool $is_creditor, float $balance)
    {
        $this->fullname = $fullname;
        $this->is_creditor = $is_creditor;
        $this->balance = round($balance, 2);
    }

    /**
     * Return HTML list of balances before refund for each participant
     */
    public function getHtmlBalance(): string
    { 
        $class = $this->is_creditor ? 'text-success': 'text-danger';
        return '<li><span>'.$this->fullname.': </span><span class="'.$class.'">'.$this->balance.'€</span></li>';    
    }

}