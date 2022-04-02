<?php namespace App\Modal;

class ConfirmModal extends Modal {

    private $mainBtnUrl;
    private $title;
    private $content;
    private $mainBtnText;
    private $mainBtnColor;

    public function __construct(
        string $id, 
        string $mainBtnUrl,
        string $title = 'Êtes-vous sûr.e ?',
        string $content = 'Cette action est définitive. Les données supprimées ne pourront pas être récupérées.',
        string $mainBtnText = 'Supprimer',
        string $mainBtnColor = 'danger'
    ) {
        parent::__construct($id);
        $this->mainBtnUrl = $mainBtnUrl;
        $this->title = $title;
        $this->content = $content;
        $this->mainBtnText = $mainBtnText;
        $this->mainBtnColor = $mainBtnColor;
        $this->showModal();
    }

    public function showModal(): void
    {
       ?>
        <div class="modal fade" id="<?= $this->id ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $this->title ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= $this->content ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <a href="<?= $this->mainBtnUrl ?>" class="btn btn-<?= $this->mainBtnColor ?>"><?= $this->mainBtnText ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}