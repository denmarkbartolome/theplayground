<?php

namespace Controllers;

use Core\BaseController;
use Core\Auth;
use Models\OpenPlay;

class OpenPlayReservationsController extends BaseController
{
    private $OpenPlay;

    public function __construct()
    {
        $this->OpenPlay = new OpenPlay();
    }

    /**
     * Allow only staff and admin users.
     */
    private function requireStaffAccess()
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';

        if (!in_array($role, ['staff', 'admin'], true)) {
            http_response_code(403);
            exit('Access denied.');
        }
    }

    /**
     * Display all Open Play reservations for verification.
     */
    public function index()
    {
        $this->requireStaffAccess();

        $reservations = $this->OpenPlay->getAllReservations();

        $this->view('opreservations/index', [
            'title' => '',
            'reservations' => $reservations
        ]);
    }

    /**
     * Confirm a customer's payment and reservation.
     */
    public function confirm()
    {
        $this->requireStaffAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/OpenPlayReservations');
            exit;
        }

        $reservationId = filter_input(
            INPUT_POST,
            'reservation_id',
            FILTER_VALIDATE_INT
        );

        $staffId = $_SESSION['user']['id'] ?? null;

        if (!$reservationId || !$staffId) {
            $_SESSION['error'] = 'Invalid reservation or staff account.';
            header('Location: ' . BASE_URL . '/OpenPlayReservations');
            exit;
        }

        try {
            $success = $this->OpenPlay->confirmReservation(
                $reservationId,
                $staffId,
                trim($_POST['staff_notes'] ?? '') ?: null
            );

            if ($success) {
                $_SESSION['success'] =
                    'Payment verified and Open Play reservation confirmed.';
            } else {
                $_SESSION['error'] =
                    'This reservation has already been processed or no longer exists.';
            }
        } catch (\Throwable $e) {
            error_log('Open Play confirmation error: ' . $e->getMessage());
            $_SESSION['error'] =
                'Unable to confirm the reservation. Please try again.';
        }

        header('Location: ' . BASE_URL . '/OpenPlayReservations');
        exit;
    }

    /**
     * Reject a customer's payment and release the reserved spot.
     */
    public function reject()
    {
        $this->requireStaffAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/OpenPlayReservations');
            exit;
        }

        $reservationId = filter_input(
            INPUT_POST,
            'reservation_id',
            FILTER_VALIDATE_INT
        );

        $staffId = $_SESSION['user']['id'] ?? null;
        $reason = trim($_POST['staff_notes'] ?? '');

        if (!$reservationId || !$staffId) {
            $_SESSION['error'] = 'Invalid reservation or staff account.';
            header('Location: ' . BASE_URL . '/OpenPlayReservations');
            exit;
        }

        if ($reason === '') {
            $_SESSION['error'] = 'Please provide a reason for rejecting the payment.';
            header('Location: ' . BASE_URL . '/OpenPlayReservations');
            exit;
        }

        try {
            $success = $this->OpenPlay->rejectReservation(
                $reservationId,
                $staffId,
                $reason
            );

            if ($success) {
                $_SESSION['success'] =
                    'Payment rejected. The reservation spot has been released.';
            } else {
                $_SESSION['error'] =
                    'This reservation has already been processed or no longer exists.';
            }
        } catch (\Throwable $e) {
            error_log('Open Play rejection error: ' . $e->getMessage());
            $_SESSION['error'] =
                'Unable to reject the reservation. Please try again.';
        }

        header('Location: ' . BASE_URL . '/OpenPlayReservations');
        exit;
    }
}
