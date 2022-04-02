<?php namespace App\Account;

use App\Database\Table\UserTable;

class Login {

    /** @return UserModel|false UserModel if credential match database data, FALSE otherwise */
    public static function checkCredentials(string $username, string $password)
    {
        $user = (new UserTable)->getOneByUsername($username);
        if(!empty($user)) {
            $verify = password_verify($password, $user->getPasswordHash());
            if($verify) return $user;
        }
        return false;
    }

    /** @return void|string Void on success | Error message on failure */
    public static function process(array $_post, string $redirectionUrlOnSuccess): string
    {
        self::sessionStart();
        $user = self::checkCredentials($_post['username'], $_post['password']);
        if($user) {
            $_SESSION['UserModel'] = $user;
            $_SESSION['connected'] = 1;
            header('Location:' . $redirectionUrlOnSuccess);
            exit;
        }
        return "L'identifiant et/ou le mot de passe sont incorrects";
    }

    public static function isConnected(): bool
    {
        self::sessionStart();
        return !empty($_SESSION['connected']);
    }

    public static function isAdmin(): bool
    {
        self::sessionStart();
        if(!empty($_SESSION['UserModel'])) {
            $role = $_SESSION['UserModel']->getRole() ?? null;
        }
        return !empty($role) && $role === 'admin';
    }

    /** 
     * Redirect to a specifi Url if the user is not connected
     * 
     * @param string $redirectUrl Url to redirect to if user is not connected
     * @param string $roleToCheck 'user' / 'admin' (default: 'user')
     * @todo No need to add session_start() before calling this function (already included in isConnected() and isAdmin() ). 
     * */
    public static function redirectIfIsNotConnected(string $redirectionUrl, $roleToCheck = 'user'): void
    {
        if($roleToCheck !== 'admin') {
            $valid = self::isConnected();
        } else {
            $valid = self::isAdmin();
        }
        if(!$valid) {
            header('Location: ' . $redirectionUrl);
            exit;
        }
    }


    public static function redirectIfIsConnected(string $redirectionUrl): void
    {
        if(self::isConnected()) {
            header('Location: ' . $redirectionUrl);
            exit;
        }
    }

    public static function sessionDestroy(string $redirectionUrl): void
    {
        self::sessionStart();
        unset($_SESSION['connected']);
        session_destroy();
        header('Location:'.$redirectionUrl);
        exit;
    }

    public static function sessionStart():void
    {
        if( session_status() === PHP_SESSION_NONE ) {
            session_start();
        }
    }

}