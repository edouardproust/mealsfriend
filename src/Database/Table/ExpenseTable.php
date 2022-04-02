<?php namespace App\Database\Table;

use App\Account\Login;
use App\Helper;
use App\Model\ExpenseModel;

class ExpenseTable extends Table {

    protected $class = ExpenseModel::class;

    public function getOneByID(int $expenseID): ?ExpenseModel
    {
        return $this->prepareFetchClass(
            "SELECT *
            FROM expense
            WHERE id = :id",
            ['id' => $expenseID]
        );
    }

    /** @return App\Model\ExpenseModel[] Array of ExpenseModel models */
    public function getAllByProjectId(int $projectID, string $values = null): array
    {
        if($values === null) $values = 'e.*, p.fullname, u.firstname AS author_firstname, u.lastname AS author_lastname';
        return $this->prepareFetchAllClass(
            "SELECT $values  
            FROM expense e
            JOIN user u ON e.author_id = u.id
            JOIN participant p ON p.id = e.participant_id
            WHERE e.project_id = :id
            ORDER BY e.spent_at DESC",
            ['id' => $projectID]
        );
    }

    public function addOne(int $projectID, array $_post, string $redirectionUrl = '/'): void
    {
        Login::sessionStart();
        $this->prepared(
            "INSERT INTO expense (project_id, participant_id, spent_at, amount, notes, author_id, updated_at, updated_by)
            VALUES (:project_id, :participant_id, :spent_at, :amount, :notes, :author_id, :updated_at, :updated_by)",
            [
                'project_id' => $projectID,
                'participant_id' => $_post['participant_id'],
                'spent_at' => $_post['spent_at'],
                'amount' => $_post['amount'],
                'notes' => Helper::e($_post['notes'], true) ?? '',
                'author_id' => $_SESSION['UserModel']->getID(),
                'updated_at' => date('Y-m-d'),
                'updated_by' => $_SESSION['UserModel']->getID()
            ]
        );
        header('Location:'. $redirectionUrl);
        exit;
    }

    public function updateOne(ExpenseModel $expense, array $_post, string $redirectionUrl = '/'): void
    {
        Login::sessionStart();
        $this->prepared(
            "UPDATE expense 
            SET  
                participant_id = :participant_id, 
                spent_at = :spent_at, 
                amount = :amount,
                notes = :notes, 
                updated_at = :updated_at,
                updated_by = :updated_by
            WHERE id = :id",
            [
                'id' => $expense->getID(),
                'participant_id' => $_post['participant_id'],
                'spent_at' => $_post['spent_at'],
                'amount' => $_post['amount'],
                'notes' => Helper::e($_post['notes'], true) ?? '',
                'updated_at' => date('Y-m-d'),
                'updated_by' => $_SESSION['UserModel']->getID() // change this value once SESSION system (accounts) is created
            ]
        );
        header('Location:'. $redirectionUrl);
        exit;
    }

    public function deleteOneById(int $expenseID, string $redirectionUrl = '/'): void
    {
        $this->prepared(
            "DELETE FROM expense
            WHERE id = :id",
            [ 'id' => $expenseID]
        );
        header('Location:'. $redirectionUrl);
        exit;
    }

    public function deleteAllByProjectID(int $projectID): void
    {
        $this->prepared(
            "DELETE FROM expense
            WHERE project_id = :project_id",
            ['project_id' => $projectID]
        );
    }

}