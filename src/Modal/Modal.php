<?php namespace App\Modal;

abstract class Modal {

    /** @var string ID that join triggers with modals */
    protected $id;

    public function __construct(string $id) {
        $this->id = $id;
    }

    /**
     * Display modal's trigger (link or button to click)
     *
     * @param  mixed $text The text of the link or button
     * @param  mixed $type Type of trigger ('button' or 'link')
     * @param  mixed $class Bootstrap class to stylize the trigger
     * @return void Html code
     */
    public function showTrigger(
        string $text, 
        string $type = 'button', 
        string $class = 'btn-icon btn btn-primary'
    ): void {
        switch($type) {
            case 'button': ?>
                <button type="button" class="<?= $class ?>" data-bs-toggle="modal" data-bs-target="#<?= $this->id ?>">
                    <?= $text ?>
                </button>
                <?php break;
            case 'link': ?>
                <a href="#<?= $this->id ?>" class="<?= $class ?>" data-bs-toggle="modal" data-bs-target="#<?= $this->id ?>">
                    <?= $text ?>
                </a>
                <?php break;
            default:
                throw new \Exception ('Le paramètre passé dans showTrigger() n\'est pas valide.');
        }
    }
    
    /**
     * Show modal and its content. 
     * TO configure in children classes.
     *
     * @param  string $title
     * @param  string $content
     * @param  string $mainBtnText
     * @return void
     */
    public function showModal(): void 
    { }

}