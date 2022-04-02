<?php

use App\Account\Login;

Login::redirectIfIsNotConnected($router->url('login'));

Login::sessionStart();
/*** finish UserTable->deleteOneById() before unmuting and testing ***
(new UserTable)->deleteOneById($_SESSION['UserModel']->getID());
*/