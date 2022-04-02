<?php namespace App\Database\Table;

use App\Model\ParticipantModel;
use App\Model\ProjectModel;

final class ParticipantTable extends Table {

    protected $class = ParticipantModel::class;

    public function getAllByProjectId(int $projectID, string $valuesToReturn = '*'): array
    {
        return $this->prepareFetchAllClass(
            "SELECT $valuesToReturn 
            FROM participant
            WHERE project_id = :id
            ORDER BY fullname ASC",
            ['id' => $projectID]
        );
    }

    private function addOne($projectID, $userID, $participantName): void
    {
        $this->prepared(
            "INSERT INTO participant (project_id, user_id, fullname)
            VALUES (:project_id, :user_id, :fullname)",
            [
                'project_id' => $projectID, 
                'user_id' => $userID, 
                'fullname' => $participantName
            ]
        );
    }

    /** 
     * Populate All ProjectModel models with ParticipantModel models
     * 
     * @param array [ProjectModel[], string] Array containing: an array of ProjectModel instances + a string of projects IDs (eg. "1, 3, 5") 
     */
    public function populateProjectModel(array $projectsById): void
    {
        [$array, $string] = $projectsById;
        $participants = $this->queryfetchAllClass(
            "SELECT * FROM participant WHERE project_id IN ($string) ORDER BY fullname ASC"
        );
        foreach($participants as $participant) {
            $array[$participant->getProjectID()]->addParticipant($participant);
        }
    }

    /**
     * Check if the ParticipantModel's name match with a UserModel name or not. 
     * Populated ProjectTable with Partcipant if no match (user_id === 0) / with UserModel otherwise (user_id === UserModel->getID())
     *
     * @param  mixed $projectID
     * @param  UserModel[] $allUsers
     * @param  string[] $participantsFullnames eg. ['Jean Dupont', 'Marie Leroy']
     * @return void
     */
    public function addIfNotUserAlready(int $projectID, ?array $participantsFullnames)
    {
        $allUsers = (new UserTable)->getAll('id, firstname, lastname');
        foreach ($allUsers as $user) {
            $allUsersData[$user->getID()] = $user->getName();
        }
        foreach ($participantsFullnames as $participantName) {
            if ($key = array_search($participantName, $allUsersData)) { // Is a registered user
                $userID = $key;
            } else { // Is a simple participant
                $userID = 0;
            } 
            $this->addOne($projectID, $userID, $participantName); // Add each row to the table
        }
    }

    public function deleteAllByProjectID(int $projectID): void
    {
        $this->prepared(
            "DELETE FROM participant
            WHERE project_id = :project_id",
            ['project_id' => $projectID]
        );
    }
    
    /**
     * Returns a table containing participants of a projects who are not registered users
     *
     * @return \App\Model\ParticipantModel[]
     */
    public static function getNonUsersOnes(ProjectModel $project): array
    {
        $participants = $project->getParticipants();
        $output = [];
        foreach($participants as $participant) {
            if( (int)$participant->getUserID() === 0 ) {
                $output[] = $participant;
            }
        }
        return $output;
    }

}