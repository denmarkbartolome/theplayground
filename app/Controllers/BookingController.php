<?php

namespace Controllers;

use Core\BaseController;
use Models\Bookings;
use Core\Auth;

class BookingController extends BaseController
{
    private $Bookings;

    public function __construct()
    {
        $this->Bookings = new Bookings();
    }

    // Customer booking page
    public function index()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        // Admins should use their own dashboard
        if ($role === 'admin') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $courts = $this->Bookings->getCourts();

        $this->view('Bookings/index', [
            'title' => '',
            'courts' => $courts
        ]);
    }

    // Return unavailable time slots for the selected court and date
    public function availability()
    {
        Auth::requireLogin();

        if (($_SESSION['user']['role'] ?? '') === 'admin') {
            http_response_code(403);
            exit('Access denied.');
        }

        header('Content-Type: application/json');

        $courtId = filter_input(INPUT_GET, 'court_id', FILTER_VALIDATE_INT);
        $date = $_GET['date'] ?? '';

        if (!$courtId || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid court or date.']);
            return;
        }

        echo json_encode(
            $this->Bookings->getUnavailable($courtId, $date)
        );
    }

    // Save a customer booking
    public function store()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        if ($role === 'admin') {
            http_response_code(403);
            exit('Access denied.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/Booking');
            exit;
        }

        try {
            // Never accept user_id from the submitted form.
            $userId = $_SESSION['user']['id'] ?? null;

            if (!$userId) {
                throw new \RuntimeException('Your session has expired. Please log in again.');
            }

            $data = [
                'user_id' => $userId,
                'court_id' => $_POST['court_id'] ?? null,
                'booking_date' => $_POST['booking_date'] ?? '',
                'start_time' => $_POST['start_time'] ?? '',
                'end_time' => $_POST['end_time'] ?? '',
                'has_own_paddle' => $_POST['has_own_paddle'] ?? '1',
                'paddle_quantity' => $_POST['paddle_quantity'] ?? 0,
                'payment_method' => $_POST['payment_method'] ?? '',
                'payment_reference' => trim($_POST['payment_reference'] ?? ''),
                'customer_notes' => trim($_POST['customer_notes'] ?? '')
            ];
             
            $bookingId = $this->Bookings->save($data);

            $_SESSION['success'] = 'Booking submitted successfully. It is now pending staff review.';

            header('Location: ' . BASE_URL . '/mybookings');
            exit;

        } catch (\Throwable $e) {
            $_SESSION['error'] = $e->getMessage();

            header('Location: ' . BASE_URL . '/Booking');
            exit;
        }
    }

    // Show the logged-in customer's bookings
    public function myBookings()
    {
        Auth::requireLogin();

        if (($_SESSION['user']['role'] ?? '') === 'admin') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $userId = $_SESSION['user']['id'] ?? null;

        $bookings = $this->Bookings->findByUserID($userId);

        $this->view('Bookings/myBookings', [
            'title' => 'My Bookings',
            'bookings' => $bookings
        ]);
    }
}
