<?php

namespace Controllers;

use Core\BaseController;
use Core\Auth;
use Models\Bookings;

class MyBookingsController extends BaseController
{
    private $Bookings;

    public function __construct()
    {
        $this->Bookings = new Bookings();
    }

    public function index()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        if ($role === 'admin' || $role === 'staff') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $userId = $_SESSION['user']['id'] ?? null;

        $bookings = $this->Bookings->findByUserID($userId);

        $this->view('mybookings/index', [
            'title' => '',
            'bookings' => $bookings
        ]);
    }
}
