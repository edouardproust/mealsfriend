<?php require 'header.php' ?>

<div class="container my-5">
    <div class="mb-5">
        <h1><?= $pageTitle ?></h1>
        <?php if (isset($pageDescription)): ?>
            <p class="lead mb-2"><?= $pageDescription ?></p>
        <?php endif ?>
        <?php if (isset($pageMeta)): ?>
            <p class="small text-muted"><?= $pageMeta ?></p>
        <?php endif ?>
    </div>
    <?= $content ?>
</div>

<?php require 'footer.php' ?>