<?php

use App\Database\Connexion;
use App\Database\Table\Table;
use App\Helper;

// 1. Create tables
require CONFIG_PATH . DS . 'manage-database' . DS . 'create-tables.php';

// 2. Add admin
$pdo = Connexion::getPDO();
$table->query(
    "INSERT INTO user (firstname, lastname, username, password_hash, email, role) VALUE ('" . ADMIN_FIRSTNAME . "', '" . ADMIN_LASTNAME . "', '" . ADMIN_USERNAME . "', '" . ADMIN_PASSWORD_HASH . "', '" . ADMIN_EMAIL . "', 'admin')"
);
