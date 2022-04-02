<?php

use App\Account\Login;
use App\Database\Table\ExpenseTable;

Login::redirectIfIsNotConnected($router->url('login'));

(new ExpenseTable)->deleteOneById(
    $router->getParams('expense_id'), 
    $router->url('show-project', ['id' => $router->getParams('id'), 'slug' => $router->getParams('slug')])
);