<?php
use \WebGuy;

// UC1: https://docs.google.com/document/d/1kaAxV02vO2GlNgHmxEFMik_iYvLQMpErTtCifht9-Uc/edit?pli=1#heading=h.qwf701bxn3r6
class AuthenticateUserCest
{

    public function _before()
    {
        // TODO: Remove cookies
    }

    public function _after()
    {

    }

    // TC1.1
    public function navigateToPage(WebGuy $I)
    {
        $I->wantTo('See the front page');
        $I->amOnPage('/');

        $I->see('PHPLogin');
        $I->see('Klockan är');
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

}
