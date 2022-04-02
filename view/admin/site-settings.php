<?php

use App\Account\Login;

Login::redirectIfIsNotConnected($router->url('login'), 'admin');

$pageTitle = "Site settings & options";
$pageDescription = "Customize the website.";

if(!empty($_POST)) dump($_POST);
dump(isset($_POST['primary']));

?>
<h3>Site main color</h3>
<form action="" method="post">
<?php foreach(
    ['Blue' => 'primary', 
    'Gray' => 'secondary', 
    'Green' => 'success', 
    'Red' => 'danger', 
    'Yellow' => 'warning', 
    'Turquoise' => 'info', 
    'Off-white' => 'light', 
    'Black' => 'dark', 
    'White' => 'white'] 
    as $title => $slug): ?>
        <button type="submit" class="btn btn-<?= $slug ?>" name="<?= $slug ?>"><?= $title ?></button>
    <?php endforeach ?>
    
</form>