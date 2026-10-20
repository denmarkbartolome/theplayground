<?php

namespace Controllers;

use Core\BaseController;
use Core\Auth;
use Models\Customers;

class CustomersController extends BaseController
{
    private $Customers;

    public function __construct()
    {
        $this->Customers = new Customers();
    }

    /**
     * Restrict customer management to admins.
     */
    private function requireAdmin()
    {
        Auth::requireLogin();

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }
    }

    /**
     * Display the customer list.
     */
    public function index()
    {
        $this->requireAdmin();

        $search = trim($_GET['search'] ?? '');
        $filter = $_GET['filter'] ?? 'all';

        if (!in_array($filter, ['all', 'recent'], true)) {
            $filter = 'all';
        }

        $customers = $this->Customers->find($search, $filter);

        $success = $_SESSION['customer_success'] ?? '';
        $error = $_SESSION['customer_error'] ?? '';

        unset(
            $_SESSION['customer_success'],
            $_SESSION['customer_error']
        );

        $this->view('Customers/index', [
            'title' => '',
            'customers' => $customers,
            'search' => $search,
            'filter' => $filter,
            'success' => $success,
            'error' => $error
        ]);
    }

    /**
     * Add a customer account.
     */
    public function create()
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/Customers');
            exit;
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (
            $firstName === '' ||
            $lastName === '' ||
            $email === '' ||
            $password === ''
        ) {
            $_SESSION['customer_error'] =
                'Please complete all required fields.';
        } elseif (
            strlen($firstName) > 100 ||
            strlen($lastName) > 100 ||
            strlen($email) > 255
        ) {
            $_SESSION['customer_error'] =
                'One or more fields exceed the allowed length.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['customer_error'] =
                'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $_SESSION['customer_error'] =
                'Password must be at least 8 characters.';
        } elseif ($this->Customers->emailExists($email)) {
            $_SESSION['customer_error'] =
                'This email address is already registered.';
        } else {
            try {
                $this->Customers->create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password' => $password
                ]);

                $_SESSION['customer_success'] =
                    'Customer account created successfully.';
            } catch (\PDOException $e) {
                $_SESSION['customer_error'] =
                    'Unable to create the customer account. Please check the details and try again.';
            }
        }

        header('Location: ' . BASE_URL . '/Customers');
        exit;
    }
}
