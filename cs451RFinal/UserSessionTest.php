<?php

require_once "UserSession.php";

use PHPUnit\Framework\TestCase;

class UserSessionTest extends TestCase
{
    public function testIsStudent()
    {
        $session = new UserSession();
        $reflector = new ReflectionClass( 'UserSession');
        $role = $reflector->getProperty( 'role');

        $role->setAccessible('true');
        $role->setValue($session, null);
        self::assertFalse($session->isStudent());

        $role->setValue($session, 1);
        self::assertFalse($session->isStudent());

        $role->setValue($session, 2);
        self::assertTrue($session->isStudent());
    }

    public function testGetID()
    {
        $session = new UserSession();
        $reflector = new ReflectionClass( 'UserSession');
        $id = $reflector->getProperty( 'id');
        $id->setAccessible('true');

        $id->setValue($session, 1);
        self::assertTrue($session->getID() == 1);

        $id->setValue($session, 2);
        self::assertFalse($session->getID() == 1);
    }

    public function testIsAdmin()
    {
        $session = new UserSession();
        $reflector = new ReflectionClass( 'UserSession');
        $role = $reflector->getProperty( 'role');

        $role->setAccessible('true');
        $role->setValue($session, null);
        self::assertFalse($session->isAdmin());

        $role->setValue($session, 1);
        self::assertTrue($session->isAdmin());

        $role->setValue($session, 2);
        self::assertFalse($session->isAdmin());
    }

    public function testGetRole()
    {
        $session = new UserSession();
        $reflector = new ReflectionClass( 'UserSession');
        $role = $reflector->getProperty( 'role');

        $role->setAccessible('true');
        $role->setValue($session, null);
        self::assertTrue($session->getRole() == null);

        $role->setValue($session, 1);
        self::assertTrue($session->getRole() == 1);

        $role->setValue($session, 2);
        self::assertFalse($session->getRole() == 1);
    }

    public function testIsLoggedIn()
    {
        $session = new UserSession();
        $reflector = new ReflectionClass( 'UserSession');
        $role = $reflector->getProperty( 'role');

        $role->setAccessible('true');
        $role->setValue($session, null);
        self::assertFalse($session->isLoggedIn());

        $role->setValue($session, 1);
        self::assertTrue($session->isLoggedIn());
    }


    public function testLogin() {
        $session = new UserSession();
        $reflector = new ReflectionClass('UserSession');
        $login = $reflector->getMethod( 'login');
        $login->setAccessible('true');
        $email = 'tarngp@umkc.edu';
        $password = 'password';
        self::assertTrue($login->invokeArgs($session, array($email, $password)));

    }

    //setters
    public function testClearRequestFilters() {
        $session = new UserSession();
        $session->majorChoice = [0];
        $session->degreeLevelChoice = [0];
        $session->posChoice = [1];
        $session->minimumGpaChoice = "3.5";
        $session->maximumGpaChoice = "4.0";

        $session->clearRequestFilters();
        self::assertEmpty($session->majorChoice);
        self::assertEmpty($session->degreeLevelChoice);
        self::assertEmpty($session->posChoice);
        self::assertEmpty($session->maximumGpaChoice);
        self::assertEmpty($session->minimumGpaChoice);
    }

    //with database test
    public function testAcceptApplication() {
        $session = new UserSession();
        $reflector = new ReflectionClass('UserSession');
        $query = $reflector->getMethod('dbQuery');
        $query->setAccessible('true');
        $session->acceptApplication(6);
        $sql = "SELECT * FROM application WHERE application_id = 6";
        $result = mysqli_fetch_array($query->invokeArgs($session, array($sql)));
        self::assertTrue($result['accepted_flag'] == 1);
        self::assertTrue($result['rejected_flag'] == 0);
    }

    //with database test
    public function testRejectApplication() {

        $session = new UserSession();
        $reflector = new ReflectionClass('UserSession');
        $query = $reflector->getMethod('dbQuery');
        $query->setAccessible('true');
        $session->rejectApplication(6);
        $sql = "SELECT * FROM application WHERE application_id = 6";
        $result = mysqli_fetch_array($query->invokeArgs($session, array($sql)));
        self::assertTrue($result['accepted_flag'] == 0);
        self::assertTrue($result['rejected_flag'] == 1);

    }
}
