<?php

namespace Phplogin\Controllers;

use Phplogin\Models\LoginModel;
use Phplogin\Models\UserModel;
use Phplogin\Models\UserListModel;
use Phplogin\Views\LoginView;
use Phplogin\Views\DateTimeView;
use Phplogin\Views\AppView;
use Exception;

class LoginController
{
    /**
     * @var LoginModel
     */
    private $loginModel;

    /**
     * @var LoginView
     */
    private $loginView;

    /**
     * @var AppView
     */
    private $appView;

    public function __construct()
    {
        $db = new UserListModel('db/users.sqlite');
        $this->loginModel = new LoginModel($db);

        $this->loginView = new LoginView($this->loginModel);
        $this->appView = new AppView();
    }

    /**
     * Prints out the entire html-page
     */
    public function printLoginPage()
    {
        $loginHTML = $this->handleState();
        print $this->appView->getHTML($loginHTML);
    }

    /**
     * Manages which function that should be called depending
     * on what session, cookies, form fields are set.
     * @return string HTML
     */
    private function handleState()
    {
        // Log out
        if ($this->appView->userWantsToLogOut()) {
            return $this->logout();
        }

        // TODO: Register user here?

        // Session login
        if ($this->loginModel->isLoggedIn()) {
            return $this->loginView->getLoginSuccessHTML();
        }

        // Cookie login
        if ($this->loginView->isCookieCredentialsSet()) {
            return $this->loginWithCookies();
        }

        // Form login
        if ($this->appView->userWantsToLogIn()) {
            return $this->loginWithForm();
        }

        // Plain form
        return $this->loginView->getFormHTML();
    }

    /**
     * Handles the form data and shows error messeges,
     * or redirects to the success-screen.
     * @return string HTML
     */
    private function loginWithForm()
    {
        // Authenticate
        try {
            // Get usercredentials from form view
            $credentials = $this->loginView->getCredentialsFromForm();

            // Authenticate them
            // TODO: Put this in its own try, if it throws - loginView->showLoginFailedCredentials()
            $user = $this->loginModel->logIn($credentials);
        } catch (Exception $e) {
            // TODO: Use custom exceptions instead of passing through errors from the model
            return $this->loginView->getFormHTML($e->getMessage());
        }

        // Set cookies
        /*
        if ($this->loginView->userWantsToStayLoggedIn()) {
            $this->loginModel->getUsername
            $this->loginView->setCookies($userName, $temporaryPassword);
        }
        */

        return $this->loginView->getLoginSuccessHTML();
    }

    private function loginWithCookies()
    {
        // TODO: Testing code

        // Get information from cookies
        //$user = $this->loginView->getUsernameFromCookie()
        //$tempPassword = $this->loginView->getTemporaryPasswordFromCookie()
        //
        // Try to log in
        // $this->loginModel->logIn()
        // Cookie success
            // loginView->showLoggedInByCookies()
        // Cookie error
            // loginView->showLoginError(cookies?)
    }

    private function logout()
    {
        // TODO: Implement
        // Delete session variables
        $this->loginModel->logOut();

        // Delete cookies / temporary password
        // $this->loginView->unsetCookies();

        // Show logout success-page
        // TODO: The string belongs in the loginView
        return $this->loginView->getFormHTML('Du har nu loggat ut!');
    }
}
