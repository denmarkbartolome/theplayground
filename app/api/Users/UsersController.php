<?php

namespace Api\Users;

use Core\BaseController;
use Models\Users;

class UsersController extends BaseController
{
    private $Users;

    public function __construct()
    {
        $this->Users = new Users();
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            $this->json([
                'status' => 'error',
                'message' => 'Please log in first.'
            ]);
            exit;
        }

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);
            $this->json([
                'status' => 'error',
                'message' => 'Administrator access required.'
            ]);
            exit;
        }
    }

    private function validateUserData(bool $creating): array
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name'] ?? '');
        $email     = strtolower(trim($_POST['email'] ?? ''));
        $password  = $_POST['password'] ?? '';
        $role      = $_POST['role'] ?? '';

        if (
            $firstName === '' ||
            $lastName === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !in_array($role, ['admin', 'staff', 'customer'], true)
        ) {
            $this->json([
                'status' => 'error',
                'message' => 'Enter valid first name, last name, email, and role.'
            ]);
            exit;
        }

        if ($creating && strlen($password) < 8) {
            $this->json([
                'status' => 'error',
                'message' => 'A password of at least 8 characters is required.'
            ]);
            exit;
        }

        if (!$creating && $password !== '' && strlen($password) < 8) {
            $this->json([
                'status' => 'error',
                'message' => 'A new password must contain at least 8 characters.'
            ]);
            exit;
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
            'role' => $role
        ];
    }

    public function all()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            $this->json([
                'status' => 'error',
                'message' => 'Method not allowed.'
            ]);
            exit;
        }

        $this->json([
            'data' => $this->Users->find()
        ]);
    }

    public function add()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json([
                'status' => 'error',
                'message' => 'Method not allowed.'
            ]);
            exit;
        }

        $data = $this->validateUserData(true);

        if ($this->Users->emailExists($data['email'])) {
            $this->json([
                'status' => 'error',
                'message' => 'That email address is already registered.'
            ]);
            exit;
        }

        try {
            $saved = $this->Users->save($data);

            $this->json([
                'status' => $saved ? 'success' : 'error',
                'message' => $saved
                    ? 'User added successfully.'
                    : 'Unable to add user.'
            ]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());

            http_response_code(500);
            $this->json([
                'status' => 'error',
                'message' => 'Unable to add user. Please check the details.'
            ]);
        }
    }

    public function edit($id)
    {
        $this->requireAdmin();

        $method = $_SERVER['REQUEST_METHOD'];

        if (!in_array($method, ['GET', 'POST'], true)) {
            http_response_code(405);
            $this->json([
                'status' => 'error',
                'message' => 'Method not allowed.'
            ]);
            exit;
        }

        $user = $this->Users->findByID($id);

        if (!$user) {
            http_response_code(404);
            $this->json([
                'status' => 'error',
                'message' => 'User not found.'
            ]);
            exit;
        }

        // GET: return the user details for the Edit modal.
        if ($method === 'GET') {
            $this->json($user);
            return;
        }

        // POST: save the updated user details.
        $data = $this->validateUserData(false);
        $data['id'] = $id;

        if ($this->Users->emailExists($data['email'], $id)) {
            $this->json([
                'status' => 'error',
                'message' => 'That email address belongs to another user.'
            ]);
            exit;
        }

        try {
            $saved = $this->Users->save($data);

            $this->json([
                'status' => $saved ? 'success' : 'error',
                'message' => $saved
                    ? 'User updated successfully.'
                    : 'Unable to update user.'
            ]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());

            http_response_code(500);
            $this->json([
                'status' => 'error',
                'message' => 'Unable to update user.'
            ]);
        }
    }

    public function delete($id)
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            $this->json([
                'status' => 'error',
                'message' => 'Method not allowed.'
            ]);
            exit;
        }

        $currentUserId = (string) ($_SESSION['user']['id'] ?? '');

        if ($currentUserId !== '' && $currentUserId === (string) $id) {
            $this->json([
                'status' => 'error',
                'message' => 'You cannot delete your own account.'
            ]);
            exit;
        }

        if (!$this->Users->findByID($id)) {
            http_response_code(404);
            $this->json([
                'status' => 'error',
                'message' => 'User not found.'
            ]);
            exit;
        }

        try {
            $deleted = $this->Users->delete($id);

            $this->json([
                'status' => $deleted ? 'success' : 'error',
                'message' => $deleted
                    ? 'User deleted successfully.'
                    : 'Unable to delete user.'
            ]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());

            http_response_code(409);
            $this->json([
                'status' => 'error',
                'message' => 'This user may have related bookings or reservations and cannot be deleted.'
            ]);
        }
    }
}
