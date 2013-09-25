<?php
use \WebGuy;

// Use Cases: https://docs.google.com/document/d/1kaAxV02vO2GlNgHmxEFMik_iYvLQMpErTtCifht9-Uc/edit?pli=1#heading=h.qwf701bxn3r6
class TestCasesCest
{

    public function _before()
    {
        // Delete all cookies
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
    }

    public function _after()
    {

    }

    // TC1.1
    public function navigateToPage(WebGuy $I)
    {
        $I->wantTo('See the normal login page');
        $I->amOnPage('/');

        $I->dontSee('Användarnamn saknas');
        $I->dontSee('Lösenord saknas');
        $I->dontSee('är inloggad');
        $I->seeElement('input'); // I see a form
        $I->see('Klockan är'); // Tests only if it's visible, not how it's formatted
        $I->see('Ej Inloggad');
    }

    // TC1.2
    public function failedLoginWithoutCredentials(WebGuy $I)
    {
        $I->wantTo('Fail to log in without entering anything');
        $I->amOnPage('/');

        $I->click('Logga in');

        $I->see('Ej Inloggad');
        $I->see('Användarnamn saknas');
    }

    // TC1.3
    public function failedLoginWithOnlyUsename(WebGuy $I)
    {
        $I->wantTo('Fail to log in when only entering username');
        $I->amOnPage('/');

        $I->fillField('UserNameID', 'Admin');
        $I->click('Logga in');

        $I->see('Ej Inloggad');
        $I->see('Lösenord saknas');
        $I->seeInField('UserNameID','Admin');
    }

    // TC1.4
    public function failedLoginWithOnlyPassword(WebGuy $I)
    {
        $I->wantTo('Fail to log in when only entering password');
        $I->amOnPage('/');

        $I->fillField('PasswordID', 'Password');
        $I->click('Logga in');

        $I->see('Ej Inloggad');
        $I->see('Användarnamn saknas');
    }

    // TC1.5
    public function failedLoginWithWrongCredentials(WebGuy $I)
    {
        $I->wantTo('Fail to log in when entering wrong username and password');
        $I->amOnPage('/');

        $I->fillField('UserNameID', 'Admin');
        $I->fillField('PasswordID', 'notcorrect');
        $I->click('Logga in');

        $I->see('Felaktigt användarnamn och/eller lösenord');
        $I->seeInField('UserNameID','Admin');
        $I->see('Ej Inloggad');
    }

    // TC1.6
    public function failedLoginWithWrongUsername(WebGuy $I)
    {
        $I->wantTo('Fail to log in when entering wrong username but correct password');
        $I->amOnPage('/');

        $I->fillField('UserNameID', 'Admina');
        $I->fillField('PasswordID', 'Password');
        $I->click('Logga in');

        $I->see('Felaktigt användarnamn och/eller lösenord');
        $I->seeInField('UserNameID','Admina');
        $I->see('Ej Inloggad');
    }

    // Helper method used in multiple tests.
    // This is TC1.7 without actually asserting anything
    private function logInWithCredentials(WebGuy $I)
    {
        $I->amOnPage('/');

        $I->fillField('UserNameID', 'Admin');
        $I->fillField('PasswordID', 'Password');
        $I->click('Logga in');
    }

    // TC1.7
    public function successfulLoginWithCredentials(WebGuy $I)
    {
        $I->wantTo('Log in with the right credentials');
        $this->logInWithCredentials($I);

        $I->see('Inloggning lyckades');
        $I->see('Admin är inloggad');
        $I->seeLink('Logga ut', '?logout');
    }

    // TC1.8
    public function loggedInAfterRefresh(WebGuy $I)
    {
        $this->logInWithCredentials($I);

        // Refresh the page
        $I->amOnPage('/');

        $I->dontSee('Inloggning lyckades');
        $I->seeLink('Logga ut', '?logout');
        $I->see('Admin är inloggad');
    }

    // TODO: Separate this into its own cest

    // TC2.1
    public function logOut(WebGuy $I)
    {
        $this->logInWithCredentials($I);

        $I->click('Logga ut');

        $I->see('Du har nu loggat ut');
        $I->see('Användarnamn'); // TODO: Check that there's a form, not very robust but it works
        $I->see('Lösenord');
        $I->see('Ej Inloggad');
    }

    // TC2.2
    public function logOutByClosingBrowser(WebGuy $I)
    {
        $this->logInWithCredentials($I);

        // TODO: Simulate closing browser

        $this->navigateToPage($I);

    }

    // TC2.3
    public function loggedOutAfterRefresh(WebGuy $I)
    {
        $this->logInWithCredentials($I);
        $I->click('Logga ut');

         // Refresh
        $I->amOnPage('/');

        $I->dontSee('Du har nu loggat ut');
        $I->seeElement('input');
        $I->see('Ej Inloggad');
    }

    // TODO: Write the rest of the tests

}
