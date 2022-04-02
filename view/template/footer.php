<footer class="footer mt-auto py-3 bg-<?= SITE_COLOR_BG ?> text-white">
    <div class="container">
        <?php if(!empty(SITE_FOOTER_COPYRIGHT)): ?>
            <div class="text-muted small"><?= SITE_FOOTER_COPYRIGHT ?></div>
        <?php endif ?>
        <div>
            <?php if(DEV_MODE): ?>
                <span class="small <?= $textMuted ?>">Page generated in <?= ceil((microtime(true) - DEBUG_TIME) * 1000) ?> ms</span>
            <?php endif ?>
            <?php if($isAdmin): ?>
                <div class="small <?= $textMuted ?>">
                    <a class="text-muted" href="<?= $router->url("admin-settings") ?>">Site settings</a>
                    <span> | </span>
                    <a class="text-muted" href="<?= $router->url("admin-database") ?>">Manage database</a>
                </div>
            <?php endif ?>
        </div>
    </div>
</footer>

<?php $template->executeScripts('footer') ?>
  
</body>
</html>