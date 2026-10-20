<?php

namespace Controllers;

use Core\BaseController;
use Models\Users;

class AuthController extends BaseController
{
    private $Users;

    public function __construct()
    {
        $this->Users = new Users();
    }

    public function index()
    {
        if (count($this->Users->find()) === 0) {
            $this->redirect(BASE_URL . '/Users/register');
        }

        $this->view('Auth/index', ['title' => 'Login'], null);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $_SESSION['error'] = 'Email and password are required.';
            $this->redirect(BASE_URL . '/Auth');
        }

        $user = $this->Users->findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            $_SESSION['error'] = 'Invalid email or password.';
            $this->redirect(BASE_URL . '/Auth');
        }

        $_SESSION['user'] = [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->role
        ];

        $this->redirect(BASE_URL . '/Dashboard');
    }
    

    public function logout()
    {
        session_destroy();
        $this->redirect(BASE_URL . '/Auth');
    }
}