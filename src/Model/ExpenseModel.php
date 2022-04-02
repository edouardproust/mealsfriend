<?php namespace App\Model;

final class ExpenseModel extends Model {

    protected $id;
    
    protected $project_id;
    protected $fullname;
    private $participant_id;
    private $spent_at;
    private $amount;
    private $notes;
    protected $created_at;
    protected $author_id;
    protected $author_firstname;
    protected $author_lastname;
    protected $updated_at;
    protected $updated_by;

    public function getParticipantID(): ?int
    {
        return $this->participant_id;
    }
    
    /**
     * getSpentAt
     *
     * @param  string|null $format string (eg. 'Y,M,d') / null to take default value defined by $this->datetimeToString()
     * @return string
     */
    function getSpentAt($format = null): ?string
    {
        return $this->datetimeToString($this->spent_at, $format);
    }

    function getAmount($withCurrency = true): ?string
    {
        if(!$withCurrency) return (float)$this->amount;
        return $this->amount . SITE_CURRENCY;
    }

    function getNotes(): ?string
    {
        return htmlentities($this->notes);
    }

}