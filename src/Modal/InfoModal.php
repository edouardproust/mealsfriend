<?php namespace App\Modal;

final class InfoModal extends Modal {

    private $title;
    private $content;

    public function __construct(string $id, string $title, string $content)
    {
        parent::__construct($id);
        $this->title = $title;
        $this->content = $content;
        $this->showModal();
    }

    public function showModal(): void
    {
       ?>
        <div class="modal fade" id="<?= $this->id ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $this->title ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?= $this->content ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}