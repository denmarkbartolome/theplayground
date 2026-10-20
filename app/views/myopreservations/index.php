<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>My O.P Reservations</h3>
            <p class="text-muted">
                View your Open Play reservations and payment details.
            </p>
        </div>

        <a href="<?= BASE_URL ?>/OpenPlay" class="btn btn-primary">
            <i class="fas fa-plus"></i> Reserve Open Play
        </a>
    </div>

    <!-- Success Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($reservations)): ?>

        <!-- Empty State -->
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                <h5>No Open Play reservations yet</h5>
                <p class="text-muted">
                    Your Open Play reservations will appear here after you submit one.
                </p>
                <a href="<?= BASE_URL ?>/OpenPlay" class="btn btn-primary">
                    Make Your First Reservation
                </a>
            </div>
        </div>

    <?php else: ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Open Play Reservations</h3>
            </div>

            <div class="card-body">

                <!-- Search -->
                <div class="row mb-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input
                                type="text"
                                id="opSearch"
                                class="form-control"
                                placeholder="Search reservations..."
                                autocomplete="off"
                            >
                            <div class="input-group-append">
                                <button
                                    type="button"
                                    class="btn btn-default"
                                    id="clearOpSearch"
                                    title="Clear search"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reservations Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover"
                           id="opReservationsTable">

                        <thead>
                            <tr>
                                <th>Session Date</th>
                                <th>Time</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Payment Status</th>
                                <th>Reservation Status</th>
                                <th>Transaction Reference</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($reservations as $reservation): ?>

                            <?php
                            // Support the stdClass objects returned by PDO.
                            $paymentStatus = $reservation->payment_status ?? '';
                            $reservationStatus = $reservation->reservation_status ?? '';

                            $paymentBadge = [
                                'pending_verification' => 'badge-warning',
                                'verified'             => 'badge-success',
                                'rejected'             => 'badge-danger'
                            ][$paymentStatus] ?? 'badge-secondary';

                            $reservationBadge = [
                                'pending'   => 'badge-warning',
                                'confirmed' => 'badge-success',
                                'rejected'  => 'badge-danger',
                                'cancelled' => 'badge-secondary'
                            ][$reservationStatus] ?? 'badge-secondary';

                            $paymentLabels = [
                                'pending_verification' => 'Pending Verification',
                                'verified'             => 'Verified',
                                'rejected'             => 'Rejected'
                            ];

                            $reservationLabels = [
                                'pending'   => 'Pending',
                                'confirmed' => 'Confirmed',
                                'rejected'  => 'Rejected',
                                'cancelled' => 'Cancelled'
                            ];

                            $sessionDate = $reservation->session_date ?? '';
                            $startTime = $reservation->start_time ?? '';
                            $endTime = $reservation->end_time ?? '';
                            ?>

                            <tr class="op-reservation-row">

                                <td>
                                    <?= htmlspecialchars($sessionDate) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(substr((string) $startTime, 0, 5)) ?>
                                    -
                                    <?= htmlspecialchars(substr((string) $endTime, 0, 5)) ?>
                                </td>

                                <td>
                                    ₱<?= number_format(
                                        (float) ($reservation->total_amount ?? 200),
                                        2
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        ucfirst($reservation->payment_method ?? '')
                                    ) ?>
                                </td>

                                <td>
                                    <span class="badge <?= $paymentBadge ?>">
                                        <?= htmlspecialchars(
                                            $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus)
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge <?= $reservationBadge ?>">
                                        <?= htmlspecialchars(
                                            $reservationLabels[$reservationStatus] ?? ucfirst($reservationStatus)
                                        ) ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $reservation->payment_reference ?? ''
                                    ) ?>

                                    <?php if (!empty($reservation->staff_notes)): ?>
                                        <br>
                                        <small class="text-muted">
                                            Staff note:
                                            <?= htmlspecialchars($reservation->staff_notes) ?>
                                        </small>
                                    <?php endif; ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

                <!-- Results Information and Pagination -->
                <div class="d-flex flex-column flex-md-row
                            justify-content-between align-items-center mt-3">

                    <small class="text-muted mb-2 mb-md-0"
                           id="opReservationInfo"></small>

                    <nav aria-label="Open Play reservation pagination">
                        <ul class="pagination pagination-sm mb-0"
                            id="opReservationPagination"></ul>
                    </nav>

                </div>

                <div id="noOpSearchResults"
                     class="text-center text-muted py-4"
                     style="display:none;">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p class="mb-0">No matching reservations found.</p>
                </div>

            </div>
        </div>

    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('opSearch');
    const table = document.getElementById('opReservationsTable');

    if (!searchInput || !table) return;

    const rows = Array.from(
        table.querySelectorAll('tbody .op-reservation-row')
    );

    const pagination = document.getElementById('opReservationPagination');
    const info = document.getElementById('opReservationInfo');
    const noResults = document.getElementById('noOpSearchResults');
    const clearButton = document.getElementById('clearOpSearch');

    const perPage = 5;
    let currentPage = 1;
    let filteredRows = [...rows];

    function renderTable() {
        const total = filteredRows.length;
        const totalPages = Math.ceil(total / perPage);

        if (currentPage > totalPages) {
            currentPage = Math.max(1, totalPages);
        }

        rows.forEach(row => {
            row.style.display = 'none';
        });

        const start = (currentPage - 1) * perPage;
        const end = Math.min(start + perPage, total);

        filteredRows.slice(start, end).forEach(row => {
            row.style.display = '';
        });

        info.textContent = total === 0
            ? 'Showing 0 reservations'
            : `Showing ${start + 1}-${end} of ${total} reservations`;

        noResults.style.display = total === 0 ? 'block' : 'none';
        table.style.display = total === 0 ? 'none' : '';

        pagination.innerHTML = '';

        if (totalPages <= 1) return;

        function addPage(label, page, disabled, active) {
            const li = document.createElement('li');

            li.className = 'page-item' +
                (disabled ? ' disabled' : '') +
                (active ? ' active' : '');

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'page-link';
            button.textContent = label;
            button.disabled = disabled;

            button.addEventListener('click', function () {
                currentPage = page;
                renderTable();
            });

            li.appendChild(button);
            pagination.appendChild(li);
        }

        addPage('Previous', currentPage - 1, currentPage === 1, false);

        for (let page = 1; page <= totalPages; page++) {
            addPage(
                String(page),
                page,
                false,
                page === currentPage
            );
        }

        addPage(
            'Next',
            currentPage + 1,
            currentPage === totalPages,
            false
        );
    }

    searchInput.addEventListener('input', function () {
        const query = this.value.trim().toLowerCase();

        filteredRows = rows.filter(row =>
            row.textContent.toLowerCase().includes(query)
        );

        currentPage = 1;
        renderTable();
    });

    clearButton.addEventListener('click', function () {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    });

    renderTable();
});
</script>
