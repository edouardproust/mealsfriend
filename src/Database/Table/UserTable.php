<?php namespace App\Database\Table;

use App\Account\Login;
use App\Helper;
use App\Model\UserModel;

final class UserTable extends Table {

    protected $class = UserModel::class; 

    public function getAll(string $columns = "*")
    {
        return $this->queryfetchAllClass(
            "SELECT $columns FROM user"
        );
    }

    /** @return  \App\Model\UserModel[] */
    public function getAllByProjectId(int $projectID): array
    {
        return $this->prepareFetchAllClass(
            "SELECT u.id, u.firstname, u.lastname 
            FROM user u
            JOIN participant p ON u.id = p.user_id
            WHERE p.project_id = :id", 
            ['id' => $projectID]
        );
    }

    /**
     * Return All Users other than the ones set for the current project (based on project ID)
     * This is usefull for example to prevent users to select twice the same Users in a select field
     *
     * @param  int $projectID
     * @return  \App\Model\UserModel[]
     */
    public function getAllOtherThanProjectUsers(int $projectID): array
    {
        $output = $allUsers = $this->getAll();
        $thisProjectUsers = $this->getAllByProjectId($projectID);
        $thisProjectUsersIDs = [];
        foreach($thisProjectUsers as $user) {
            $thisProjectUsersIDs[] = $user->getID();
        }
        foreach($allUsers as $key => $user) {
            if(in_array($user->getID(), $thisProjectUsersIDs)) unset($output[$key]);
        }
        return $output;
    }

    /** @return UserModel|false Instance of UserModel if username match, FALSE otherwise */
    public function getOneByUsername(string $username)
    {
        return $this->prepareFetchClass(
            "SELECT * FROM user
            WHERE username = :username",
            ['username' => $username]
        );
    }

    /** @return UserModel|false Instance of UserModel if username match, FALSE otherwise */
    public function getOneById(int $id, string $columns = "*")
    {
        return $this->prepareFetchClass(
            "SELECT $columns FROM user
            WHERE id = :id",
            ['id' => $id]
        );
    }

    /** 
     * Return an array of Users models based on their IDs
     * 
     * @param int[] $usersIDs 
     * @return App\Model\UserModel[] 
    */
    public function getSeveralById(array $usersIDs, string $columns = "*"): array
    {
        $users = [];
        foreach($usersIDs as $userID) { // Replace ParticipantModel by UserModel
            $users[] = $this->getOneById((int)$userID, $columns);
        }
        return $users;
    }

    public function createOne(array $_post): ?string
    {
        $alert = $this->checkFormInputs($_post);
        if(empty($alert)) {
            $this->prepared(
                "INSERT INTO user (firstname, lastname, username, password_hash, email) 
                VALUES (:firstname, :lastname, :username, :password_hash, :email)",
                [
                    "firstname" => ucfirst($_post['firstname']),
                    "lastname" => ucfirst($_post['lastname']),
                    'username' => $_post['username'],
                    'password_hash' => password_hash($_post['password'], PASSWORD_DEFAULT, ['cost' => 14]),
                    'email' => $_post['email']
                ],
            );
            return null;
        }
        return $alert;
    }
    
    /**
     * updateOneById
     *
     * @param  int $id
     * @param  array $_post
     * @return string Alert string if an error occures or empty string '' if no alert
     */
    public function updateOneById(int $id, array $_post)
    {
        $queryValues = [
            "id" => $id,
            "firstname" => ucfirst(Helper::e($_post['firstname'])),
            "lastname" => ucfirst(Helper::e($_post['lastname'])),
            'username' => Helper::e($_post['username']),
            'email' => Helper::e($_post['email'])
        ];
        // password_hash (update only if field is filled)
        if(!empty($_post['password'])) {
            $passwordHashQuery = ', password_hash = :password_hash';
            $queryValues['password_hash'] = password_hash($_post["password"], PASSWORD_DEFAULT, ["cost" => 14]);
        } else {
            $passwordHashQuery = '';
        }
        // Process
        $alert = $this->checkFormInputs($_post, false);
        if(empty($alert)) {
            // process query
            $this->prepared(
                "UPDATE user SET 
                firstname = :firstname,  lastname = :lastname,  username = :username, email = :email".
                $passwordHashQuery." WHERE id = :id", 
                $queryValues 
            );
            // update session
            Login::sessionStart();
            $user = $this->getOneById($id);
            $_SESSION['UserModel'] = $user;
        }
        return $alert;
    }

    public function deleteOneById(int $id): void
    {
        $this->prepared(
            "DELETE FROM user
            WHERE id = :id",
            ['id' => $id]
        );
        // Create a new ParticipantModel with the UserModel fullname
        // populate all Projects he's in with that ParticipantModel
        // DEpopulate from all Projects he is in
    }

    /** @param array [ProjectModel[], string] Array containing: an array of ProjectModel instances + a string of projects IDs (eg. "1, 3, 5") */
    public function populateProjectModel(array $projectsById): void
    {
        [$array, $string] = $projectsById;
        $users = $this->queryfetchAllClass(
            "SELECT u.firstname, u.lastname, u.id, pu.project_id
            FROM participant pu
            JOIN user u ON u.id = pu.user_id
            WHERE pu.project_id IN ($string)"
        );
        foreach($users as $user) {
            $array[$user->getProjectID()]->addUser($user);
        }
    }

    /** @return  int[] Array of UserModels IDs */
    public function getUsersIDsByProject(int $projectID): array
    {
        $usersModels = $this->getAllByProjectID($projectID);
        $usersIDs = [];
        foreach($usersModels as $userModel) {
            $usersIDs[] = (int)$userModel->getID();
        }
        return $usersIDs;
    }

    private function usernameAlreadyExists(string $username): bool
    {
        $users = $this->getAll();
        $allUsernames = [];
        foreach($users as $user) {
            $allUsernames[] = $user->getUsername();
        }
        if(in_array($username, $allUsernames)) return true;
        else return false;
    }
    
    /**
     * Check the form's informations submitted by user.
     * Returns an Alert if form fields contain forbidden characters (special characters)
     * or if username already exists
     *
     * @param  mixed $_post Form submitted informations $_POST
     * @return string Alert if an error occures or empty string '' if no errors
     */
    private function checkFormInputs(array $_post, bool $checkUsername = true): string
    {
        $alert = '';
        foreach([
                'firstname' => 'prénom', 
                'lastname' => 'nom de famille', 
                'username' => 'nom d\'utilisateur (pseudo)', 
                'email' => 'email'
            ] as $field => $name) {
            if(Helper::stringContainsForbiddenCharacters($_post[$field])) {
                $alert .= "<li>Votre " . $name . " contient des caractères interdits.</li>";
            }
        }
        if($checkUsername && empty($alert) && $this->usernameAlreadyExists($_post['username'])) {
            $alert .= "<li>Ce nom d'utilisateur (pseudo) existe déja.</li>";
        }
        return $alert;
    }

}