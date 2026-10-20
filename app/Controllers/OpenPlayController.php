<?php

namespace Controllers;

use Core\BaseController;
use Models\OpenPlay;
use Core\Auth;

class OpenPlayController extends BaseController
{
    private $OpenPlay;

    public function __construct()
    {
        $this->OpenPlay = new OpenPlay();
    }

    // Customer Open Play session calendar
    public function index()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        if ($role === 'admin') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $sessions = $this->OpenPlay->getUpcomingSessions();

        $this->view('OpenPlay/index', [
            'title' => '',
            'sessions' => $sessions
        ]);
    }

    // Submit a customer's Open Play reservation
    public function store()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        if ($role === 'admin') {
            http_response_code(403);
            exit('Access denied.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/OpenPlay');
            exit;
        }

        try {
            // Always use the logged-in customer's ID.
            $userId = $_SESSION['user']['id'] ?? null;

            if (!$userId) {
                throw new \RuntimeException(
                    'Your session has expired. Please log in again.'
                );
            }

            $data = [
                'user_id' => $userId,
                'session_id' => filter_input(
                    INPUT_POST,
                    'session_id',
                    FILTER_VALIDATE_INT
                ),
                'payment_method' => $_POST['payment_method'] ?? '',
                'payment_reference' => trim(
                    $_POST['payment_reference'] ?? ''
                )
            ];

            if (!$data['session_id']) {
                throw new \RuntimeException(
                    'Please select a valid Open Play session.'
                );
            }

            $reservationId = $this->OpenPlay->createReservation($data);

            $_SESSION['success'] = 'Open Play reservation submitted successfully. Your payment is pending staff verification.';

            header('Location: ' . BASE_URL . '/myopreservations');
            exit;

        } catch (\Throwable $e) {
            $_SESSION['error'] = $e->getMessage();

            header('Location: ' . BASE_URL . '/OpenPlay');
            exit;
        }
    }

    // Show the logged-in customer's Open Play reservations
    public function myReservations()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        if ($role === 'admin') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $userId = $_SESSION['user']['id'] ?? null;

        $reservations = $this->OpenPlay->getCustomerReservations($userId);

        $this->view('myopreservations/index', [
            'title' => '',
            'reservations' => $reservations
        ]);
    }
}
