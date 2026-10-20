<?php
$totalBookings = $totalBookings ?? 0;
$pendingCount = $pendingCount ?? 0;
$approvedCount = $approvedCount ?? 0;
$todayBookings = $todayBookings ?? 0;
$totalRevenue = $totalRevenue ?? 0;
$customerCount = $customerCount ?? 0;
$recentBookings = $recentBookings ?? [];

function adminEscape($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<div class="container-fluid">

    <!-- Summary Cards -->
    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= number_format($totalBookings) ?></h3>
                    <p>Total Bookings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <a href="<?= BASE_URL ?>/allbookings" class="small-box-footer">
                    Manage Bookings <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= number_format($pendingCount) ?></h3>
                    <p>Pending Bookings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="<?= BASE_URL ?>/allbookings" class="small-box-footer">
                    Review Bookings <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>₱<?= number_format($totalRevenue, 2) ?></h3>
                    <p>Approved Booking Value</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="<?= BASE_URL ?>/reports" class="small-box-footer">
                    View Reports <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?= number_format($customerCount) ?></h3>
                    <p>Customers With Bookings</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="<?= BASE_URL ?>/customers" class="small-box-footer">
                    View Customers <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Daily Overview -->
    <div class="row">
        <div class="col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-secondary">
                    <i class="fas fa-calendar-day"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Bookings Today</span>
                    <span class="info-box-number">
                        <?= number_format($todayBookings) ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="info-box">
                <span class="info-box-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Approved Bookings</span>
                    <span class="info-box-number">
                        <?= number_format($approvedCount) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt mr-1"></i> Quick Actions
            </h3>
        </div>

        <div class="card-body">
            <a href="<?= BASE_URL ?>/users" class="btn btn-primary m-1">
                <i class="fas fa-user-cog mr-1"></i> Manage Users
            </a>

            <a href="<?= BASE_URL ?>/customers" class="btn btn-info m-1">
                <i class="fas fa-users mr-1"></i> Customers
            </a>

            <a href="<?= BASE_URL ?>/courts" class="btn btn-success m-1">
                <i class="fas fa-table-tennis mr-1"></i> Manage Courts
            </a>

            <a href="<?= BASE_URL ?>/allbookings" class="btn btn-warning m-1">
                <i class="fas fa-calendar-check mr-1"></i> Normal Bookings
            </a>

            <a href="<?= BASE_URL ?>/openplayreservations" class="btn btn-secondary m-1">
                <i class="fas fa-running mr-1"></i> Open Play Reservations
            </a>

            <a href="<?= BASE_URL ?>/reports" class="btn btn-dark m-1">
                <i class="fas fa-chart-line mr-1"></i> Revenue Reports
            </a>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-1"></i> Recent Bookings
            </h3>

            <div class="card-tools">
                <a href="<?= BASE_URL ?>/allbookings" class="btn btn-sm btn-primary">
                    View All
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Court</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($recentBookings)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No bookings found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentBookings as $booking): ?>
                        <?php
                        $status = strtolower($booking['status'] ?? 'pending');

                        $badgeClass = match ($status) {
                            'approved' => 'success',
                            'pending' => 'warning',
                            'rejected', 'cancelled' => 'danger',
                            default => 'secondary'
                        };

                        $customerName = trim(
                            ($booking['first_name'] ?? '') . ' ' .
                            ($booking['last_name'] ?? '')
                        );
                        ?>

                        <tr>
                            <td>#<?= adminEscape($booking['id'] ?? '') ?></td>
                            <td>
                                <?= adminEscape($customerName ?: 'Unknown') ?>
                            </td>
                            <td>
                                <?= adminEscape($booking['court_name'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <?= adminEscape($booking['booking_date'] ?? 'N/A') ?>
                            </td>
                            <td>
                                ₱<?= number_format(
                                    (float) ($booking['total_amount'] ?? 0),
                                    2
                                ) ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $badgeClass ?>">
                                    <?= adminEscape(ucfirst($status)) ?>
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
