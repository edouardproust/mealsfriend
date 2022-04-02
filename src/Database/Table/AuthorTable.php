<?php namespace App\Database\Table;

use App\Model\AuthorModel;

final class AuthorTable extends Table {

    protected $class = AuthorModel::class;

    public function populateProjectModel($projectsById): void
    {
        [$array, $string] = $projectsById;
        $authors = $this->queryfetchAllClass(
            "SELECT u.firstname, u.lastname, u.id, p.id AS project_id
            FROM user u
            JOIN project p ON p.author_id = u.id
            WHERE p.id IN ($string)"
        );
        foreach($authors as $author) {
            $array[$author->getProjectID()]->setAuthor($author);
        }
    }

}