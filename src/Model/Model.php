<?php namespace App\Model;

use App\Account\Login;
use App\Helper;
use DateTime;

abstract class Model {

    protected function datetimeToString(?string $date, string $format = null): ?string
    {
        if($date == null) return null;
        $dateTime = new DateTime($date);
        if($format === null) $format = 'F d, Y';
        return $dateTime->format($format);
    }

    // get IDs (int)

    public function getID(): ?int
    {
        return $this->id;
    }
    public function getProjectID(): ?int
    {
        return $this->project_id;
    }
    public function getUserID(): ?int
    {
        if($this->user_id == 0) {
            return null;
        }
        return $this->user_id;
    }
    public function getAuthorID(): ?int
    {
        return (int)$this->author_id;
    }
    public function getUpdatedBy(): ?int
    {
        return $this->updated_by;
    }

    // get Dates (string)

    public function getCreatedAt(string $format = null): ?string
    {
        if($this->created_at === null) return null;
        return $this->datetimeToString($this->created_at, $format);
    }
    public function getUpdatedAt(string $format = null): ?string
    {
        if($this->updated_at === null) return null;
        return $this->datetimeToString($this->updated_at, $format);
    }

    // get Names (string)

    public function getName(): ?string
    {
        if(!isset($this->firstname)) {
            return ucwords($this->fullname);
        }
        $name = ucfirst($this->firstname) . ' ' . ucfirst($this->lastname);
        return htmlspecialchars($name);
    }
    public function getFirstname(): ?string
    {
        return ucfirst($this->firstname);
    }
    public function getLastname(): ?string
    {
        return ucFirst($this->lastname);
    }
    public function getAuthorName(): ?string
    {
        Login::sessionStart();
        if( (int)$this->getAuthorID() === $_SESSION['UserModel']->getID() ) {
            return 'Moi';
        }
        return ucfirst($this->author_firstname) . ' ' . ucfirst($this->author_lastname);
    }

    // setters

    public function setFullname(string $fullname): void
    {
        $this->fullname = ucwords(Helper::e($fullname));
    }

}