<?php

use App\Account\Login;
use App\Database\Table\UserTable;

$pageTitle = "Créer un compte";
$pageDescription = "Tous les champs sont obligatoires";

$success = $alert = '';
if(!empty($_POST)) {
    $alert .= (new UserTable)->createOne($_POST, true); // save form data to database
    if(empty($alert)) Login::process($_POST, $router->url('dashboard'));
}
// Set form fields' value attribute (firstnameValue, lastnameValue, usernameValue, passwordValue, emailValue)
foreach(['firstname', 'lastname', 'username', 'password', 'email'] as $name) {
    ${$name.'Value'} = $_POST[$name] ?? '';
}
$passwordRequired = true;
$submitBtnText = 'Créer un compte';
// show form
require 'form.php';