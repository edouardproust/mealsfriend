<?php

use App\Account\Login;

Login::redirectIfIsNotConnected($router->url('login'));
Login::sessionDestroy($router->url('login'));