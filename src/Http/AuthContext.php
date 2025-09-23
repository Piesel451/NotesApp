<?php
namespace App\Http;

class AuthContext
{
    private static $user = null;

    public static function setUser($auth)
    {
        self::$user = $auth;
    }

    public static function getUser()
    {
        return self::$user;
    }
}
?>