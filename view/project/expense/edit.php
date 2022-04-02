<?php

use App\Account\Login;
use App\Database\Table\ExpenseTable;

Login::redirectIfIsNotConnected($router->url('login'));

$pageTitle = "Modifier la dépense";

$expenseTable = new ExpenseTable;
$expense = $expenseTable->getOneByID($router->getParams('expense_id'));

if(!empty($_POST)) {
    $expenseTable->updateOne(
        $expense, 
        $_POST,
        $router->url('show-project', ['id' => $router->getParams('id'), 'slug' => $router->getParams('slug')])
    );
}
require 'form.php';