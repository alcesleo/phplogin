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

    public function __construct(LoginModel $loginModel)
    {
        // TODO: Take instances as params?

        $this->loginModel = $loginModel;

        $this->loginView = new LoginView($this->loginModel);
        $this->appView = new AppView();
    }

    /**
     * Prints out the entire html-page
     */
    public function indexAction()
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
            $$this->loginModel->saveTemporaryPassword($user);
            $this->loginView->setCookies($userName, $temporaryPassword);
        }
        */

        return $this->loginView->getLoginSuccessHTML();
    }

    // TODO: Should this be its own controller?
    /**
     * Log out the current user
     * @return string HTML logout success
     */
    private function logOut()
    {
        // Only show logged out messege if not already logged out
        if ($this->loginModel->logOut()) {
            // Show logout success-page
            return $this->loginView->getFormHTML(LoginView::LOGOUT_SUCCESS);
        }
        // TODO: Delete cookies / temporary password

        $this->appView->redirect();
    }
}
