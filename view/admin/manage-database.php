<?php

use App\Account\Login;

Login::redirectIfIsNotConnected($router->url('login'), 'admin');

/**
 * This files stands for creating all the needed database tables automatically.
 * Use when pushing the project on a new online host.
 */

$action1 = 'Delete table and create new ones';
$action2 = 'Add random tables content';

$pageTitle = "Manage database";
$pageDescription = "Very high risk section :)";

//---------------------------------------------------

$message = '';

if (@$_POST['action'] === $action1) {
    require CONFIG_PATH . DS .'manage-database' . DS . 'create-tables.php';
} else if ((@$_POST['action'] === $action2)) {
    require CONFIG_PATH . DS .'manage-database' . DS . 'update-tables-content.php';
}

?>
<div class="mb-4">
    <h3>Actions</h3>
    <div class="alert alert-warning"><b>Warning</b>: This will erase any existing tables contents. Please proceed carefully.</div>
    <form action="" method="post">
        <?php foreach([ $action1, $action2 ] as $action): ?>
            <div class="mb-2"><input type="submit" name="action" class="btn btn-danger" value="<?= $action ?>"></div>
        <?php endforeach ?>
    </form>
</div>
<h3>Result</h3>
<?php if($message): ?>
    <p><?= $message ?></p>
<?php else: ?>
    <p>Nothing yet. Please choose an action above to process.</p>
<?php endif ?>