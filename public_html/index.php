<?php

use App\{Helper, Router};
use App\Template\Template;

require '../vendor/autoload.php';

error_reporting(-1);
ini_set('display_errors', 'On');

// UNCOMMENT TO INIT THE APP (first upload online)
// require '../config/init.php';

if (DEV_MODE) {
    define("DEBUG_TIME", microtime(true));
    Helper::getWhoops();
}

// MENUS **************************************************************

$templateConfig = new Template(
    'default', // 1. default template name (inside root/view/template folder)
    VIEW_PATH, // 2. defautl views path
    [ // 3. menus
        'menu_header_main_1' => [
            'Mes séjours' => 'dashboard', // linkTitle => routerLinkName
            'Calcul rapide' => 'manual',
        ],
        'menu_header_secondary_1' => [
            '<i class="nav-menu-icon fas fa-user-alt"></i>' => 'show-user'
        ],
        /* Example of switching menus easily: in 'default menus' below, just replace 'menu_header_secondary_1' by 'menu_header_secondary_2'in order activate this menu
        'menu_header_secondary_2' => [ 
            'Test' => 'home'
        ] */
    ],
    [ // 4. default menus
        'menu_header_main' => 'menu_header_main_1',
        'menu_header_secondary' => 'menu_header_secondary_1' // position => menuKey
    ]
);

// ROUTER **************************************************************

(new Router($templateConfig))
    ->get('/', 'home.php', 'home', ['template' => 'homepage'])
    ->get('/dashboard', 'account/dashboard.php', 'dashboard')
    ->both('/manual', 'manual/show.php', 'manual')
    // session
    ->both('/login', 'account/login.php', 'login')
    ->get('/logout', 'account/logout.php', 'logout')
    // user
    ->both('/my-account', 'user/show.php', 'show-user') // a faire (identifier l'utilisateur avec $_SESSION)
    ->both('/signup', 'user/add.php', 'add-user')
    ->both('/edit-user', 'user/edit.php', 'edit-user') // a faire (identifier l'utilisateur avec $_SESSION)
    ->both('/delete-user', 'user/delete.php', 'delete-user') // a faire (identifier l'utilisateur avec $_SESSION)
    // expense
    ->both('/project/[*:slug]-[i:id]/add-expense', 'project/expense/add.php', 'add-expense')
    ->both('/project/[*:slug]-[i:id]/edit-expense-[i:expense_id]', 'project/expense/edit.php', 'edit-expense')
    ->get('/project/[*:slug]-[i:id]/delete-expense-[i:expense_id]', 'project/expense/delete.php', 'delete-expense')
    // project
    ->both('/project/[*:slug]-[i:id]', 'project/show.php', 'show-project', ['template' => 'no-title'])
    ->both('/add-project', 'project/add.php', 'add-project') // add some js to form to make it dynamic
    ->both('/edit-project-[i:id]', 'project/edit.php', 'edit-project')
    ->get('/delete-project-[i:id]', 'project/delete.php', 'delete-project')
    ->get('/archive-project-[i:id]', 'project/archive.php', 'archive-project')
    // admin
    ->both('/admin/site-settings', 'admin/site-settings.php', 'admin-settings')
    ->both('/admin/manage-database', 'admin/manage-database.php', 'admin-database')

    ->run('default', [
        // Header menu $menu
        'dashboard' => 'Mes séjours',
        'manual' => 'Saisie Manuelle'
    ]);
