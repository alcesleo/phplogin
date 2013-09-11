<?php

namespace controllers;

use models\LoginModel;
use models\UserDatabaseModel as UserDAO;
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

    // Views
    private $loginView;
    private $dateTimeView;
    private $appView;

    // TODO: Send in userdatabase
    public function __construct()
    {
        $this->loginModel = new LoginModel();

        // Create instances
        $this->loginView = new LoginView($this->loginModel);
        $this->dateTimeView = new DateTimeView('sv_SE', '%A, den %e %B år %Y. Klockan är [%H:%M:%S].');

        // TODO: Should this be passed in as a param?
        $this->appView = new AppView();
    }

    public function logIn()
    {
        $loginHTML;
        $dateHTML;

        // When ?login is hit
        if ($this->appView->userWantsToLogIn()) {

            // Validate form
            if ($this->loginView->validateFormInput()) {

                // Authorize user
                try {
                    $user = UserModel::authorizeUser($this->loginView->getPostUserName(), $this->loginView->getPostPassword());
                    // TODO: Unfuck this up.

                    $this->loginView->showLoginSuccess($user);
                    $loginHTML = $this->loginView->getWelcomeHTML();
                    $this->loginView->setSessionCredentials($user);
                } catch (\Exception $ex) {
                    $loginHTML = $this->loginView->showLoginFailed();
                }
            } else {
                $loginHTML = $this->loginView->getFormHTML();
            }
        } else {
            $loginHTML = $this->loginView->getFormHTML();
        }

        $dateHTML = $this->dateTimeView->getHTML();
        print $this->appView->getHTML($loginHTML, $dateHTML);

    }
}
