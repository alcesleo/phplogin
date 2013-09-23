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

    // Get-pages
    private static $loginPage = 'login';
    private static $logoutPage = 'logout';

    // Variable-names for the input form.
    private static $passwordKey = 'LoginView::Password';
    private static $usernameKey = 'LoginView::UserName';
    private static $stayLoggedInKey = 'LoginView::Checked';
    private static $passwordID = 'PasswordID';
    private static $userNameID = 'UserNameID';
    private static $stayLoggedInID = 'AutoLoginID';

    // Notifications
    const ERR_USERNAME_NOT_SET = 'Användarnamn saknas';
    const ERR_PASSWORD_NOT_SET = 'Lösenord saknas';
    const ERR_AUTHENTICATION_FAILED = 'Felaktigt användarnamn och/eller lösenord.';
    const LOGOUT_SUCCESS = "Du har nu loggat ut";

    /**
     * @param LoginModel $loginModel
     */
    public function __construct(LoginModel $loginModel)
    {
        $this->loginModel = $loginModel;
    }

    /****************************************
    Cookie credentials
    *****************************************/

    /**
     * @return bool
     */
    public function userHasSavedCredentials()
    {
        // return true if both cookies are set
        return (isset($_COOKIE[self::$passwordKey]) && isset($_COOKIE[self::$passwordKey]));
    }

    /**
     * Saves a users credentials as coookies
     * @param UserModel $user The user to save
     */
    public function saveUserCredentials(UserModel $user)
    {
        // TODO: Variable for time
        // TODO: Use temporary passwords
        setcookie(self::$usernameKey, $user->getUsername(), time() + 60);
        setcookie(self::$passwordKey, $user->getHash(), time() + 60);
    }

    /**
     * Return an object containing the credentials saved in cookies
     * @return UserModel
     * @throws Exception If no credentials are saved
     */
    public function getSavedCredentials()
    {
        if (! isset($_COOKIE[self::$usernameKey]) || ! isset($_COOKIE[self::$passwordKey])) {
            throw new \Exception("Cookies not set");
        }

        return new UserModel($_COOKIE[self::$usernameKey], $_COOKIE[self::$passwordKey]);
    }

    /**
     * Remove cookie credentials on client
     */
    public function removeSavedCredentials()
    {
        // Set expiration in the past
        setcookie(self::$usernameKey, '', time()-3600);
        setcookie(self::$passwordKey, '', time()-3600);

        // Unset variables so they do not accidentally get called later
        unset($_COOKIE[self::$usernameKey]);
        unset($_COOKIE[self::$passwordKey]);
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

    /**
     * If loginpage is active
     * @return bool
     */
    public function userWantsToLogIn()
    {
        return isset($_GET[self::$loginPage]);
    }

    /**
     * If logoutpage is active
     * @return bool
     */
    public function userWantsToLogOut()
    {
        return isset($_GET[self::$logoutPage]);
    }

    /**
     * @return boolean posted value of checkbox
     */
    public function userWantsToStayLoggedIn()
    {
        return isset($_POST[self::$stayLoggedInKey]) ? true : false;
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
        $username = isset($_POST[self::$usernameKey]) ? trim($_POST[self::$usernameKey]) : "";
        $password = isset($_POST[self::$passwordKey]) ? trim($_POST[self::$passwordKey]) : "";

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

        // Get name of logged in user
        $loggedInUser = $this->loginModel->getLoggedInUser();
        $loggedInUserName = $loggedInUser->getUsername();

        return "
            <h2>$loggedInUserName är inloggad.</h2>
            <p><a href='?" . self::$logoutPage . "'>Logga ut</a></p>
        ";
    }

    /**
     * @param  string $message Notification/error-message shown in the form
     * @return string          HTML
     */
    public function getFormHTML($message = '')
    {
        // TODO: Clean up this function

        // Declare strings to be interpolated

        // Legend
        $legend = "Logga in - Skriv in användarnamn och lösenord";

        // Surround the message in p-tags if not empty
        $message = $message ? "<p>$message</p>" : '';

        // Persist value from checkbox
        $checked = $this->userWantsToStayLoggedIn() ? 'checked=\"checked\"' : '';

        // Construct the form
        // TODO: Get action from app-view...?
        return "
        <h2>Ej inloggad</h2>

        <form action='?" . self::$loginPage . "' method='post'>
            <fieldset>
                <legend>Logga in - Skriv in användarnamn och lösenord</legend>
                $message
                <label for='" . self::$userNameID . "'>Användarnamn:</label>
                <input type='text' size='20' name='" . self::$usernameKey . "'
                id='" . self::$userNameID . "' value='" . $this->getSafeInputStringFromPost(self::$usernameKey) . "' />

                <label for='" . self::$passwordID . "'>Lösenord:</label>
                <input type='password' size='20' name='" . self::$passwordKey . "' id='" . self::$passwordID . "' />

                <label for='" . self::$stayLoggedInID . "'>Håll mig inloggad:</label>
                <input type='checkbox' name='" . self::$stayLoggedInKey . "'
                id='" . self::$stayLoggedInID . "' $checked />

                <input type='submit' value='Log in'>
            </fieldset>
        </form>";
    }

    // TODO: Move these to a helper-class

    /**
     * Get variable $key from $_POST
     * @param  string $key key of post-variable
     * @return string      sanitized input, empty string if not set
     */
    private function getSafeInputStringFromPost($key)
    {
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : "";
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
