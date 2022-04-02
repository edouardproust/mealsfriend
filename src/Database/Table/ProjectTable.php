<?php

namespace App\Database\Table;

use App\Account\Login;
use App\Model\ProjectModel;
use App\Helper;

class ProjectTable extends Table
{

    protected $class = ProjectModel::class;

    /** @return \App\Model\ProjectModel[]|null */
    public function getAll(?bool $archived = null): ?array
    {
        $query = "SELECT p.*, u.firstname AS author_firstname, u.lastname AS author_lastname
        FROM project p
        JOIN user u ON u.id = p.author_id\n";
        if ($archived === true) {
            $query .= "WHERE p.archived = 1\n";
        } elseif ($archived === false) {
            $query .= "WHERE p.archived = 0\n";
        }
        $query .= "ORDER BY created_at DESC";
        $projects = $this->queryfetchAllClass($query);
        if (empty($projects)) {
            return null;
        };
        $projectsById = self::projectsById($projects);
        (new ParticipantTable($this->pdo))->populateProjectModel($projectsById);
        (new AuthorTable($this->pdo))->populateProjectModel($projectsById);
        return $projects;
    }

    public function getOneById(int $projectID): ProjectModel
    {
        $project = $this->prepareFetchClass(
            "SELECT p.*, u.firstname AS author_firstname, u.lastname AS author_lastname
            FROM project p
            JOIN user u ON u.id = p.author_id
            WHERE p.id = :id",
            ['id' => $projectID]
        );
        (new ParticipantTable($this->pdo))->populateProjectModel($this->projectsByID($project));
        return $project;
    }

    /** 
     * @param  \App\ModelProject[]|App\ModelProject|null (array|string|null)
     * @return array $projectById
     */
    private static function projectsById($projects): ?array
    {
        if (!is_array($projects)) $projects = [$projects];
        $prByIdArray = [];
        foreach ($projects as $project) {
            $prByIdArray[$project->getID()] = $project;
        }
        $prByIdString = implode(', ', array_keys($prByIdArray));
        return [$prByIdArray, $prByIdString];
    }

    /** @return int|null $lastInsertId of $this */
    public function createOne(): ?int
    {
        Login::sessionStart();
        $this->prepared(
            "INSERT INTO project (title, description, slug, author_id) 
            VALUES (:title, :description, :slug, :author_id)",
            [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'slug' => Helper::stringToSlug($_POST['title']),
                'author_id' => $_SESSION['UserModel']->getID()
            ]
        );
        return $this->pdo->lastInsertId();
    }

    /**
     * updateOne
     *
     * @param  ProjectModel $project
     * @param  array $_post $_POST content
     * @param  string $redirectionUrl Default '/' (homepage)
     * @return string|void Return alert as a string if an error occures / Return noid (db update and page redirection) if no error occures
     */
    public function updateOne(ProjectModel $project, array $_post, string $redirectionUrl = '/'): string
    {
        if (isset($_post['user_id'])) {
            // Check if the same user has not been selected twice
            $usersIDs = $_post['user_id'];
            $selectedUsersIDs = [];
            foreach ($usersIDs as $userID) { // Remove empty usersIDs
                if (!empty($userID)) $selectedUsersIDs[] = $userID;
            }
            if (!empty($selectedUsersIDs)) {
                if (count(array_unique($selectedUsersIDs)) < count($selectedUsersIDs)) {
                    return 'Vous avez sélectionné le même utilisateur plusieures fois.'; // Return an alert
                }
            }
        }

        // 1. Update title, description & slug
        $this->prepared(
            "UPDATE project SET 
                title = :title, 
                description = :description,
                slug = :slug
            WHERE id = :id",
            [
                'id' => $project->getID(),
                'title' => $_post['title'],
                'description' => $_post['description'],
                'slug' => ProjectModel::slugFromTitle($_post['title'])
            ]
        );
        // 2. Replace Participants by Users
        if (!empty($selectedUsersIDs)) {
            $users = (new UserTable)->getSeveralById(array_filter($usersIDs), 'id, firstname, lastname');
            foreach ($users as $user) {
                $this->prepared(
                    "UPDATE participant SET 
                        user_id = :user_id,
                        fullname = :fullname
                    WHERE user_id = 0 AND project_id = :project_id
                    LIMIT 1",
                    [
                        'user_id' => $user->getID(),
                        'fullname' => $user->getName(),
                        'project_id' => $project->getID()
                    ]
                );
            }
        }
        header('Location:' . $redirectionUrl);
        exit;
    }

    public function deleteOneById(int $projectID, string $redirectionUrl = '/'): void
    {
        $this->prepared(
            "DELETE FROM project
            WHERE id = :id",
            ['id' => $projectID]
        );
        (new ParticipantTable)->deleteAllByProjectID($projectID);
        (new ExpenseTable)->deleteAllByProjectID($projectID);
        header('Location:' . $redirectionUrl);
        exit;
    }

    /** @param int $id ProjectModel ID
     *  @return string ProjectModel slug
     */
    public function getSlugFromId(int $id): string
    {
        $project = $this->prepareFetchClass(
            "SELECT slug FROM project WHERE id = :id",
            ['id' => $id]
        );
        return $project->getSlug();
    }

    public static function extractUsers(ProjectModel $project): array
    {
        $usersIDs = [];
        foreach ($project->getParticipants() as $participant) {
            if ((int)$participant->getUserID() != 0) {
                $usersIDs[] = (int)$participant->getUserID();
            }
        }
        return $usersIDs;
    }

    /** 
     * @param  int $userID Connected UserModel id eg. $_SESSION['UserModel]->getID() 
     * @return  \App\Model\ProjectModel[] Array of ProjectModel objects
     */
    public function getProjectsWhereUserAppears(?bool $archived = null): array
    {
        switch ($archived) {
            case true:
                $projects = $this->getAll(true);
                break;
            case false:
                $projects = $this->getAll(false);
                break;
            default:
                $projects = $this->getAll();
                break;
        }
        $projectsWhereUserAppears = [];
        if ($projects == null) $projects = [];
        foreach ($projects as $project) {
            if (in_array($_SESSION['UserModel']->getID(), $this->extractUsers($project))) {
                $projectsWhereUserAppears[] = $project;
            }
        }
        return $projectsWhereUserAppears;
    }

    public function archiveById(int $projectID): void
    {
        $this->prepared(
            "UPDATE project 
            SET archived = 1
            WHERE id = :id",
            ['id' => $projectID]
        );
    }

    public function unarchiveById(int $projectID): void
    {
        $this->prepared(
            "UPDATE project 
            SET archived = 0
            WHERE id = :id",
            ['id' => $projectID]
        );
    }

    /** 
     * Redirect User if he/she is not a participant of this project 
     * (if User accessed page taping the url in the adress bar) 
     */
    public static function redirectIfNotAParticipant(int $projectID, string $redirectionUrl): void
    {
        $usersIDs = (new UserTable)->getUsersIDsByProject($projectID);
        if (!in_array($_SESSION['UserModel']->getID(), $usersIDs)) {
            header('Location:' . $redirectionUrl);
        }
    }
}
