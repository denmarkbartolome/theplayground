<div class="container-fluid">

    <!-- Page Header -->
    <div class="mb-4">
        <h3>Open Play Reservations</h3>
        <p class="text-muted">
            Review Open Play reservations and verify customer payments.
        </p>
    </div>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($reservations)): ?>

        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                <h5>No Open Play reservations found</h5>
                <p class="text-muted mb-0">
                    Customer reservations will appear here when submitted.
                </p>
            </div>
        </div>

    <?php else: ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reservation Verification</h3>
            </div>

            <div class="card-body">

                <!-- Search -->
                <div class="row mb-3">
                    <div class="col-md-6 col-lg-4">
                        <input
                            type="text"
                            id="opStaffSearch"
                            class="form-control"
                            placeholder="Search customer, date, or reference..."
                            autocomplete="off"
                        >
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover"
                           id="opStaffTable">

                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Session</th>
                                <th>Amount</th>
                                <th>Payment Details</th>
                                <th>Status</th>
                                <th style="min-width: 280px;">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($reservations as $reservation): ?>

                            <?php
                            $paymentStatus = $reservation->payment_status ?? '';
                            $reservationStatus = $reservation->reservation_status ?? '';

                            $isPending = (
                                $paymentStatus === 'pending_verification'
                                && $reservationStatus === 'pending'
                            );

                            $paymentBadges = [
                                'pending_verification' => 'badge-warning',
                                'verified' => 'badge-success',
                                'rejected' => 'badge-danger'
                            ];

                            $reservationBadges = [
                                'pending' => 'badge-warning',
                                'confirmed' => 'badge-success',
                                'rejected' => 'badge-danger',
                                'cancelled' => 'badge-secondary'
                            ];

                            $paymentLabels = [
                                'pending_verification' => 'Pending Verification',
                                'verified' => 'Verified',
                                'rejected' => 'Rejected'
                            ];

                            $reservationLabels = [
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'rejected' => 'Rejected',
                                'cancelled' => 'Cancelled'
                            ];
                            ?>

                            <tr class="op-staff-row">

                                <!-- Customer -->
                                <td>
                                    <strong>
                                        <?= htmlspecialchars(
                                            trim(
                                                ($reservation->first_name ?? '') . ' ' .
                                                ($reservation->last_name ?? '')
                                            )
                                        ) ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($reservation->email ?? '') ?>
                                    </small>
                                </td>

                                <!-- Session -->
                                <td>
                                    <?= htmlspecialchars($reservation->session_date ?? '') ?>
                                    <br>
                                    <small>
                                        <?= htmlspecialchars(substr((string) ($reservation->start_time ?? ''), 0, 5)) ?>
                                        -
                                        <?= htmlspecialchars(substr((string) ($reservation->end_time ?? ''), 0, 5)) ?>
                                    </small>
                                </td>

                                <!-- Amount -->
                                <td>
                                    <strong>
                                        ₱<?= number_format(
                                            (float) ($reservation->total_amount ?? 0),
                                            2
                                        ) ?>
                                    </strong>
                                </td>

                                <!-- Payment Details -->
                                <td>
                                    <strong>
                                        <?= htmlspecialchars(
                                            strtoupper($reservation->payment_method ?? '')
                                        ) ?>
                                    </strong>
                                    <br>
                                    <small>
                                        Ref:
                                        <?= htmlspecialchars($reservation->payment_reference ?? '') ?>
                                    </small>
                                </td>

                                <!-- Status -->
                                <td>
                                    <span class="badge <?= $paymentBadges[$paymentStatus] ?? 'badge-secondary' ?>">
                                        <?= htmlspecialchars(
                                            $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus)
                                        ) ?>
                                    </span>

                                    <br>

                                    <span class="badge mt-1 <?= $reservationBadges[$reservationStatus] ?? 'badge-secondary' ?>">
                                        <?= htmlspecialchars(
                                            $reservationLabels[$reservationStatus] ?? ucfirst($reservationStatus)
                                        ) ?>
                                    </span>

                                    <?php if (!empty($reservation->staff_notes)): ?>
                                        <br>
                                        <small class="text-muted">
                                            Note: <?= htmlspecialchars($reservation->staff_notes) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <?php if ($isPending): ?>

                                        <!-- Confirm -->
                                        <form
                                            action="<?= BASE_URL ?>/OpenPlayReservations/confirm"
                                            method="POST"
                                            class="mb-2"
                                            onsubmit="return confirm('Have you verified this payment? Confirm this reservation?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="reservation_id"
                                                value="<?= (int) $reservation->id ?>"
                                            >

                                            <input
                                                type="text"
                                                name="staff_notes"
                                                class="form-control form-control-sm mb-2"
                                                maxlength="1000"
                                                placeholder="Optional confirmation note"
                                            >

                                            <button type="submit"
                                                    class="btn btn-success btn-sm btn-block">
                                                <i class="fas fa-check mr-1"></i>
                                                Confirm Payment
                                            </button>
                                        </form>

                                        <!-- Reject -->
                                        <form
                                            action="<?= BASE_URL ?>/OpenPlayReservations/reject"
                                            method="POST"
                                            onsubmit="return confirm('Reject this payment and release the reservation spot?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="reservation_id"
                                                value="<?= (int) $reservation->id ?>"
                                            >

                                            <textarea
                                                name="staff_notes"
                                                class="form-control form-control-sm mb-2"
                                                rows="2"
                                                maxlength="1000"
                                                placeholder="Reason for rejection (required)"
                                                required
                                            ></textarea>

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm btn-block">
                                                <i class="fas fa-times mr-1"></i>
                                                Reject Payment
                                            </button>
                                        </form>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Processed
                                        </span>

                                    <?php endif; ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="opStaffNoResults"
                     class="text-center text-muted py-4"
                     style="display:none;">
                    No matching reservations found.
                </div>

            </div>
        </div>

    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const search = document.getElementById('opStaffSearch');
    const table = document.getElementById('opStaffTable');
    const noResults = document.getElementById('opStaffNoResults');

    if (!search || !table) return;

    const rows = Array.from(
        table.querySelectorAll('tbody .op-staff-row')
    );

    search.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();
        let visibleCount = 0;

        rows.forEach(function (row) {
            const matches = row.textContent.toLowerCase().includes(query);

            row.style.display = matches ? '' : 'none';

            if (matches) visibleCount++;
        });

        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
        table.style.display = visibleCount === 0 ? 'none' : '';
    });
});
</script>
