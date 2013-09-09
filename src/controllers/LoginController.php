<?php

namespace controllers;

use models\LoginModel;
use models\UserDatabaseModel as UserDAO;
use models\UserModel;
use views\LoginView;
use views\DateTimeView;
use views\UserView;

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

    // Views
    private $loginView;
    private $dateTimeView;

    public function __construct()
    {

        // Create instances
        $this->loginView = new LoginView();
        $this->dateTimeView = new DateTimeView('sv_SE');
    }

    public function logIn()
    {
        $this->handleInput();

        $output = '';

        $output .= $this->loginView->getHTML();
        $output .= $this->dateTimeView->getHTML();

        return $output;
    }

    private function handleInput()
    {
        // When ?login is hit
        if ($this->loginView->userWantsToLogIn()) {

            // Validate form
            if ($this->loginView->validateFormInput()) {

                // Authorize user
                try {
                    $user = UserModel::authorizeUser($this->loginView->getPostUserName(), $this->loginView->getPostPassword());
                } catch (\Exception $ex) {
                    $this->loginView->showValidationError($ex->getMessage());
                }
            }


        }
    }

}
