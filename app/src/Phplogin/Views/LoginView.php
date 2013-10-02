<?php

namespace Phplogin\Views;

use Phplogin\Models\LoginModel;
use Phplogin\Models\TemporaryPasswordModel;
use Phplogin\Models\UserCredentialsModel;
use Exception;

/**
 * Handles the form where a user can log in, the associated messages etc.
 */
class LoginView extends View
{
    /**
     * @var LoginModel
     */
    private $loginModel;

    // Variable-names for the input form.
    private static $passwordKey = 'LoginView::Password';
    private static $usernameKey = 'LoginView::UserName';
    private static $stayLoggedInKey = 'LoginView::Checked';
    private static $passwordID = 'PasswordID';
    private static $userNameID = 'UserNameID';
    private static $stayLoggedInID = 'AutoLoginID';

    // Notification messages
    const ERR_USERNAME_NOT_SET = 'Användarnamn saknas';
    const ERR_PASSWORD_NOT_SET = 'Lösenord saknas';
    const ERR_FORM_AUTHENTICATION_FAILED = 'Felaktigt användarnamn och/eller lösenord.';
    const ERR_COOKIE_AUTHENTICATION_FAILED = 'Felaktig information i kaka.';
    const LOGOUT_SUCCESS = "Du har nu loggat ut";
    const LOGGED_IN_WITH_FORM = 'Inloggning lyckades';
    const LOGGED_IN_WITH_FORM_CHECKED = 'Inloggning lyckades och vi kommer ihåg dig nästa gång';
    const LOGGED_IN_WITH_COOKIES = 'Inloggning lyckades via cookies';

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
     * @param TemporaryPasswordModel $temppw
     */
    public function saveUserCredentials(TemporaryPasswordModel $temppw)
    {
        setcookie(self::$usernameKey, $temppw->getUsername(), $temppw->getExpirationTime());
        setcookie(self::$passwordKey, $temppw->getTemporaryPassword(), $temppw->getExpirationTime());
    }

    /**
     * Return an object containing the credentials saved in cookies
     * @return TemporaryPasswordModel containing Username and temporary password
     * @throws Exception If no credentials are saved
     */
    public function getSavedCredentials()
    {
        if (! isset($_COOKIE[self::$usernameKey]) || ! isset($_COOKIE[self::$passwordKey])) {
            throw new Exception("Cookies not set");
        }

        // Construct temporary password
        $ret = new TemporaryPasswordModel();
        $ret->setTemporaryPassword($_COOKIE[self::$passwordKey]);
        $ret->setUsername($_COOKIE[self::$usernameKey]);
        return $ret;
    }

    /**
     * Remove cookie credentials on client
     */
    public function removeSavedCredentials()
    {
        if ($this->userHasSavedCredentials()) {
            // Set expiration in the past
            setcookie(self::$usernameKey, '', time()-3600);
            setcookie(self::$passwordKey, '', time()-3600);

            // Unset variables so they do not accidentally get called later
            unset($_COOKIE[self::$usernameKey]);
            unset($_COOKIE[self::$passwordKey]);
        }
    }

    /****************************************
    Form
    *****************************************/

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

    /**
     * Get credentials from form input fields
     * @return UserCredentialsModel
     * @throws Exception If malformed input
     */
    public function getFormCredentials()
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
    public function getLoginSuccessHtml($message = '')
    {
        // TODO: Review this function, testing code

        // Get name of logged in user
        $loggedInUsername = $this->loginModel->getLoggedInUsername();

        return "
            <h2>$loggedInUsername är inloggad.</h2>
            $message
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

                <input type='submit' value='Logga in'>
            </fieldset>
        </form>";
    }

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
