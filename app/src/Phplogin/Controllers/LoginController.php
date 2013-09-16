<?php

namespace Phplogin\Controllers;

use Phplogin\Models\LoginModel;
use Phplogin\Models\UserDatabaseModel as UserDAO;
use Phplogin\Models\UserSaverModel;
use Phplogin\Models\UserModel;
use Phplogin\Views\LoginView;
use Phplogin\Views\DateTimeView;
use Phplogin\Views\UserView;
use Phplogin\Views\AppView;

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
        $this->dateTimeView = new DateTimeView('sv_SE.UTF-8', '%A, den %e %B år %Y. Klockan är [%H:%M:%S].');

        // TODO: Should this be passed in as a param?
        $this->appView = new AppView();
    }

    // FIXME: This code is shit
    public function logIn()
    {
        $loginHTML;
        $dateHTML;
        $user;

        // Log in with session
        try {
            $user = $this->userSaverModel->load();

            // FIXME: Copy pasta
            $this->loginView->showLoginSuccess($user);
            $loginHTML = $this->loginView->getWelcomeHTML();
        } catch (\Exception $ex) {
            // Don't give a shit
        }

        // Log in with cookies
        if (! isset($user)) {
            try {
                $user = $this->loginView->getUserFromCookies();

                // FIXME: Copy pasta
                $this->loginView->showLoginSuccess($user);
                $loginHTML = $this->loginView->getWelcomeHTML();

                // Save the user session
                $this->userSaverModel->save($user);
            } catch (\Exception $ex) {
                // Don't give a shit
            }
        }
        // If user has not been set from cookies

        // Log out
        if (isset($user)) {

            if ($this->appView->userWantsToLogOut()) {
                // Log out
                $this->userSaverModel->remove();

                // Unset session and cookie
                $this->loginView->unsetUserCookies();
                $this->loginView->showLogoutSuccess();

                $loginHTML = $this->loginView->getFormHTML();
            }

        // Log in manually
        } else {

            // When ?login is hit
            if ($this->appView->userWantsToLogIn()) {

                // Check session credentials
                // TODO Check this on other screens as well?

                // Validate form
                if ($this->loginView->validateFormInput()) {

                    // Authorize user
                    try {
                        // This can fail
                        $user = UserModel::authorizeUser($this->loginView->getPostUserName(), $this->loginView->getPostPassword(), UserModel::AUTHORIZED_BY_USER);

                        // Stay logged in
                        if ($this->loginView->getPostStayLoggedIn()) {
                            $this->loginView->setUserCookies($user);
                            $this->loginView->showWeWillRememberYou();
                        }

                        $this->loginView->showLoginSuccess($user);
                        $loginHTML = $this->loginView->getWelcomeHTML();


                        // Save the user session
                        $this->userSaverModel->save($user);

                    } catch (\Exception $ex) {
                        $this->loginView->showLoginFailed();
                        $loginHTML = $this->loginView->getFormHTML();
                    }

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
