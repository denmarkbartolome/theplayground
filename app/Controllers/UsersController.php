<?php

namespace Controllers;

use Core\BaseController;
use Models\Users;
use Core\Auth;

class UsersController extends BaseController
{
    private $Users;
 
    public function __construct()
    {
        $this->Users = new Users();
    }

      public function register()
    {
        // ADDED: Prevent logged-in users from accessing registration
        if (isset($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $this->view('Users/register', [
            'title' => 'Register'
        ], null);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');

        if ($email === '' || $password === '' || $firstName === '' || $lastName === '') {
            $this->json([
                'status' => 'error',
                'message' => 'All required fields must be completed.'
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json([
                'status' => 'error',
                'message' => 'Please enter a valid email address.'
            ]);
        }

        if (strlen($password) < 4) {
            $this->json([
                'status' => 'error',
                'message' => 'Password must contain at least 4 characters.'
            ]);
        }

        if ($password !== $confirm) {
            $this->json([
                'status' => 'error',
                'message' => 'Passwords do not match.'
            ]);
        }

        if ($this->Users->emailExists($email)) {
            $this->json([
                'status' => 'error',
                'message' => 'Email is already registered.'
            ]);
        }

        $this->Users->save([
            'email' => $email,
            'password' => $password,
            'role' => 'customer',
            'first_name' => $firstName,
            'last_name' => $lastName
        ]);

        $_SESSION['success'] = 'Registration successful. You may now log in.';

        $this->redirect(BASE_URL . '/Auth');
    }


    
public function index(): void
{
    $this->requireAdmin();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $this->view('Users/index', [
        'title' => 'Users',
        'users' => $this->Users->find(),
        'csrfToken' => $_SESSION['csrf_token'],
        'success' => $_SESSION['success'] ?? null,
        'error' => $_SESSION['error'] ?? null,
    ]);

    unset($_SESSION['success'], $_SESSION['error']);
}

private function requireAdmin(): void
{
    Auth::requireLogin();

    if (($_SESSION['user']['role'] ?? '') !== 'admin') {
        header('Location: ' . BASE_URL . '/Dashboard');
        exit;
    }
}

private function validateCsrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';

    if (
        !is_string($submitted) ||
        !is_string($stored) ||
        $stored === '' ||
        !hash_equals($stored, $submitted)
    ) {
        http_response_code(403);
        exit('Invalid security token. Please reload the page and try again.');
    }
}

public function create(): void
{
    $this->requireAdmin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method not allowed.');
    }

    $this->validateCsrf();

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $role      = $_POST['role'] ?? 'customer';

    if ($firstName === '' || $lastName === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8 ||
        !in_array($role, ['admin', 'staff', 'customer'], true)) {
        $_SESSION['error'] =
            'Check the required fields. Passwords must contain at least 8 characters.';
        $this->redirect(BASE_URL . '/users');
        return;
    }

    if ($this->Users->emailExists($email)) {
        $_SESSION['error'] = 'That email address is already registered.';
        $this->redirect(BASE_URL . '/users');
        return;
    }

    try {
        $this->Users->save([
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'password'   => $password,
            'role'       => $role,
        ]);

        $_SESSION['success'] = 'User created successfully.';
    } catch (\PDOException $e) {
        $_SESSION['error'] = 'Unable to create the user. Check the email and try again.';
    }

    $this->redirect(BASE_URL . '/users');
}

public function update($id): void
{
    $this->requireAdmin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method not allowed.');
    }

    $this->validateCsrf();

    $user = $this->Users->findByID($id);

    if (!$user) {
        $_SESSION['error'] = 'User not found.';
        $this->redirect(BASE_URL . '/users');
        return;
    }

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = strtolower(trim($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $role      = $_POST['role'] ?? '';

    if ($firstName === '' || $lastName === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !in_array($role, ['admin', 'staff', 'customer'], true) ||
        ($password !== '' && strlen($password) < 8)) {
        $_SESSION['error'] = 'Please check the submitted user details.';
        $this->redirect(BASE_URL . '/users');
        return;
    }

    if ($this->Users->emailExists($email, $id)) {
        $_SESSION['error'] = 'That email address belongs to another user.';
        $this->redirect(BASE_URL . '/users');
        return;
    }

    try {
        $this->Users->save([
            'id'         => $id,
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'email'      => $email,
            'password'   => $password,
            'role'       => $role,
        ]);

        $_SESSION['success'] = 'User updated successfully.';
    } catch (\PDOException $e) {
        $_SESSION['error'] = 'Unable to update the user.';
    }

    $this->redirect(BASE_URL . '/users');
}

public function delete($id): void
{
    $this->requireAdmin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method not allowed.');
    }

    $this->validateCsrf();

    $currentUserId = (string) ($_SESSION['user']['id'] ?? '');

    if ($currentUserId !== '' && $currentUserId === (string) $id) {
        $_SESSION['error'] = 'You cannot delete your own account.';
        $this->redirect(BASE_URL . '/users');
        return;
    }

    try {
        if (!$this->Users->findByID($id)) {
            $_SESSION['error'] = 'User not found.';
        } elseif ($this->Users->delete($id)) {
            $_SESSION['success'] = 'User deleted successfully.';
        } else {
            $_SESSION['error'] = 'Unable to delete the user.';
        }
    } catch (\PDOException $e) {
        $_SESSION['error'] =
            'This user cannot be deleted because related records may still exist.';
    }

    $this->redirect(BASE_URL . '/users');
}



    

}