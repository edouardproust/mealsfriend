<?php namespace App\Modal;

class FormModal extends Modal {

    /** @var Field[] */
    private $fields;
    /** @var string */
    private $mainBtnUrl;
    /** @var string */
    private $title;
    /** @var string */
    private $content;
    /** @var string */
    private $mainBtnText;
    /** @var string */
    private $mainBtnColor;

    public function __construct(
        string $id,
        array $fields,
        string $mainBtnUrl = '#',
        string $title = 'Remplir le formulaire', 
        string $content = 'Veuillez remplir le formulaire ci-dessous.',
        string $mainBtnText = 'Envoyer',
        string $mainBtnColor = 'primary'
    ){
        parent::__construct($id);
        $this->fields = $fields;
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
        <div class="modal fade text-dark" id="<?= $this->id ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?= $this->title ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <?= $this->content ?>
                        </div>
                        <?= $this->getFormHtml() ?>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <a href="<?= $this->mainBtnUrl ?>" class="btn btn-<?= $this->mainBtnColor ?>"><?= $this->mainBtnText ?></a>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    private function getFormHtml(): string
    {
        $formHtml = '';
        foreach($this->fields as $field) {
            $formHtml .= $field->html();
        }
        return '<form>' . $formHtml; // CLosing of <form> is set AFTER the submit button (in Modal.php)
    }

}