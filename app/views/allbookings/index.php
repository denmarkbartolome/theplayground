<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">All Bookings</h3>
            <p class="text-muted mb-0">
                Review reservations and manage booking confirmations.
            </p>
        </div>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="close" data-dismiss="alert"
                    aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="close" data-dismiss="alert"
                    aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php
    $pendingCount = 0;
    $approvedCount = 0;
    $rejectedCount = 0;

    foreach ($bookings as $booking) {
        if ($booking['status'] === 'pending') {
            $pendingCount++;
        } elseif ($booking['status'] === 'approved') {
            $approvedCount++;
        } elseif ($booking['status'] === 'rejected') {
            $rejectedCount++;
        }
    }
    ?>

    <!-- Summary Cards -->
    <div class="row">

    <!-- Total Bookings -->
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= count($bookings) ?></h3>
                <p>Total Bookings</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
    </div>

    <!-- Pending Bookings -->
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $pendingCount ?></h3>
                <p>Pending Bookings</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <!-- Approved Bookings -->
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $approvedCount ?></h3>
                <p>Approved Bookings</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>

    <!-- Rejected Bookings -->
    <div class="col-lg-3 col-md-6 col-sm-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $rejectedCount ?></h3>
                <p>Rejected Bookings</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>

</div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-check mr-2"></i>
                Booking Records
            </h3>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Court & Schedule</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Booking Status</th>
                        <th style="min-width: 220px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($bookings)): ?>

                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">
                                No bookings found.
                            </p>
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($bookings as $booking): ?>

                        <?php
                        $status = $booking['status'];
                        $paymentStatus = $booking['payment_status'];
                        $paymentMethod = $booking['payment_method'];

                        $statusClasses = [
                            'pending'   => 'badge-warning',
                            'approved'  => 'badge-success',
                            'rejected'  => 'badge-danger',
                            'cancelled' => 'badge-secondary'
                        ];

                        $paymentClasses = [
                            'unpaid'   => 'badge-secondary',
                            'pending'  => 'badge-warning',
                            'paid'     => 'badge-success',
                            'failed'   => 'badge-danger',
                            'refunded' => 'badge-info'
                        ];

                        $canApprove =
                            $status === 'pending'
                            && (
                                $paymentMethod === 'onsite'
                                || $paymentStatus === 'paid'
                            );
                        ?>

                        <tr>
                            <!-- Booking ID -->
                            <td>
                                <strong>
                                    #<?= (int) $booking['id'] ?>
                                </strong>
                            </td>

                            <!-- Customer -->
                            <td>
                                <strong>
                                    <?= htmlspecialchars(
                                        $booking['first_name'] . ' ' .
                                        $booking['last_name']
                                    ) ?>
                                </strong>
                                <br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($booking['email']) ?>
                                </small>
                            </td>

                            <!-- Court and Schedule -->
                            <td>
                                <strong>
                                    <?= htmlspecialchars($booking['court_name']) ?>
                                </strong>
                                <br>
                                <small>
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    <?= htmlspecialchars($booking['booking_date']) ?>
                                </small>
                                <br>
                                <small>
                                    <i class="far fa-clock mr-1"></i>
                                    <?= htmlspecialchars(substr($booking['start_time'], 0, 5)) ?>
                                    -
                                    <?= htmlspecialchars(substr($booking['end_time'], 0, 5)) ?>
                                </small>
                            </td>

                            <!-- Amount -->
                            <td>
                                <strong>
                                    ₱<?= number_format(
                                        (float) $booking['total_amount'],
                                        2
                                    ) ?>
                                </strong>
                                <br>
                                <small class="text-muted">
                                    Paddle quantity:
                                    <?= (int) $booking['paddle_quantity'] ?>
                                </small>
                            </td>

                            <!-- Payment Details -->
                            <td>
                                <?= htmlspecialchars(ucfirst($paymentMethod)) ?>
                                <br>

                                <span class="badge <?= $paymentClasses[$paymentStatus] ?? 'badge-secondary' ?>">
                                    <?= htmlspecialchars(ucfirst($paymentStatus)) ?>
                                </span>

                                <?php if ($paymentMethod === 'online'): ?>
                                    <br>
                                    <small class="text-muted">
                                        Ref:
                                        <?= !empty($booking['payment_reference'])
                                            ? htmlspecialchars($booking['payment_reference'])
                                            : 'Not provided' ?>
                                    </small>
                                <?php endif; ?>
                            </td>

                            <!-- Booking Status -->
                            <td>
                                <span class="badge <?= $statusClasses[$status] ?? 'badge-secondary' ?>">
                                    <?= htmlspecialchars(ucfirst($status)) ?>
                                </span>

                                <?php if (!empty($booking['rejection_reason'])): ?>
                                    <br>
                                    <small class="text-danger">
                                        <?= htmlspecialchars($booking['rejection_reason']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                          
<td>
    <?php if ($status === 'pending'): ?>

        <?php if ($paymentMethod === 'onsite' && $paymentStatus !== 'paid'): ?>

            <!-- Pay on Facility: Confirm Payment First -->
            <div class="text-muted small mb-2">
                <i class="fas fa-exclamation-circle"></i>
                Confirm payment after receiving money from the customer.
            </div>

            <form method="POST"
                  action="<?= BASE_URL ?>/AllBookings/confirmFacilityPayment"
                  class="mb-2"
                  onsubmit="return confirm('Have you received the payment from this customer?');">

                <input type="hidden"
                       name="booking_id"
                       value="<?= (int) $booking['id'] ?>">

                <button type="submit"
                        class="btn btn-info btn-sm btn-block">
                    <i class="fas fa-money-bill-wave"></i>
                    Confirm Payment Received
                </button>
            </form>

        <?php elseif ($paymentMethod === 'online' && $paymentStatus !== 'paid'): ?>

            <!-- Online Payment Verification -->
            <div class="text-muted small mb-2">
                <i class="fas fa-exclamation-circle"></i>
                Verify online payment before approval.
            </div>

            <?php if ($paymentStatus === 'pending'): ?>
                <form method="POST"
                      action="<?= BASE_URL ?>/AllBookings/confirmPayment"
                      class="mb-2"
                      onsubmit="return confirm('Have you verified that this online payment was received?');">

                    <input type="hidden"
                           name="booking_id"
                           value="<?= (int) $booking['id'] ?>">

                    <button type="submit"
                            class="btn btn-info btn-sm btn-block">
                        <i class="fas fa-money-check-alt"></i>
                        Confirm Payment Received
                    </button>
                </form>
            <?php endif; ?>

        <?php else: ?>

            <!-- Payment is confirmed: Approve Booking -->
            <form method="POST"
                  action="<?= BASE_URL ?>/AllBookings/confirm"
                  class="mb-2"
                  onsubmit="return confirm('Approve this booking?');">

                <input type="hidden"
                       name="booking_id"
                       value="<?= (int) $booking['id'] ?>">

                <button type="submit"
                        class="btn btn-success btn-sm btn-block">
                    <i class="fas fa-check"></i>
                    Confirm Booking
                </button>
            </form>

        <?php endif; ?>

        <!-- Reject Booking -->
        <form method="POST"
              action="<?= BASE_URL ?>/allbookings/reject"
              onsubmit="return confirm('Reject this booking?');">

            <input type="hidden"
                   name="booking_id"
                   value="<?= (int) $booking['id'] ?>">

            <input type="text"
                   name="rejection_reason"
                   class="form-control form-control-sm mb-2"
                   placeholder="Reason for rejection"
                   required
                   maxlength="500">

            <button type="submit"
                    class="btn btn-outline-danger btn-sm btn-block">
                <i class="fas fa-times"></i>
                Reject Booking
            </button>
        </form>

    <?php else: ?>

        <span class="text-muted small">
            No actions available
        </span>

    <?php endif; ?>
</td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
