<?php
$name = $name ?? 'Guest';
$upcomingCount = $upcomingCount ?? 0;
$pendingCount = $pendingCount ?? 0;
$approvedCount = $approvedCount ?? 0;
$recentBookings = $recentBookings ?? [];

$escape = static function ($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
};
?>

<div class="content-header">
    <div class="container-fluid">
        
        <p class="text-muted mb-0">
            Welcome back, <?= $escape($name) ?>!
            Ready for your next game?
        </p>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        <!-- Booking statistics -->
        <div class="row">

            <div class="col-lg-4 col-md-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= (int)$upcomingCount ?></h3>
                        <p>Upcoming Bookings</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <a href="<?= BASE_URL ?>/mybookings"
                       class="small-box-footer">
                        View My Bookings
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= (int)$pendingCount ?></h3>
                        <p>Pending Requests</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="<?= BASE_URL ?>/mybookings"
                       class="small-box-footer">
                        Check Request Status
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= (int)$approvedCount ?></h3>
                        <p>Approved Bookings</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="<?= BASE_URL ?>/mybookings"
                       class="small-box-footer">
                        View Approved Bookings
                        <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        </div>

        <!-- Quick actions -->
        <div class="row">

            <div class="col-md-6">
                <div class="card card-primary card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-calendar-plus fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h4>Book a Court</h4>
                                <p class="text-muted mb-2">
                                    Choose your court, date, and available time.
                                </p>
                                <a href="<?= BASE_URL ?>/booking"
                                   class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i>
                                    Make a Booking
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-success card-outline h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-users fa-3x text-success"></i>
                            </div>
                            <div>
                                <h4>Join Open Play</h4>
                                <p class="text-muted mb-2">
                                    Find a scheduled session and reserve your player slot.
                                </p>
                                <a href="<?= BASE_URL ?>/openplay"
                                   class="btn btn-success">
                                    <i class="fas fa-table-tennis mr-1"></i>
                                    Explore Open Play
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent bookings -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-1"></i>
                    Recent Bookings
                </h3>

                <div class="card-tools">
                    <a href="<?= BASE_URL ?>/mybookings"
                       class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
            </div>

            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Court</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (empty($recentBookings)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                <p class="mb-2">You don't have any bookings yet.</p>
                                <a href="<?= BASE_URL ?>/booking"
                                   class="btn btn-primary btn-sm">
                                    Book Your First Court
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($recentBookings as $booking): ?>
                            <?php
                            $status = strtolower(
                                $booking['status'] ?? 'pending'
                            );

                            $statusClasses = [
                                'pending' => 'badge-warning',
                                'approved' => 'badge-success',
                                'rejected' => 'badge-danger',
                                'cancelled' => 'badge-secondary',
                                'completed' => 'badge-info'
                            ];

                            $statusClass = $statusClasses[$status]
                                ?? 'badge-secondary';

                            $startTime = !empty($booking['start_time'])
                                ? date(
                                    'g:i A',
                                    strtotime($booking['start_time'])
                                )
                                : '—';

                            $endTime = !empty($booking['end_time'])
                                ? date(
                                    'g:i A',
                                    strtotime($booking['end_time'])
                                )
                                : '—';
                            ?>

                            <tr>
                                <td>#<?= (int)$booking['id'] ?></td>

                                <td>
                                    <?= $escape($booking['court_name'] ?? 'Court') ?>
                                </td>

                                <td>
                                    <?php if (!empty($booking['booking_date'])): ?>
                                        <?= $escape(
                                            date(
                                                'M d, Y',
                                                strtotime($booking['booking_date'])
                                            )
                                        ) ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= $escape($startTime) ?>
                                    -
                                    <?= $escape($endTime) ?>
                                </td>

                                <td>
                                    ₱<?= number_format(
                                        (float)($booking['total_amount'] ?? 0),
                                        2
                                    ) ?>
                                </td>

                                <td>
                                    <span class="badge <?= $escape($statusClass) ?>">
                                        <?= $escape(ucfirst($status)) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>
