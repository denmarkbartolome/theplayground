<?php

namespace Controllers;

use Core\BaseController;
use Core\Auth;
use Models\OpenPlay;

class MyOPReservationsController extends BaseController
{
    private $OpenPlay;

    public function __construct()
    {
        $this->OpenPlay = new OpenPlay();
    }

    /**
     * Display the logged-in customer's Open Play reservations.
     */
    public function index()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        // Only customers and staff who are permitted by your app
        // should access this customer-facing reservation page.
        if ($role === 'admin') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header('Location: ' . BASE_URL . '/Login');
            exit;
        }

        $reservations = $this->OpenPlay->getCustomerReservations($userId);

        $this->view('myopreservations/index', [
            'title' => '',
            'reservations' => $reservations
        ]);
    }
}
