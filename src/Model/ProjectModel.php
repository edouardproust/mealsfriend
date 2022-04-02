<?php

namespace App\Model;

use App\Helper;

final class ProjectModel extends Model
{

    protected $id;
    protected $created_at;
    private $title;
    private $slug;
    private $description;
    private $users_number;
    protected $author_id;
    protected $author_firstname;
    protected $author_lastname;
    /** @var bool 1 or 0 */
    private $archived;
    /** @var AuthorModel */
    private $author;
    /** @var Participants[] */
    private $participants;
    /** Total expenses for the project, inlucing all participants */
    private $total_spent;
    /** Total meals eaten by all the project's participants */
    private $total_meals;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return Helper::e($this->description);
    }

    public function getUsersNumber(): ?int
    {
        return $this->users_number;
    }

    public function getParticipants(): ?array
    {
        return $this->participants;
    }

    public function getAuthor(): ?AuthorModel
    {
        return $this->author;
    }

    protected function getAuthorFirstname()
    {
        return ucfirst($this->getAuthor()->getLastname());
    }

    public function getAuthorLastname()
    {
        return ucfirst($this->author_lastname);
    }

    public function getArchived(): ?int
    {
        return $this->archived;
    }

    public function getTotalSpent($expenses = null): ?float
    {
        if (!isset($this->total_spent)) $this->setTotalSpent($expenses);
        return (float)$this->total_spent;
    }

    public function getTotalMeals($stays = null): ?float
    {
        if (!isset($this->total_meals)) $this->setTotalMeals($stays);
        return (float)$this->total_meals;
    }

    public function setAuthor(AuthorModel $author): void
    {
        $this->author = $author;
    }

    public function addParticipant(ParticipantModel $participant): void
    {
        $this->participants[] = $participant;
    }

    /**
     * @param  mixed $expenses
     * @return void
     */
    public function setTotalSpent(array $expenses): void
    {
        $totalSpent = 0;
        foreach ($expenses as $expense) {
            $totalSpent += (float)$expense->getAmount(false);
        }
        $this->total_spent = $totalSpent;
    }

    public function setTotalMeals(array $stays): void
    {
        $totalMeals = 0;
        foreach ($stays as $stay) {
            $totalMeals += (float)$stay->getMeals();
        }
        $this->total_meals = $totalMeals;
    }

    public function getParticipantsListHtml(): string
    {
        $usersHtml = [];
        foreach ($this->getParticipants() as $participant) $usersHtml[] = $participant->getName();
        $usersStart = array_slice($usersHtml, 0, -1);
        $usersLast = end($usersHtml);
        return implode(', ', $usersStart) . ' & ' . $usersLast;
    }

    public static function slugFromTitle(string $title)
    {
        return Helper::stringToSlug($title);
    }
}
