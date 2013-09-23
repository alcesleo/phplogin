<?php

namespace Phplogin\Controllers;

use Phplogin\Models\LoginModel;
use Phplogin\Models\UserModel;
use Phplogin\Models\UserListModel;
use Phplogin\Views\LoginView;
use Phplogin\Views\DateTimeView;
use Phplogin\Views\AppView;
use Phplogin\Exceptions\NotAuthorizedException;
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
        // TODO: Take instances as params?

        // Open the db and give it to the LoginModel
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
        if ($this->loginView->userWantsToLogOut()) {
            return $this->logOut();
        }

        // TODO: Register user

        // Session login
        if ($this->loginModel->isLoggedIn()) {
            return $this->loginView->getLoginSuccessHTML();
        }

        // Cookie login
        if ($this->loginView->userHasSavedCredentials()) {
            return $this->loginWithCookies();
        }

        // Form login
        if ($this->loginView->userWantsToLogIn()) {
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
        // Get usercredentials from form view
        try {
            $credentials = $this->loginView->getCredentialsFromForm();
        } catch (Exception $e) {
            // TODO: Use custom exceptions instead of passing through errors from the model
            return $this->loginView->getFormHTML($e->getMessage());
        }

        // Authenticate
        try {
            $user = $this->loginModel->logInWithCredentials($credentials);
        } catch (NotAuthorizedException $e) {
            // TODO: String belongs in loginview
            return $this->loginView->getFormHTML(LoginView::ERR_AUTHENTICATION_FAILED);
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

    /**
     * Log out the current user
     * @return string HTML logout success
     */
    private function logOut()
    {
        // Only show logged out messege if not already logged out
        if ($this->loginModel->logOut()) {
            // Show logout success-page
            // TODO: The string belongs in the loginView
            return $this->loginView->getFormHTML('Du har nu loggat ut!');
        }
        // TODO: Delete cookies / temporary password
        // $this->loginView->unsetCookies();

        // TODO: Redirect to index instead?
        $this->appView->redirect();
    }
}
