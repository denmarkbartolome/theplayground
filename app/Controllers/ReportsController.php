<?php

namespace Controllers;

use Core\BaseController;
use Core\Auth;
use Models\Bookings;
use Models\OpenPlay;
use DateTimeImmutable;

class ReportsController extends BaseController
{
    public function index()
    {
        Auth::requireLogin();

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }

        $period = $_GET['period'] ?? 'daily';

        if (!in_array($period, ['daily', 'weekly', 'monthly'], true)) {
            $period = 'daily';
        }

        $today = date('Y-m-d');
        $selectedDate = $_GET['date'] ?? $today;
        $selectedMonth = $_GET['month'] ?? date('Y-m');

        // Validate the supplied dates.
        $dateObject = DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $selectedDate
        );

        if (
            !$dateObject ||
            $dateObject->format('Y-m-d') !== $selectedDate
        ) {
            $selectedDate = $today;
            $dateObject = new DateTimeImmutable($today);
        }

        $monthObject = DateTimeImmutable::createFromFormat(
            '!Y-m',
            $selectedMonth
        );

        if (
            !$monthObject ||
            $monthObject->format('Y-m') !== $selectedMonth
        ) {
            $selectedMonth = date('Y-m');
            $monthObject = new DateTimeImmutable(
                $selectedMonth . '-01'
            );
        }

        switch ($period) {
            case 'weekly':
                // Monday through Sunday.
                $startDate = $dateObject
                    ->modify('monday this week')
                    ->format('Y-m-d');

                $endDate = $dateObject
                    ->modify('monday this week')
                    ->modify('+6 days')
                    ->format('Y-m-d');

                break;

            case 'monthly':
                $startDate = $monthObject
                    ->modify('first day of this month')
                    ->format('Y-m-d');

                $endDate = $monthObject
                    ->modify('last day of this month')
                    ->format('Y-m-d');

                break;

            default:
                $startDate = $selectedDate;
                $endDate = $selectedDate;
                break;
        }

        $bookingsModel = new Bookings();
        $openPlayModel = new OpenPlay();

        $bookingRevenue = $bookingsModel->getRevenueForPeriod(
            $startDate,
            $endDate
        );

        $openPlayRevenue = $openPlayModel->getRevenueForPeriod(
            $startDate,
            $endDate
        );

        $courtTotal = (float) $bookingRevenue['court_revenue'];
        $addonTotal = (float) $bookingRevenue['addon_revenue'];
        $openPlayTotal = (float) $openPlayRevenue['open_play_revenue'];

        $totalRevenue = $courtTotal + $addonTotal + $openPlayTotal;

        $this->view('Reports/index', [
            'title' => '',
            'period' => $period,
            'selectedDate' => $selectedDate,
            'selectedMonth' => $selectedMonth,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'courtTotal' => $courtTotal,
            'addonTotal' => $addonTotal,
            'openPlayTotal' => $openPlayTotal,
            'totalRevenue' => $totalRevenue,
            'paidBookings' => (int) $bookingRevenue['paid_bookings'],
            'confirmedReservations' =>
                (int) $openPlayRevenue['confirmed_reservations']
        ]);
    }
}
