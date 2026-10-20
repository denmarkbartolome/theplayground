<?php

namespace Api\Auth;

use Core\BaseController;
use Models\Users;

class AuthController extends BaseController
{
    private $Users;

    public function __construct()
    {
        $this->Users = new Users();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['status' => 'error', 'message' => 'POST required.']);
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->json(['status' => 'error', 'message' => 'Email and password are required.']);
        }

        $user = $this->Users->findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            $this->json(['status' => 'error', 'message' => 'Invalid email or password.']);
        }

        // Never send the password hash back
        $this->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'role' => $user->role,
            ],
        ]);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['status' => 'error', 'message' => 'POST required.']);
        }

        $data = [
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'role' => 'customer', // forced: the app must never choose its own role
        ];

        if ($data['email'] === '' || $data['password'] === '' ||
            $data['first_name'] === '' || $data['last_name'] === '') {
            $this->json(['status' => 'error', 'message' => 'All fields are required.']);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->json(['status' => 'error', 'message' => 'Invalid email address.']);
        }

        if (strlen($data['password']) < 6) {
            $this->json(['status' => 'error', 'message' => 'Password must be at least 6 characters.']);
        }

        if ($this->Users->emailExists($data['email'])) {
            $this->json(['status' => 'error', 'message' => 'Email is already registered.']);
        }

        if ($this->Users->save($data)) {
            $this->json(['status' => 'success', 'message' => 'Account created. You can log in now.']);
        }

        $this->json(['status' => 'error', 'message' => 'Unable to create account.']);
    }
}