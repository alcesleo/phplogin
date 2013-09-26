<?php

namespace Phplogin\Models;

class EncryptionModel
{
    /**
     * Encrypt a string
     * @param  string $str the string to be encrypted
     * @return string      the encrypted hash
     */
    public static function encrypt($str)
    {
        return sha1($str);
    }
}
