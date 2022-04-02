<?php

use App\Account\Login;
use App\Database\Table\ExpenseTable;

Login::redirectIfIsNotConnected($router->url('login'));

$pageTitle = "Ajouter une dépense";

if(!empty($_POST)) {
    (new ExpenseTable)->addOne(
        $router->getParams('id'), 
        $_POST,
        $router->url('show-project', ['id' => $router->getParams('id'), 'slug' => $router->getParams('slug')])
    );
}
require 'form.php';