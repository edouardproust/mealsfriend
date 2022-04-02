<?php

use App\Account\Login;
use App\Database\Table\UserTable;

Login::redirectIfIsNotConnected($router->url('login'));

$pageTitle = "Modifier mes informations";

// get user data
$userTable = new UserTable;
Login::sessionStart();
$user = $userTable->getOneById($_SESSION['UserModel']->getID());

// update user if submited with no alert
$success = '';
if(!empty($_POST)) {
    $alert = $userTable->updateOneById($user->getID(), $_POST); // save form data to database and return UserModel
    if(empty($alert)) { // complete alerts system later
        $success .= 'Compte modifié avec succès.';
    }
}
// Form attributes & values (firstnameValue, lastnameValue, usernameValue, passwordValue, emailValue)
foreach(['firstname', 'lastname', 'username', 'email'] as $name) {
    $getName = 'get'.ucfirst($name);
    ${$name.'Value'} = !empty($_POST[$name]) ? $_POST[$name] : $user->$getName() ?? '';
}
$passwordValue = !empty($_POST['password']) ? $_POST['password'] : '';
$passwordRequired = false;
$submitBtnText = 'Modifier';
// show form
require 'form.php';