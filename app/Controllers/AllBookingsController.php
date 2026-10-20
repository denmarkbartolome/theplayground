<?php

namespace Controllers;

use Core\BaseController;
use Core\Auth;
use Models\Bookings;

class AllBookingsController extends BaseController
{
    private $Bookings;

    public function __construct()
    {
        $this->Bookings = new Bookings();
    }

    
public function confirmFacilityPayment()
{
    Auth::requireLogin();

    $role = $_SESSION['user']['role'] ?? '';

    // Only authorized staff and admins can confirm payments.
    if (!in_array($role, ['admin', 'staff'], true)) {
        header('Location: ' . BASE_URL . '/Dashboard');
        exit;
    }

    // Accept POST requests only.
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    $bookingId = filter_input(
        INPUT_POST,
        'booking_id',
        FILTER_VALIDATE_INT
    );

    if (!$bookingId || $bookingId < 1) {
        $_SESSION['error'] = 'Invalid booking ID.';
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    try {
        // Record payment received at the facility.
        $updated = $this->Bookings->confirmFacilityPayment(
            $bookingId
        );

        if ($updated) {
            $_SESSION['success'] =
                'Facility payment recorded successfully. You can now approve the booking.';
        } else {
            $_SESSION['error'] =
                'Payment could not be recorded. The booking may already be paid, or it is not eligible for payment confirmation.';
        }
    } catch (\PDOException $e) {
        error_log($e->getMessage());

        $_SESSION['error'] =
            'An error occurred while recording the facility payment.';
    }

    header('Location: ' . BASE_URL . '/AllBookings');
    exit;
}

    
    public function confirmPayment()
{
    Auth::requireLogin();

    $role = $_SESSION['user']['role'] ?? '';

    if (!in_array($role, ['admin', 'staff'], true)) {
        header('Location: ' . BASE_URL . '/Dashboard');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    $bookingId = filter_input(
        INPUT_POST,
        'booking_id',
        FILTER_VALIDATE_INT
    );

    if (!$bookingId || $bookingId < 1) {
        $_SESSION['error'] = 'Invalid booking ID.';
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    // Staff must verify the actual GCash transaction before this call.
    $updated = $this->Bookings->confirmPayment($bookingId);

    if ($updated) {
        $_SESSION['success'] =
            'Payment verified successfully. You can now approve the booking.';
    } else {
        $_SESSION['error'] =
            'Payment could not be updated. It may already be processed, or the booking is not eligible.';
    }

    header('Location: ' . BASE_URL . '/AllBookings');
    exit;
}


public function confirm()
{
    Auth::requireLogin();

    $role = $_SESSION['user']['role'] ?? '';

    if (!in_array($role, ['admin', 'staff'], true)) {
        header('Location: ' . BASE_URL . '/Dashboard');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    $bookingId = filter_input(
        INPUT_POST,
        'booking_id',
        FILTER_VALIDATE_INT
    );

    $staffId = $_SESSION['user']['id'] ?? null;

    if (!$bookingId || $bookingId < 1 || !$staffId) {
        $_SESSION['error'] = 'Invalid booking information.';
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    try {
        $confirmed = $this->Bookings->confirmBooking(
            $bookingId,
            $staffId
        );

        if ($confirmed) {
            $_SESSION['success'] = 'Booking approved successfully.';
        } else {
            $_SESSION['error'] =
                'Booking could not be approved. It may already be processed, or online payment has not been verified.';
        }
    } catch (\PDOException $e) {
        error_log($e->getMessage());

        $_SESSION['error'] =
            'Unable to approve the booking. Please check whether the court schedule conflicts with another booking.';
    }

    header('Location: ' . BASE_URL . '/AllBookings');
    exit;
}


public function reject()
{
    Auth::requireLogin();

    $role = $_SESSION['user']['role'] ?? '';

    if (!in_array($role, ['admin', 'staff'], true)) {
        header('Location: ' . BASE_URL . '/Dashboard');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    $bookingId = filter_input(
        INPUT_POST,
        'booking_id',
        FILTER_VALIDATE_INT
    );

    $staffId = $_SESSION['user']['id'] ?? null;

    $reason = trim($_POST['rejection_reason'] ?? 'No Payment');

    if (!$bookingId || $bookingId < 1 || !$staffId) {
        $_SESSION['error'] = 'Invalid booking information.';
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    if ($reason === '' || strlen($reason) > 500) {
        $_SESSION['error'] =
            'Please provide a rejection reason of up to 500 characters.';
        header('Location: ' . BASE_URL . '/AllBookings');
        exit;
    }

    try {
        $rejected = $this->Bookings->rejectBooking(
            $bookingId,
            $staffId,
            $reason
        );

        if ($rejected) {
            $_SESSION['success'] = 'Booking rejected successfully.';
        } else {
            $_SESSION['error'] =
                'Booking could not be rejected. It may already have been processed.';
        }
    } catch (\PDOException $e) {
        error_log($e->getMessage());

        $_SESSION['error'] =
            'An error occurred while rejecting the booking.';
    }

    header('Location: ' . BASE_URL . '/AllBookings');
    exit;
}


    public function index()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        if (!in_array($role, ['admin', 'staff'], true)) {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $bookings = $this->Bookings->findAllBookings();

        $this->view('allbookings/index', [
            'title' => '',
            'bookings' => $bookings
        ]);
    }
}
