<?php

namespace views;

use \models\LoginModel;
use \models\UserModel;

/**
 * Handles the form where a user can log in, the associated messages etc.
 */
class LoginView
{
    private $loginModel;

    // TODO: Should these be in AppView...?
    private static $login = 'login';
    private static $logout = 'logout';

    // Variable-names for the input form.
    private static $passwordName = 'LoginView::Password';
    private static $userNameName = 'LoginView::UserName'; // FIXME: This is the ugliest variable name in history
    private static $autoLoginName = 'LoginView::Checked';
    private static $passwordID = 'PasswordID';
    private static $userNameID = 'UserNameID';
    private static $autoLoginID = 'AutologinID';

    // Session/Cookie-names
    private static $sessionCredentials = 'PersistLogin';

    // Notifications
    private $notificationMessage;
    private static $errorUsernameNotSet = 'Användarnamn saknas';
    private static $errorPasswordNotSet = 'Lösenord saknas';
    private static $errorWrongCredentials = 'Felaktigt användarnamn och/eller lösenord.';
    private static $loggedOutSuccess = "Du har nu loggat ut";

    private $weWillRememberYou = false;

    public function __construct(LoginModel $loginModel)
    {
        $this->loginModel = $loginModel;
    }

    /**
     * Saves a users credentials as coookies
     * @param UserModel $user The user to save
     */
    public function setUserCookies(UserModel $user)
    {
        setcookie(self::$userNameName, $user->getUsername(), time() + 60);
        setcookie(self::$passwordName, $user->getHash(), time() + 60);
    }

    public function getUserFromCookies()
    {
        if (! isset($_COOKIE[self::$userNameName]) || ! isset($_COOKIE[self::$passwordName])) {
            throw new \Exception("Cookies not set");
        }
        return UserModel::authorizeUser($_COOKIE[self::$userNameName], $_COOKIE[self::$passwordName], UserModel::AUTHORIZED_BY_COOKIES);
    }

    public function unsetUserCookies()
    {
        // Set expiration to 0
        setcookie(self::$userNameName, '', 0);
        setcookie(self::$passwordName, '', 0);
    }

    /**
     * @return boolean
     */
    public function validateFormInput()
    {
        if (strlen(trim($this->getPostUserName())) == 0) {
            $this->showFormError(self::$errorUsernameNotSet);
            return false;
        }

        if (strlen(trim($this->getPostPassword())) == 0) {
            $this->showFormError(self::$errorPasswordNotSet);
            return false;
        }

        return true;
    }

    public function showLogoutSuccess()
    {
        // TODO: Change name of this function to imply notification
        $this->showFormError(self::$loggedOutSuccess);
    }

    public function showLoginFailed()
    {
        $this->showFormError(self::$errorWrongCredentials);
    }

    public function showWeWillRememberYou()
    {
        $this->weWillRememberYou = true;
    }

    public function showLoginSuccess(\models\UserModel $user)
    {
        // FIXME: Is this really the best way?
        // This later gets checked in getHTML()
        $this->user = $user;
    }

    /**
     * @return string posted username, or empty string
     */
    public function getPostUserName()
    {
        return isset($_POST[self::$userNameName]) ? trim($_POST[self::$userNameName]) : "";
    }

    /**
     * @return string posted password, or empty string
     */
    public function getPostPassword()
    {
        return isset($_POST[self::$passwordName]) ? trim($_POST[self::$passwordName]) : "";
    }

    /**
     * @return boolean posted value of checkbox
     */
    public function getPostStayLoggedIn()
    {
        return isset($_POST[self::$autoLoginName]) ? true : false;
    }

    private function showFormError($message)
    {
        $this->notificationMessage = $message;
    }

    public function getWelcomeHTML()
    {
        $authBy = '';

        switch ($this->user->getAutLevel()) {
            case UserModel:: AUTHORIZED_BY_SESSION:
                $authBy = "";
                break;
            case UserModel::AUTHORIZED_BY_USER:
                if ($this->weWillRememberYou) {
                    $authBy = '<p>Inloggning lyckades och vi kommer ihåg dig till nästa gång.</p>';
                } else {
                    $authBy = "<p>Inloggning lyckades.</p>";
                }
                break;
            case UserModel::AUTHORIZED_BY_COOKIES:
                $authBy = "<p>Inloggning lyckades med hjälp av cookies.</p>";
                break;
        }

        return "
        <h2>" . $this->user->getUsername() . " är inloggad.</h2>
        $authBy
        <p><a href='?" . self::$logout . "'>Logga ut</a></p>
        ";

    }

    public function getFormHTML()
    {
        // Declare strings to be interpolated
        $legend = "Log in - please insert credentials";
        $error = $this->notificationMessage ? "<p>$this->notificationMessage</p>" : '';

        // Persist value from checkbox
        $checked = $this->getPostStayLoggedIn() ? 'checked=\"checked\"' : '';

        // Construct the form
        return "
        <h2>Ej inloggad</h2>

        <form action='?" . self::$login . "' method='post'>
            <fieldset>
                <legend>$legend</legend>

                $error

                <label for='" . self::$userNameID . "'>Username:</label>
                <input type='text' size='20' name='" . self::$userNameName . "' id='" . self::$userNameID . "' value='" . $this->getPostUserName() . "' />

                <label for='" . self::$passwordID . "'>Password:</label>
                <input type='password' size='20' name='" . self::$passwordName . "' id='" . self::$passwordID . "' />

                <label for='" . self::$autoLoginID . "'>Keep me logged in:</label>
                <input type='checkbox' name='" . self::$autoLoginName . "' id='" . self::$autoLoginID . "' $checked />

                <input type='submit' value='Log in'>
            </fieldset>
        </form>";
    }

    // TODO: Write your own functions for this

    /**
     * Returns a sanitized value from the form.
     *
     * From https://github.com/dntoll/1DV408ExamplesHT2013/blob/master/HanteraIndata/BookView.php
     *
     * @param  string $name
     * @return string empty if value is not set
     */
    private function getSafeInputField($name)
    {
        if (isset($_POST[$name]) == false) {
            return "";
        }

        return $this->sanitize($_POST[$name]);
    }

    /**
     * Sanitizes a string.
     *
     * From https://github.com/dntoll/1DV408ExamplesHT2013/blob/master/HanteraIndata/BookView.php
     *
     * @param  string $input
     * @return string
     */
    private function sanitize($input) {
        $input = trim($input);
        return filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    }
}
