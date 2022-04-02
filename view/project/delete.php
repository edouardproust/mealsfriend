<?php

use App\Account\Login;
use App\Database\Table\ProjectTable;

Login::redirectIfIsNotConnected($router->url('login'));

(new ProjectTable)->deleteOneById(
    $router->getParams('id'), 
    $router->url('dashboard')
);