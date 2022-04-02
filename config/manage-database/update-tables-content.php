<?php

$newUsers = 7;
$newProjects = 3;
$minProjectEntries = 6;
$maxProjectEntries = 16;

//------------------------------------------

use App\Database\Connexion;
use App\Database\Table\Table;
use App\Helper;

$pdo = Connexion::getPDO();
$table = new Table;

$faker = Faker\Factory::create();

// Empty tables
$table->query("SET FOREIGN_KEY_CHECKS = 0");
$table->query("TRUNCATE TABLE project");
$table->query("TRUNCATE TABLE user");
$table->query("TRUNCATE TABLE participant");
$table->query("TRUNCATE TABLE expense");
$table->query("SET FOREIGN_KEY_CHECKS = 1");

// Set admin as user #1
$table->query(
    "INSERT INTO user (firstname, lastname, username, password_hash, email, role) VALUE ('" . ADMIN_FIRSTNAME . "', '" . ADMIN_LASTNAME . "', '" . ADMIN_USERNAME . "', '" . ADMIN_PASSWORD_HASH . "', '" . ADMIN_EMAIL . "', 'admin')"
);
$users[1] = ADMIN_FIRSTNAME . ' ' . ADMIN_LASTNAME; // add admin to users list [id => fullname]

// Fixtures
if (DEV_MODE) {

    // Fill 'user' table
    for ($i = 2; $i <= $newUsers; $i++) { // starts from 2 because of admin user (id 1)
        $firstname = $faker->firstName();
        $lastname = $faker->lastName();
        $username = strtolower($firstname . $lastname);
        $password = $faker->numerify(strtolower($firstname) . '###');
        $password_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 13]);
        $email = strtolower($firstname . '.' . $lastname) . '@' . $faker->freeEmailDomain();
        $created_at = $faker->dateTimeBetween('-6 months', 'now')->format("Y-m-d H:i:s");
        $table->query('INSERT INTO user (firstname, lastname, username, password_hash, email, created_at) VALUES ("' . $firstname . '", "' . $lastname . '", "' . $username . '", "' . $password_hash . '", "' . $email . '", "' . $created_at . '")');
        $users[(int)$pdo->lastInsertId()] = $firstname . ' ' . $lastname; // add each user to list as [id => fullname] eg. [2 => 'Jean Dupont']
    }

    // Fill 'project' table
    for ($i = 1; $i <= $newProjects; $i++) { // starts from 2 because of admin user (id 1)
        $project_id = $i; // for info pupropose only
        $title = ucfirst($faker->words($faker->numberBetween(1, 3), true));
        $description = $faker->sentences(3, true);
        $slug = Helper::stringToSlug($title);
        $author_id = $faker->randomElement(array_keys($users));
        $created_at = $faker->dateTimeBetween('-6 months', 'now')->format("Y-m-d H:i:s");
        $archived = $faker->numberBetween(0, 1);
        $table->query("INSERT INTO project (title, description, slug, author_id, created_at, archived) VALUES ('$title', '$description', '$slug', '$author_id', '$created_at', '$archived')");
        $projects[$project_id]['author'] = $author_id;
    }

    // Fill 'participant' table
    for ($i = 1; $i <= count($projects); $i++) {
        $participants = [];
        $project_id = $i;
        $participantsNumber = $faker->numberBetween(2, count($users)); // define a random number of participants    
        $registeredUsersNumber = $faker->numberBetween(2, $participantsNumber); // define the number of registered users among participants
        $participants['id'] = $faker->randomElements(array_keys($users), $registeredUsersNumber);
        $authorID = $projects[$i]['author'];
        if (array_search($authorID, $participants['id']) === false) { // if author's ID is not in array...
            $lastKey = key(array_slice($participants['id'], -1, 1, true)); //  ...then get last element's key in array
            $participants['id'][$lastKey] = $authorID; // ...and replace it by author's ID
        };
        foreach ($participants['id'] as $userID) { // get registered users fullnames
            $participants['fullname'][] = $users[$userID];
        }
        // set not registered users fullnames (ids are set to 'null')
        $min = count($participants['id']);
        $max = $participantsNumber - 1;
        if ($max >= $min) {
            for ($p = $min; $p <= $max; $p++) {
                $participants['id'][$p] = 0;
                $participants['fullname'][$p] = ucwords($faker->firstName() . ' ' . $faker->lastName());
            }
        }
        // query
        for ($q = 0; $q < count($participants['id']); $q++) {
            $participant_id = $participants['id'][$q];
            $participant_fullname = Helper::e($participants['fullname'][$q]);
            $table->query(
                "INSERT INTO participant (project_id, user_id, fullname) 
                VALUES ('" . $project_id . "', '" . $participant_id . "', '" . $participant_fullname . "')"
            );
            $projects[$i]['users_id'][] = $participants['id'][$q];
            $projects[$i]['participants_id'][] = $q + 1;
        }
    }

    // Fill 'expense' table
    for ($i = 1; $i <= count($projects); $i++) {
        $entries = $faker->numberBetween($minProjectEntries, $maxProjectEntries);
        $project_id = $i;
        for ($e = 1; $e <= $entries; $e++) {
            $participant_id = $faker->randomElement($projects[$i]['participants_id']);
            $spent_at = $faker->dateTimeBetween('-2 months', 'now')->format("Y-m-d H:i:s");;
            $amount = $faker->randomFloat(2, 5, 60);
            $notes = $faker->sentences($faker->numberBetween(0, 3), true);
            $author_id = 0;
            while ($author_id === 0) {
                $author_id = $updated_by = $faker->randomElement($projects[$i]['users_id']);
            }
            $created_at = $updated_at =  $faker->dateTimeBetween($spent_at, 'now')->format("Y-m-d H:i:s");
            $pdo->exec(
                "INSERT INTO expense (project_id, participant_id, spent_at, amount, notes, author_id, created_at, updated_at, updated_by) 
                VALUES ('" . $project_id . "', '" . $participant_id . "', '" . $spent_at . "', '" . $amount . "', '" . $notes . "', '" . $author_id . "', '" . $created_at . "', '" . $updated_at . "', '" . $updated_by . "')"
            );
        }
    }

    // Fill 'stay' table
    foreach ($projects as $p_id => $p_data) {
        for ($s = 1; $s <= count($p_data['participants_id']); $s++) {
            $project_id = $p_id;
            $participant_id = $s;
            $start_date = $faker->dateTimeBetween('-3 months', '-1 month')->format("Y-m-d H:i:s");
            $end_date = $faker->dateTimeBetween('-13 weeks', 'now')->format("Y-m-d H:i:s");
            $skipped_meals = $faker->numberBetween(0, 6);
            $skipped_meals_notes = '';
            $is_kid = $faker->randomElement([0, 1]);
            if ($is_kid == 1) {
                $no_breakfasts = 0;
                $no_snacks = 0;
            } else {
                $no_breakfasts = $faker->randomElement([0, 1]);
                $no_snacks = $faker->randomElement([0, 1]);
            }
            $pdo->exec(
                "INSERT INTO stay (project_id, participant_id, start_date, end_date, skipped_meals, skipped_meals_notes, no_breakfasts, no_snacks, is_kid) 
                VALUES ('" . $project_id . "', '" . $participant_id . "', '" . $start_date . "', '" . $end_date . "', '" . $skipped_meals . "', '" . $skipped_meals_notes . "', '" . $no_breakfasts . "', '" . $no_snacks . "', '" . $is_kid . "')"
            );
        }
    }
}
