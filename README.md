# Mealsfriend

An application that allows you to easily keep track of meals with friends and family. Create an account and a new stay, and invite people sharing your stay. Everyone can add their expenses. At the end of the stay, simply click on "Calculate" to generate the list and the amount of what each person owes to the others.

## Requirements

- Composer

## Technologies

- PHP (no framework)
- Boostrap 5

## Deployment

When you first upload the website on the online server (via FTP client for exemple):

1. Run command: `composer install`

2. In /config/settings.php, set this const on 'false':
const DEV_MODE = true;
-> const DEV_MODE = false;

3. In /config/settings_secured.php, replace '*******' string by database infos and admin credentials.

4. In /public_html/index.php, uncomment this line
// require '../config/init.php';
-> require '../config/init.php';

5. Load the website homepage ONCE.

6. In /public_html/index.php, comment this line back:
require '../config/init.php';
-> // require '../config/init.php';
