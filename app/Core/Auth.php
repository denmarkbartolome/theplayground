<?php

namespace Core;

class Auth{

    public static function check()
    {
        return !empty($_SESSION['user']);
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function requireLogin()
    {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '/Auth');
            exit;
        }
    }

    public static function requireAdmin()
    {
        self::requireLogin();

        if ((self::user()['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Forbidden');
        }
    }
}
