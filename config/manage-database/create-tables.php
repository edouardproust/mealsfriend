<?php

$tablesToCreate = [
    'user',
    'project',
    'participant',
    'expense',
    'stay',
];

//------------------------------------

use App\Database\Connexion;
use App\Database\Table\Table;

$pdo = Connexion::getPDO(false);
$table = new Table;

// Delete existing tables

$table->query("SET FOREIGN_KEY_CHECKS = 0");
foreach ($tablesToCreate as $tableName) {
    $tableExists = $table->query('SELECT 1 FROM ' . $tableName);
    if ($tableExists) {
        $table->query('DROP TABLE ' . $tableName);
        $message .= "La table <b>" . DB_NAME . ".$tableName</b> as été supprimée.<br>";
    } else {
        $message .= "<span style='color:red'>La table <b>" . DB_NAME . ".$tableName</b> n'existe pas. La suppression n'a donc pas eu lieu.</span><br>";
    }
}
$table->query("SET FOREIGN_KEY_CHECKS = 1");

// Create tables

$message .= $table->createTable(
    'user',
    "id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
    firstname VARCHAR(255) NOT NULL, 
    lastname VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,  
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, 
    role VARCHAR(255) NULL DEFAULT 'user',
    PRIMARY KEY (id),
    UNIQUE KEY (username)"
);
$message .= $table->createTable(
    'project',
    "id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
    title VARCHAR(255) NOT NULL, 
    description VARCHAR(2000) NULL DEFAULT NULL, 
    slug VARCHAR(255) NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    archived  TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id)"
);
$message .= $table->createTable(
    'participant',
    "id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NULL DEFAULT NULL,
    fullname VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    INDEX (project_id, user_id),
    CONSTRAINT fk_projectparticipant_project
        FOREIGN KEY (project_id)
        REFERENCES project (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
    "
);
$message .= $table->createTable(
    'expense',
    "id INT UNSIGNED NOT NULL AUTO_INCREMENT, 
    project_id INT UNSIGNED NOT NULL,
    participant_id INT UNSIGNED NOT NULL,
    spent_at TIMESTAMP NOT NULL,
    amount FLOAT NOT NULL,
    notes VARCHAR(2000) NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_by INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    INDEX (participant_id, updated_by),
    CONSTRAINT fk_expense_project
        FOREIGN KEY (project_id)
        REFERENCES project (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_expense_participantid
        FOREIGN KEY (participant_id)
        REFERENCES participant (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE"
);
$message .= $table->createTable(
    'stay',
    "id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT UNSIGNED NOT NULL,
    participant_id INT UNSIGNED NOT NULL,
    start_date TIMESTAMP NULL DEFAULT NULL,
    end_date TIMESTAMP NULL DEFAULT NULL,
    skipped_meals BOOLEAN NULL DEFAULT 0,
    skipped_meals_notes VARCHAR(2000) NULL DEFAULT '',
    no_breakfasts BOOLEAN NULL DEFAULT 0,
    no_snacks BOOLEAN NULL DEFAULT 0,
    is_kid BOOLEAN NULL DEFAULT 0,
    PRIMARY KEY (id),
    INDEX (participant_id),
    CONSTRAINT fk_stay_project
        FOREIGN KEY (project_id)
        REFERENCES project (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    CONSTRAINT fk_stay_participant
        FOREIGN KEY (participant_id)
        REFERENCES participant (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE"
);
