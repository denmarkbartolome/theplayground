<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3>My Bookings</h3>
            <p class="text-muted">
                View your court reservations and payment details.
            </p>
        </div>

        <a href="<?= BASE_URL ?>/Booking" class="btn btn-primary">
            <i class="fas fa-plus"></i> Book a Court
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>

        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                <h5>No bookings yet</h5>
                <p class="text-muted">
                    Your reservations will appear here after you book a court.
                </p>
                <a href="<?= BASE_URL ?>/Booking" class="btn btn-primary">
                    Book Your First Court
                </a>
            </div>
        </div>

    <?php else: ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Your Reservations</h3>
            </div>

            <div class="card-body">

                <!-- Search -->
                <div class="row mb-3">
                    <div class="col-md-6 col-lg-4">
                        <div class="input-group">
                            <input
                                type="text"
                                id="bookingSearch"
                                class="form-control"
                                placeholder="Search bookings..."
                                autocomplete="off"
                            >
                            <div class="input-group-append">
                                <button
                                    type="button"
                                    class="btn btn-default"
                                    onclick="document.getElementById('bookingSearch').value=''; document.getElementById('bookingSearch').dispatchEvent(new Event('input'));"
                                    title="Clear search"
                                >
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover"
                           id="bookingsTable">

                        <thead>
                            <tr>
                                
                                <th>Court</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Booking Status</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <?php
                            $status = $booking['status'];

                            $badgeClass = [
                                'pending'   => 'badge-warning',
                                'approved'  => 'badge-success',
                                'rejected'  => 'badge-danger',
                                'cancelled' => 'badge-secondary'
                            ][$status] ?? 'badge-secondary';
                            ?>

                            <tr class="booking-row">
                                

                                <td>
                                    <?= htmlspecialchars($booking['court_name']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($booking['booking_date']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(substr($booking['start_time'], 0, 5)) ?>
                                    -
                                    <?= htmlspecialchars(substr($booking['end_time'], 0, 5)) ?>
                                </td>

                                <td>
                                    ₱<?= number_format(
                                        (float) $booking['total_amount'],
                                        2
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        ucfirst($booking['payment_method'])
                                    ) ?>

                                    <br>

                                    <small>
                                        Status:
                                        <?= htmlspecialchars(
                                            ucfirst($booking['payment_status'])
                                        ) ?>
                                    </small>
                                </td>

                                <td>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars(ucfirst($status)) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

                <!-- Results information and pagination -->
                <div class="d-flex flex-column flex-md-row
                            justify-content-between align-items-center mt-3">

                    <small class="text-muted mb-2 mb-md-0"
                           id="bookingInfo"></small>

                    <nav aria-label="Booking pagination">
                        <ul class="pagination pagination-sm mb-0"
                            id="bookingPagination"></ul>
                    </nav>

                </div>

                <div id="noSearchResults"
                     class="text-center text-muted py-4"
                     style="display:none;">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p class="mb-0">No matching bookings found.</p>
                </div>

            </div>
        </div>

    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('bookingSearch');
    const table = document.getElementById('bookingsTable');

    if (!searchInput || !table) return;

    const rows = Array.from(
        table.querySelectorAll('tbody .booking-row')
    );

    const pagination = document.getElementById('bookingPagination');
    const info = document.getElementById('bookingInfo');
    const noResults = document.getElementById('noSearchResults');

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
            ? 'Showing 0 bookings'
            : `Showing ${start + 1}-${end} of ${total} bookings`;

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

    renderTable();
});
</script>