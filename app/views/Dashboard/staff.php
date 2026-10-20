<?php
$name = $name ?? 'Staff';
$pendingCount = (int)($pendingCount ?? 0);
$paymentCount = (int)($paymentCount ?? 0);
$todayApprovedCount = (int)($todayApprovedCount ?? 0);
$recentBookings = $recentBookings ?? [];

$escape = static function ($value): string {
    return htmlspecialchars(
        (string)($value ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
};
?>

<section class="content-header">
    <div class="container-fluid">
        
        <p class="text-muted">
            Welcome back, <?= $escape($name) ?>!
            Here's your booking operations overview.
        </p>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-4 col-md-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $pendingCount ?></h3>
                        <p>Pending Bookings</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="<?= BASE_URL ?>/allbookings"
                       class="small-box-footer">
                        Review Bookings
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $paymentCount ?></h3>
                        <p>Online Payments to Verify</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <a href="<?= BASE_URL ?>/allbookings"
                       class="small-box-footer">
                        Verify Payments
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $todayApprovedCount ?></h3>
                        <p>Today's Approved Bookings</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <a href="<?= BASE_URL ?>/allbookings"
                       class="small-box-footer">
                        View Bookings
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <h4>
                            <i class="fas fa-calendar-alt text-primary mr-2"></i>
                            Normal Court Bookings
                        </h4>
                        <p class="text-muted">
                            Review booking requests, verify payments,
                            and approve or reject reservations.
                        </p>
                        <a href="<?= BASE_URL ?>/bookings"
                           class="btn btn-primary">
                            Manage Bookings
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-body">
                        <h4>
                            <i class="fas fa-users text-success mr-2"></i>
                            Open Play Reservations
                        </h4>
                        <p class="text-muted">
                            Review player reservations and manage
                            Open Play session registrations.
                        </p>
                        <a href="<?= BASE_URL ?>/openplayreservations"
                           class="btn btn-success">
                            Manage Open Play
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-1"></i>
                    Recent Court Bookings
                </h3>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Court</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($recentBookings)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No court bookings found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentBookings as $booking): ?>
                            <?php
                            $status = strtolower(
                                $booking['status'] ?? 'pending'
                            );

                            $classes = [
                                'pending' => 'badge-warning',
                                'approved' => 'badge-success',
                                'rejected' => 'badge-danger',
                                'cancelled' => 'badge-secondary',
                                'completed' => 'badge-info'
                            ];
                            ?>
                            <tr>
                                <td>#<?= (int)$booking['id'] ?></td>
                                <td>
                                    <?= $escape(
                                        ($booking['first_name'] ?? '') . ' ' .
                                        ($booking['last_name'] ?? '')
                                    ) ?>
                                </td>
                                <td>
                                    <?= $escape($booking['court_name'] ?? '—') ?>
                                </td>
                                <td>
                                    <?= $escape($booking['booking_date'] ?? '—') ?>
                                </td>
                                <td>
                                    ₱<?= number_format(
                                        (float)($booking['total_amount'] ?? 0),
                                        2
                                    ) ?>
                                </td>
                                <td>
                                    <?= $escape(
                                        ucfirst($booking['payment_status'] ?? 'unpaid')
                                    ) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $classes[$status] ?? 'badge-secondary' ?>">
                                        <?= $escape(ucfirst($status)) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-right">
                <a href="<?= BASE_URL ?>/bookings"
                   class="btn btn-outline-primary btn-sm">
                    View All Bookings
                </a>
            </div>
        </div>

    </div>
</section>
