<?php namespace App\Database\Table;

use App\Helper;
use App\Model\ProjectModel;
use App\Model\StayModel;

final class StayTable extends Table {

    protected $class = StayModel::class;

    public function getAllByProjectId(int $projectID): array
    {
        return $this->prepareFetchAllClass(
            "SELECT s.*, pa.fullname 
            FROM stay s
            JOIN participant pa ON s.participant_id = pa.id
            WHERE s.project_id = :id 
            ORDER BY pa.fullname ASC",
            ['id' => $projectID]
        );
    }

    public function getOneByParticipantsId(int $participantID): ?StayModel
    {
        return $this->prepareFetchClass(
            "SELECT * FROM stay WHERE id = :participant_id",
            ['participant_id' => $participantID]
        );
    }

    public function getIdsByProject(int $projectID): array
    {

        $stays = $this->prepareFetchAllClass(
            "SELECT id, project_id FROM stay
            WHERE project_id = :project_id",
            ['project_id' => $projectID]
        );
        $staysIDs = [];
        foreach($stays as $stay) {
            $staysIDs[] = $stay->getID();
        }
        return $staysIDs;
    }

    public function updateOnebyID(?array $_post, int $stayID): void
    {
        $this->prepared(
            "UPDATE stay SET
                start_date = :start_date,
                end_date = :end_date,
                skipped_meals = :skipped_meals,
                skipped_meals_notes = :skipped_meals_notes,
                no_breakfasts = :no_breakfasts,
                no_snacks = :no_snacks,
                is_kid = :is_kid
            WHERE id = :id",
            [
                'id' => $stayID,
                'start_date' => null != $_post['start_date'][$stayID] ? $_post['start_date'][$stayID] : null,
                'end_date' => null != $_post['end_date'][$stayID] ? $_post['end_date'][$stayID] : null,
                'skipped_meals' => $_post['skipped_meals'][$stayID] ?? null,
                'skipped_meals_notes' => Helper::e($_post['skipped_meals_notes'][$stayID]),
                'no_breakfasts' => isset($_post['no_breakfasts'][$stayID]) ? 1 : 0,
                'no_snacks' => isset($_post['no_snacks'][$stayID]) ? 1 : 0,
                'is_kid' => isset($_post['is_kid'][$stayID]) ? 1 : 0
            ]
        );
    }

    public function createAllForNewProject(ProjectModel $project): void
    {
        foreach($project->getParticipants() as $participant) {
            $this->prepared(
                "INSERT INTO stay (project_id, participant_id)
                VALUES (:project_id, :participant_id)",
                [
                    'project_id' => $project->getID(),
                    'participant_id' => $participant->getID()   
                ]
            );
        }
    }

}