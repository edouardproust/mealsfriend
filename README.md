# Mealsfriend

An application that allows you to easily keep track of meals with friends and family. Create an account and a new stay, and invite people sharing your stay. Everyone can add their expenses. At the end of the stay, simply click on "Calculate" to generate the list and the amount of what each person owes to the others.

👉 [**Live demo**](https://https://mealsfriend.com/)

![image](https://user-images.githubusercontent.com/45925914/176819123-b05e5489-d99f-4141-b4b4-5bb595522657.png)

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

---------

## Project Info

Mealfriends allows you to calculate what you owe to your friends during your stays, in two different ways:
- By creating a trip where each participant enters his own expenses. To do this, each participant must create an account on Mealsfriend, enter their dates of stay. A "Calculate" button will appear on the stay page. After clicking on the button, the result of the calculation appears at the top of the stay page.
- By doing a quick calculation at the end of the stay. To do this, you must enter the expenses of each participant in the table and then click on calculate.
- The application has been designed in PHP vanilla, so without backend framework. The frontend is styled using Bootstrap 5.

### Mealsfriends has a number of features:
- In the stay options, you can define if a participant does not eat breakfast, is absent during several meals or is a child. This allows for a greater precision of calculation, which you will not find in any other application.
- Possibility to create the stay with unregistered participants (simple names in the list), then replace these participants by a registered user. The trip will then appear on the user's dashboard. Only the creator of the trip can do this. This allows more flexibility.
- Only the user who has registered an expense can delete it. Everyone can modify it by adding a comment. This allows a form of collaborative work.
- The calculation can only be performed when at least one expense has been recorded and the dates of stay of each participant have been entered.
- Export the result as a PDF document.
- Each participant can decide to archive a trip. The stay will then go to the "Archived Stays" section of their dashboard.

## Upcoming updates:
- Password recovery system
- Invitation of participants to a stay by email
- Send by email the result of the calculation of the stay in PDF
- English translation
- Ability to switch to dark theme
