<?php require 'header.php' ?>

<div class="h-100 mf_bg-img-cover" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?= SITE_HOME_BG_IMAGE ?>') no-repeat center center; -webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;">
    <div class="container my-5">
        <?= $content ?>
    </div>
</div>

<?php require 'footer.php' ?>