<?php

namespace controllers;

use models\LoginModel;
use models\UserDatabaseModel as UserDAO;
use models\UserSaverModel;
use models\UserModel;
use views\LoginView;
use views\DateTimeView;
use views\UserView;
use views\AppView;

/**
 * # UC1 Autentisera användare
 *
 * ## Huvudscenario
 *
 * 1. Startar när en användare vill autentisera sig.
 * 2. Systemet ber om användarnamn och lösenord och valet att spara uppgifter
 * 3. Användaren anger användarnamn och lösenord
 * 4. Systemet autentierar användaren och presenterar att autentiseringen lyckades
 *
 * ## Alternativa scenarion
 *
 * 3a. Användaren anger att spara uppgifter
 *     1. Systemet autentisierar användaren och presenterar att autentiseringen lyckades och att uppgifterna har sparats.
 * 4a. Användaren kunde inte autentisieras
 *     1. Systemet presenterar felmeddelande
 *     2. Gå till steg 2 i huvudscenariot.
 *
 */
class LoginController
{

    private $loginModel;
    private $userSaverModel;

    // Views
    private $loginView;
    private $dateTimeView;
    private $appView;

    // TODO: Send in userdatabase
    public function __construct()
    {
        $this->loginModel = new LoginModel();
        $this->userSaverModel = new UserSaverModel();

        // Create instances
        $this->loginView = new LoginView($this->loginModel);
        $this->dateTimeView = new DateTimeView('sv_SE', '%A, den %e %B år %Y. Klockan är [%H:%M:%S].');

        // TODO: Should this be passed in as a param?
        $this->appView = new AppView();
    }

    // FIXME: This code is shit
    public function logIn()
    {
        $loginHTML;
        $dateHTML;
        $user;

        // Try to load from session
        try {
            $user = $this->userSaverModel->load();

            // FIXME: Copy pasta
            $this->loginView->showLoginSuccess($user);
            $loginHTML = $this->loginView->getWelcomeHTML();
        } catch (\Exception $ex) {
            // Don't give a shit
        }

        if (isset($user)) {

            if ($this->appView->userWantsToLogOut()) {
                // Log out
                $this->userSaverModel->remove();
                $this->loginView->showLogoutSuccess();
                $loginHTML = $this->loginView->getFormHTML();
            }

        // If not set from session
        } else {

            // When ?login is hit
            if ($this->appView->userWantsToLogIn()) {

                // Check session credentials
                // TODO Check this on other screens as well?

                // Validate form
                if ($this->loginView->validateFormInput()) {

                    // Authorize user
                    try {
                        $user = UserModel::authorizeUser($this->loginView->getPostUserName(), $this->loginView->getPostPassword());
                    } catch (\Exception $ex) {
                        $loginHTML = $this->loginView->showLoginFailed();
                    }

                    // TODO: Unfuck this up.
                    $this->loginView->showLoginSuccess($user);
                    $loginHTML = $this->loginView->getWelcomeHTML();

                    // Save the user session
                    $this->userSaverModel->save($user);
                // DRY as fuck.
                } else {
                    $loginHTML = $this->loginView->getFormHTML();
                }
            } else {
                $loginHTML = $this->loginView->getFormHTML();
            }

        }
        // Print out the page
        $dateHTML = $this->dateTimeView->getHTML();
        print $this->appView->getHTML($loginHTML, $dateHTML);

    }
}
