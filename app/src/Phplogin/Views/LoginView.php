<?php

namespace Phplogin\Views;

use Phplogin\Models\LoginModel;
use Phplogin\Models\UserCredentialsModel;
use Exception;

/**
 * Handles the form where a user can log in, the associated messages etc.
 */
class LoginView
{
    /**
     * @var LoginModel
     */
    private $loginModel;

    // TODO: Should these be in AppView?
    private static $login = 'login';
    private static $logout = 'logout';

    // Variable-names for the input form.
    private static $passwordName = 'LoginView::Password';
    private static $userNameName = 'LoginView::UserName'; // FIXME: This is the ugliest variable name in history
    private static $autoLoginName = 'LoginView::Checked';
    private static $passwordID = 'PasswordID';
    private static $userNameID = 'UserNameID';
    private static $autoLoginID = 'AutologinID';

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

    public function isCookieCredentialsSet()
    {
        // return true if both cookies are set
        return (isset($_COOKIE[self::$passwordName]) && isset($_COOKIE[self::$passwordName]));
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
        // FIXME: Line too long
        return new UserModel($_COOKIE[self::$userNameName]);
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

    public function showLoginSuccess()
    {
        // FIXME: Is this really the best way?
        // This later gets checked in getHTML()
        $this->user = $user;
    }

    // **** TODO: Remove these functions
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
    public function userWantsToStayLoggedIn()
    {
        return isset($_POST[self::$autoLoginName]) ? true : false;
    }
    // **** END

    private function showFormError($message)
    {
        $this->notificationMessage = $message;
    }

    /**
     * Get credentials from form input fields
     * @return UserCredentialsModel
     * @throws Exception If malformed input
     */
    public function getCredentialsFromForm()
    {
        // Get input fields
        // NOTE: Dependancy on form output
        $username = isset($_POST[self::$userNameName]) ? trim($_POST[self::$userNameName]) : "";
        $password = isset($_POST[self::$passwordName]) ? trim($_POST[self::$passwordName]) : "";

        // Check for empty fields
        // TODO: Don't depend on exception messages
        if (strlen($username) == 0) {
            throw new Exception('Användarnamn saknas');
        }
        if (strlen($password) == 0) {
            throw new Exception('Lösenord saknas');
        }

        return new UserCredentialsModel($username, $password);
    }


    /**
     * Success-page displaying the currently logged in
     * user, and a logout-button.
     * @return string HTML
     */
    public function getLoginSuccessHTML()
    {
        // TODO: Review this function, testing code

        // Make sure this function isn't called without being logged in
        assert($this->loginModel->isLoggedIn());

        // TODO: Find out login-method?
        $authBy = '';

        //return "Logged in";
        return "
            <h2>" . $this->loginModel->getLoggedInUser()->getUsername() . " är inloggad.</h2>
            $authBy
            <p><a href='?" . self::$logout . "'>Logga ut</a></p>
        ";
    }

    /**
     * @param  string $message Notification/error-message shown in the form
     * @return string          HTML
     */
    public function getFormHTML($message = '')
    {
        // TODO: Clean up this function
        // TODO: Get action from app-view...?
        // Declare strings to be interpolated
        $legend = "Logga in - Skriv in användarnamn och lösenord";

        // Surround the message in p-tags if not empty
        $message = $message ? "<p>$message</p>" : '';

        // Persist value from checkbox
        $checked = $this->userWantsToStayLoggedIn() ? 'checked=\"checked\"' : '';

        // Construct the form
        return "
        <h2>Ej inloggad</h2>

        <form action='?" . self::$login . "' method='post'>
            <fieldset>
                <legend>Logga in - Skriv in användarnamn och lösenord</legend>
                $message
                <label for='" . self::$userNameID . "'>Användarnamn:</label>
                <input type='text' size='20' name='" . self::$userNameName . "'
                id='" . self::$userNameID . "' value='" . $this->getPostUserName() . "' />

                <label for='" . self::$passwordID . "'>Lösenord:</label>
                <input type='password' size='20' name='" . self::$passwordName . "' id='" . self::$passwordID . "' />

                <label for='" . self::$autoLoginID . "'>Håll mig inloggad:</label>
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
    private function sanitize($input)
    {
        $input = trim($input);
        return filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    }
}
