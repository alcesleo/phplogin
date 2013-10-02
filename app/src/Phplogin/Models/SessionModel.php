<?php

namespace Phplogin\Models;

use Phplogin\Exceptions\NotFoundException;

/**
 * An interface to the $_SESSION variable that adds security and convenience
 */
class SessionModel
{
    /**
     * The key in session in which to store security data
     * @var string
     */
    protected static $remoteAddrKey = 'SessionModel::Security::IP';
    protected static $userAgentKey = 'SessionModel::Security::UA';

    public static function start()
    {
        session_start();
    }

    /**
     * Save data to help verify the sessions validity
     */
    public static function saveSecurityData()
    {
        $_SESSION[self::$remoteAddrKey] = $_SERVER['REMOTE_ADDR'];
        $_SESSION[self::$userAgentKey] = $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Validates the session to prevent session theft
     * @return boolean true if it checks out
     */
    public static function isSecurityValidated()
    {
        if (! self::isSecurityDataSet()) {
            return false;
        }
        if ($_SESSION[self::$remoteAddrKey] != $_SERVER['REMOTE_ADDR']) {
            return false;
        }
        if ($_SESSION[self::$userAgentKey] != $_SERVER['HTTP_USER_AGENT']) {
            return false;
        }
        return true;
    }

    /**
     * If security data has been saved
     * @return boolean
     */
    private static function isSecurityDataSet()
    {
        return isset($_SESSION[self::$remoteAddrKey]) && isset($_SESSION[self::$userAgentKey]);
    }

    /**
     * Delete a session variable
     * @param  string $key
     */
    public static function delete($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * If key is used
     * @param  string $key
     * @return boolean
     */
    public static function exists($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @param  mixed $obj       any instance of a class
     * @param  string $key location to store in
     */
    public static function saveObject($obj, $key)
    {
        $_SESSION[$key] = serialize($obj);
    }

    /**
     * [getObject description]
     * @param  string $key
     * @return mixed       saved object
     * @throws NotFoundException If nothing is stored in $key
     */
    public static function getObject($key)
    {
        if (! isset($_SESSION[$key])) {
            throw new NotFoundException();
        }
        return unserialize($_SESSION[$key]);
    }
}
