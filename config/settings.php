<?php

// dev mode
// CHANGE THIS ONLY ON ONLINE SERVER (PROD)
const DEV_MODE = true;

// Database
if (DEV_MODE) {
    require 'settings_dev.php';
} else {
    require 'settings_secured.php';
}

// Admin
const ADMIN_FIRSTNAME = "Edouard";
const ADMIN_LASTNAME = "Proust";
define("ADMIN_PASSWORD_HASH", password_hash(ADMIN_PASSWORD, PASSWORD_DEFAULT, ['cost' => 13]));

// Meta
const SITE_NAME = "MealsFriend";

// Global
const SITE_CURRENCY = "€";

// Template
const SITE_COLOR_BG = 'dark'; // Bootstrap "Theme colors": "primary", "secondary", "success", "info", "warning", "danger", "light", "dark"
const SITE_CSS_GLOBAL = [ // in '/public_html/css'
    // model: 'scriptName' (string) => deferExecution (bool)
    'bootstrap5' => false,
    'main' => false
];
const SITE_JS_GLOBAL = [ // in '/public_html/css or /public_html/js'
    // model: 'scriptName' (string) => deferExecution (bool)
    'bootstrap5' => false,
    'fontawesome' => true,
    'main' => true
];
const SITE_LOGO = 'logo-blog-icon.svg'; // in '/public_html/img/logo'
const SITE_LOGO_HEIGHT = 32; // px
const SITE_LOGO_WIDTH = 40; // px (only for SVG images)
const SITE_FAVICON = 'favicon-blog.png'; // in '/public_html/img/logo'
define('SITE_FOOTER_COPYRIGHT', '&copy; ' . date("Y", time()) . ' MealFriends.com - All rights reserved.');
const SITE_HOME_BG_IMAGE = '/img/bg-meal-friends.jpg';

?>
<style>
    body {
        /* font-family: 'Lato', sans-serif !important; */
        font-family: 'Open Sans', sans-serif !important;
    }
</style>