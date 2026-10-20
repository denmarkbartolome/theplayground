<?php

namespace Controllers;

use Core\BaseController;
use Core\Auth;
use Models\Bookings;

class DashboardController extends BaseController
{
    public function index(): void
    {
        Auth::requireLogin();

        $role = $_SESSION['user']['role'] ?? '';
        $name = $_SESSION['user']['first_name'] ?? 'Guest';

        // Keep the existing admin dashboard.
        if ($role === 'admin') {
    $bookingModel = new \Models\Bookings();
    $allBookings = $bookingModel->find();

    $today = date('Y-m-d');

    $totalBookings = count($allBookings);
    $pendingCount = 0;
    $approvedCount = 0;
    $todayBookings = 0;
    $totalRevenue = 0;
    $customerIds = [];

    foreach ($allBookings as $booking) {
        $status = strtolower($booking['status'] ?? '');

        if ($status === 'pending') {
            $pendingCount++;
        }

        if ($status === 'approved') {
            $approvedCount++;

            // This is an approved-booking total, not necessarily
            // collected revenue. Use payment records/status for
            // a true revenue figure.
            $totalRevenue += (float) ($booking['total_amount'] ?? 0);
        }

        if (($booking['booking_date'] ?? '') === $today) {
            $todayBookings++;
        }

        if (isset($booking['user_id'])) {
            $customerIds[$booking['user_id']] = true;
        }
    }

    $this->view('Dashboard/admin', [
        'title' => 'Admin Dashboard',
        'name' => $name,
        'totalBookings' => $totalBookings,
        'pendingCount' => $pendingCount,
        'approvedCount' => $approvedCount,
        'todayBookings' => $todayBookings,
        'totalRevenue' => $totalRevenue,
        'customerCount' => count($customerIds),
        'recentBookings' => array_slice($allBookings, 0, 5)
    ]);

    return;
}

        // Keep the existing staff dashboard.
        if ($role === 'staff') {
    $bookingModel = new \Models\Bookings();

    $allBookings = $bookingModel->findAllBookings();

    $today = date('Y-m-d');
    $pendingCount = 0;
    $paymentCount = 0;
    $todayApprovedCount = 0;

    foreach ($allBookings as $booking) {
        $status = strtolower($booking['status'] ?? '');
        $paymentStatus = strtolower(
            $booking['payment_status'] ?? ''
        );

        if ($status === 'pending') {
            $pendingCount++;
        }

        if (
            $status === 'pending' &&
            $booking['payment_method'] === 'online' &&
            $paymentStatus === 'pending'
        ) {
            $paymentCount++;
        }

        if (
            ($booking['booking_date'] ?? '') === $today &&
            $status === 'approved'
        ) {
            $todayApprovedCount++;
        }
    }

    $this->view('Dashboard/staff', [
        'title' => '',
        'name' => $name,
        'pendingCount' => $pendingCount,
        'paymentCount' => $paymentCount,
        'todayApprovedCount' => $todayApprovedCount,
        'recentBookings' => array_slice($allBookings, 0, 5)
    ]);

    return;
}

        // Load customer booking data.
        if ($role === 'customer') {
            $bookingModel = new Bookings();

            $bookings = $bookingModel->findByUserID(
                (int) ($_SESSION['user']['id'] ?? 0)
            );

            $today = date('Y-m-d');

            $upcomingCount = 0;
            $pendingCount = 0;
            $approvedCount = 0;

            foreach ($bookings as $booking) {
                $status = strtolower($booking['status'] ?? '');
                $date = $booking['booking_date'] ?? '';

                if ($status === 'pending') {
                    $pendingCount++;
                }

                if ($status === 'approved') {
                    $approvedCount++;
                }

                if (
                    $date >= $today &&
                    in_array($status, ['pending', 'approved'], true)
                ) {
                    $upcomingCount++;
                }
            }

            // Show the five most recently created bookings.
            $recentBookings = array_slice($bookings, 0, 5);

            $this->view('Dashboard/index', [
                'title' => '',
                'name' => $name,
                'upcomingCount' => $upcomingCount,
                'pendingCount' => $pendingCount,
                'approvedCount' => $approvedCount,
                'recentBookings' => $recentBookings
            ]);

            return;
        }

        http_response_code(403);
        exit('Access denied.');
    }
}
