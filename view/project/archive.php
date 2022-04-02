<?php

use App\Database\Table\ProjectTable;

$projectTable = new ProjectTable;
$projectID = $router->getParams('id');
$projectSlug = $projectTable->getSlugFromId($projectID);
$isArchived = $projectTable->getOneById($projectID)->getArchived();
if (!$isArchived) {
    $projectTable->archiveById($projectID);
} else {
    $projectTable->unarchiveById($projectID);
}
header('Location: ' . $router->url('show-project', ['id' => $projectID, 'slug' => $projectSlug]));
