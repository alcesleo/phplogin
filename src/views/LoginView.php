<?php

namespace views;

use \models\LoginModel;

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

    // Errors
    private $validationErrorMessage;
    private static $errorUsernameNotSet = 'Användarnamn saknas';
    private static $errorPasswordNotSet = 'Lösenord saknas';
    private static $errorWrongCredentials = 'Felaktigt användarnamn och/eller lösenord.';


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
        // TODO: Set cookies with the user credentials.

    }

    /**
     * @return string credential string, or empty string if not set
     */
    public function getSessionCredentials()
    {
        return isset($_SESSION[self::$sessionCredentials]) ? $_SESSION[self::$sessionCredentials] : '';
    }

    // TODO: Get $user from loginmodel...
    public function validateSessionCredentials($user)
    {
        return ($this->getSessionCredentials === $this->createCredentialString($user));
    }

    // TODO: Get the usermodel from the loginModel...?
    public function setSessionCredentials(\models\UserModel $user)
    {
        $_SESSION[self::$sessionCredentials] = $this->createCredentialString($user);
    }

    // TODO: Move this where it belongs
    // TODO: Don't need $user..?
    /**
     * Create a credential string based on the username, useragent and remote IP:
     * @param  [type] $user [description]
     * @return [type]       [description]
     */
    private function createCredentialString($user)
    {
        return md5($user->getUsername() . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
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

    public function showLoginFailed()
    {
        $this->showFormError(self::$errorWrongCredentials);
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
        return isset($_POST[self::$autoLoginName]) ? $_POST[self::$autoLoginName] : false;
    }

    private function showFormError($message)
    {
        $this->validationErrorMessage = $message;
    }

    public function getHTML()
    {
        if (isset($this->user) && $this->user->isAuthorized()) {
            return $this->getWelcomeHTML();
        }
        return $this->getFormHTML();
    }

    public function getWelcomeHTML()
    {
        return "
        <h2>" . $this->user->getUsername() . " är inloggad.</h2>
        <p>Inloggning lyckades.</p>
        <p><a href='?" . self::$logout . "'>Logga ut</a></p>
        ";

    }

    public function getFormHTML()
    {
        // Declare strings to be interpolated
        $legend = "Log in - please insert credentials";
        $error = $this->validationErrorMessage ? "<p>$this->validationErrorMessage</p>" : '';

        // Persist value from checkbox
        $checked = $this->getPostStayLoggedIn() ? 'checked=\"checked\"' : '';

        // Construct the form
        return "
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
