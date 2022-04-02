<?php
if($project->getParticipants() !== null) {
    $usersHtml = $project->getParticipantsListHtml();
} else {
    $usersHtml = 'Aucun participant';
}
?>
<a class="text-decoration-none text-reset" href="<?= $router->url('show-project', ['slug'=>$project->getSlug(), 'id'=>$project->getID()]) ?>">
    <div class="card h-100 mf_card">
        <div class="card-header mf_bg-white">
            <h5 class="pt-2"><?= $project->getTitle() ?></h5>
        </div>
        <div class="card-body">
            <p><span><b>Avec:</b> </span><?= $usersHtml ?></p>
            <p class="card-text small"><?= $project->getDescription() ?></p>
        </div>
        <div class="card-footer mf_bg-white small text-muted">
            <span>Créé le <b><?= $project->getCreatedAt("d/m/Y") ?></b></span>
            <span> par <b><?= $project->getAuthorName() ?></b><span>
        </div>
    </div>
</a>