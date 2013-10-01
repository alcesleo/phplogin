<?php

namespace Phplogin\Controllers;

use Phplogin\Models\LoginModel;
use Phplogin\Models\UserModel;
use Phplogin\Views\LoginView;
use Phplogin\Views\DateTimeView;
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
        $this->loginModel = $loginModel;
        $this->loginView = new LoginView($loginModel);
    }

    /**
     * Manages which function that should be called depending
     * on what session, cookies, form fields are set.
     * @return string HTML
     */
    public function handleState()
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
            return $this->loginWithSavedCredentials();
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
            $credentials = $this->loginView->getFormCredentials();
        } catch (Exception $e) {
            // TODO: Use custom exceptions instead of passing through errors from the model
            return $this->loginView->getFormHTML($e->getMessage());
        }

        // Authenticate
        try {
            $user = $this->loginModel->logInWithCredentials($credentials);
        } catch (NotAuthorizedException $e) {
            // TODO: String belongs in loginview
            return $this->loginView->getFormHTML(LoginView::ERR_FORM_AUTHENTICATION_FAILED);
        }

        // Save cookies
        if ($this->loginView->userWantsToStayLoggedIn()) {
            // Generate and save on server
            $temporaryPassword = $this->loginModel->generateTemporaryPassword($user);
            // Save on client
            $this->loginView->saveUserCredentials($temporaryPassword);

            return $this->loginView->getLoginSuccessHTML(LoginView::LOGGED_IN_WITH_FORM_CHECKED);
        }

        return $this->loginView->getLoginSuccessHTML(LoginView::LOGGED_IN_WITH_FORM);
    }

    /**
     * Handles login by cookies
     * @return string HTML
     */
    private function loginWithSavedCredentials()
    {
        try {
            // Get usercredentials from form view
            $temppw = $this->loginView->getSavedCredentials();
            // Authenticate
            $user = $this->loginModel->logInWithTemporaryPassword($temppw);
        } catch (Exception $e) {
            // Delete invalid cookies
            $this->loginView->removeSavedCredentials();
            return $this->loginView->getFormHTML(LoginView::ERR_COOKIE_AUTHENTICATION_FAILED);
        }

        // TODO: Refresh the cookie

        return $this->loginView->getLoginSuccessHTML(LoginView::LOGGED_IN_WITH_COOKIES);
    }

    /**
     * Log out the current user
     * @return string HTML logout success
     */
    private function logOut()
    {
        // Delete cookies on client
        $this->loginView->removeSavedCredentials();

        // Only show logged out messege if not already logged out
        if ($this->loginModel->logOut()) {
            // Show logout success-page
            return $this->loginView->getFormHTML(LoginView::LOGOUT_SUCCESS);
        } else {
            // TODO: Use a function for this?
            header('Location: /');
        }

    }
}
