<?php
$today = (new DateTime(
    'now',
    new DateTimeZone('Asia/Manila')
))->format('Y-m-d');

$calendarSessions = [];

foreach ($sessions as $session) {
    $calendarSessions[] = [
        'id' => (int) $session->id,
        'date' => $session->session_date,
        'start_time' => $session->start_time,
        'end_time' => $session->end_time,
        'capacity' => (int) $session->capacity,
        'available_slots' => max(0, (int) $session->available_slots)
    ];
}
?>

<div class="container-fluid">

    <!-- Alerts -->
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-md-8">
            <h1>Open Play</h1>
            <p class="text-muted">
                Choose a session date and reserve your spot.
            </p>
        </div>

        <div class="col-md-4 text-md-right">
            <a href="<?= BASE_URL ?>/myopreservations"
               class="btn btn-outline-primary">
                <i class="fas fa-calendar-check mr-1"></i>
                My O.P Reservations
            </a>
        </div>
    </div>

    <form action="<?= BASE_URL ?>/OpenPlay/store"
          method="POST"
          id="openPlayForm">

        <input type="hidden" name="session_id" id="session_id" required>

        <div class="row">

            <!-- LEFT COLUMN -->
            <div class="col-lg-8">

                <!-- CALENDAR -->
                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            1. Choose a Date
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <button type="button"
                                    class="btn btn-default"
                                    id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <h5 class="mb-0 font-weight-bold"
                                id="monthLabel"></h5>

                            <button type="button"
                                    class="btn btn-default"
                                    id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>

                        </div>

                        <div class="booking-calendar">

                            <div class="calendar-grid calendar-weekdays">
                                <div>Sun</div>
                                <div>Mon</div>
                                <div>Tue</div>
                                <div>Wed</div>
                                <div>Thu</div>
                                <div>Fri</div>
                                <div>Sat</div>
                            </div>

                            <div class="calendar-grid" id="calendarDays"></div>

                        </div>

                        <div class="mt-3">
                            <span class="badge badge-success">&nbsp;</span>
                            Available

                            <span class="badge badge-primary ml-3">&nbsp;</span>
                            Selected

                            <span class="badge badge-light ml-3">Unavailable</span>
                        </div>

                        <p class="mt-3 mb-0">
                            Selected date:
                            <strong id="selectedDateLabel">Choose a date</strong>
                        </p>

                    </div>
                </div>

                <!-- SELECTED SESSION -->
                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-2"></i>
                            2. Session Details
                        </h3>
                    </div>

                    <div class="card-body">

                        <div id="noSessionSelected" class="text-muted">
                            Select an available date on the calendar to view
                            session details.
                        </div>

                        <div id="sessionDetails" style="display:none;">

                            <div class="row">

                                <div class="col-md-6">
                                    <p class="mb-1 text-muted">Session Date</p>
                                    <h5 id="sessionDate"></h5>
                                </div>

                                <div class="col-md-6">
                                    <p class="mb-1 text-muted">Session Time</p>
                                    <h5 id="sessionTime">5:00 PM – 11:00 PM</h5>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <p class="mb-1 text-muted">Available Slots</p>
                                    <h5 id="availableSlots"></h5>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <p class="mb-1 text-muted">Price Per Player</p>
                                    <h5 class="text-success">₱200.00</h5>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- PAYMENT -->
                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-wallet mr-2"></i>
                            3. Payment Details
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="alert alert-info">
                            <strong>
                                <i class="fas fa-info-circle mr-1"></i>
                                Payment Instructions
                            </strong>

                            <p class="mb-2">
                                Pay ₱200.00 through GCash or Maya before submitting
                                your reservation.
                            </p>

                            <p class="mb-0">
                                Enter your payment transaction reference.
                                Staff will verify your payment before confirming
                                your reservation.
                            </p>

                            <!-- Add your actual GCash/Maya account details here. -->
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>

                            <select name="payment_method"
                                    id="payment_method"
                                    class="form-control"
                                    required>

                                <option value="">Select payment method</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>

                            </select>
                        </div>

                        <div class="form-group">
                            <label for="payment_reference">
                                Transaction Reference Number
                            </label>

                            <input type="text"
                                   name="payment_reference"
                                   id="payment_reference"
                                   class="form-control"
                                   maxlength="150"
                                   placeholder="Enter payment reference"
                                   required>
                        </div>

                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: RESERVATION SUMMARY -->
            <div class="col-lg-4">

                <div class="card card-success card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-receipt mr-2"></i>
                            Reservation Summary
                        </h3>
                    </div>

                    <div class="card-body">

                        <p>
                            Date:
                            <strong id="summaryDate">Not selected</strong>
                        </p>

                        <p>
                            Session:
                            <strong>5:00 PM – 11:00 PM</strong>
                        </p>

                        <p>
                            Players:
                            <strong>1</strong>
                        </p>

                        <hr>

                        <p>
                            Price per player:
                            <strong>₱200.00</strong>
                        </p>

                        <hr>

                        <h4 class="text-success">
                            Total:
                            <span>₱200.00</span>
                        </h4>

                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Your reservation remains pending until staff
                            verifies your payment.
                        </div>

                        <button type="submit"
                                id="submitReservation"
                                class="btn btn-success btn-lg btn-block"
                                disabled>
                            <i class="fas fa-check-circle mr-1"></i>
                            Submit Reservation
                        </button>

                        <p class="small text-muted mt-2 mb-0">
                            Select an available date and complete your
                            payment details to continue.
                        </p>

                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<style>
.booking-calendar {
    width: 100%;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 6px;
}

.calendar-weekdays {
    text-align: center;
    font-weight: 600;
    margin-bottom: 8px;
}

.calendar-day {
    min-height: 52px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #fff;
    color: #343a40;
    cursor: pointer;
    font-weight: 500;
}

.calendar-day:hover:not(:disabled) {
    border-color: #007bff;
    background: #eaf3ff;
}

.calendar-day.available {
    border-color: #28a745;
    background: #f0fff4;
}

.calendar-day.selected {
    background: #007bff;
    border-color: #007bff;
    color: #fff;
}

.calendar-day:disabled {
    background: #f1f1f1;
    color: #aaa;
    cursor: not-allowed;
    border-color: #e4e4e4;
}

@media (max-width: 575px) {
    .calendar-grid {
        gap: 3px;
    }

    .calendar-day {
        min-height: 42px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sessions = <?= json_encode(
        $calendarSessions,
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    ) ?>;

    const today = '<?= $today ?>';

    const sessionMap = {};

    sessions.forEach(function (session) {
        sessionMap[session.date] = session;
    });

    const todayParts = today.split('-').map(Number);

    let viewYear = todayParts[0];
    let viewMonth = todayParts[1] - 1;
    let selectedSession = null;

    const calendarDays = document.getElementById('calendarDays');
    const monthLabel = document.getElementById('monthLabel');
    const previousButton = document.getElementById('prevMonth');
    const nextButton = document.getElementById('nextMonth');

    const sessionIdInput = document.getElementById('session_id');
    const selectedDateLabel = document.getElementById('selectedDateLabel');
    const summaryDate = document.getElementById('summaryDate');

    const noSessionSelected = document.getElementById('noSessionSelected');
    const sessionDetails = document.getElementById('sessionDetails');

    const sessionDate = document.getElementById('sessionDate');
    const sessionTime = document.getElementById('sessionTime');
    const availableSlots = document.getElementById('availableSlots');

    const paymentMethod = document.getElementById('payment_method');
    const paymentReference = document.getElementById('payment_reference');
    const submitButton = document.getElementById('submitReservation');
    const form = document.getElementById('openPlayForm');

    function pad(number) {
        return String(number).padStart(2, '0');
    }

    function dateKey(year, month, day) {
        return year + '-' + pad(month + 1) + '-' + pad(day);
    }

    function formatDate(key) {
        const parts = key.split('-').map(Number);

        return new Date(parts[0], parts[1] - 1, parts[2])
            .toLocaleDateString('en-PH', {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
    }

    function isPastDate(key) {
        return key < today;
    }

    function renderCalendar() {

        calendarDays.innerHTML = '';

        const firstDay = new Date(viewYear, viewMonth, 1);
        const numberOfDays = new Date(
            viewYear,
            viewMonth + 1,
            0
        ).getDate();

        const startingDay = firstDay.getDay();

        monthLabel.textContent = firstDay.toLocaleDateString('en-PH', {
            month: 'long',
            year: 'numeric'
        });

        previousButton.disabled =
            viewYear < todayParts[0] ||
            (
                viewYear === todayParts[0] &&
                viewMonth <= todayParts[1] - 1
            );

        for (let i = 0; i < startingDay; i++) {
            const blank = document.createElement('div');
            calendarDays.appendChild(blank);
        }

        for (let day = 1; day <= numberOfDays; day++) {

            const key = dateKey(viewYear, viewMonth, day);
            const date = new Date(viewYear, viewMonth, day);
            const weekday = date.getDay();

            const eligibleWeekday = [1, 3, 6].includes(weekday);
            const session = sessionMap[key];

            const available = session &&
                Number(session.available_slots) > 0;

            const past = isPastDate(key);
            const selectable = !past && eligibleWeekday && Boolean(available);

            const button = document.createElement('button');

            button.type = 'button';
            button.className = 'calendar-day btn btn-block';
            button.textContent = day;

            if (past || !selectable) {
                button.disabled = true;
            }

            if (selectable) {
                button.classList.add('available');
                button.title = 'Open Play available';

                if (
                    selectedSession &&
                    selectedSession.date === key
                ) {
                    button.classList.add('selected');
                }

                button.addEventListener('click', function () {
                    selectSession(session);
                });
            } else {
                button.title = past
                    ? 'Past dates cannot be selected'
                    : (session && !available
                        ? 'Session is full'
                        : 'No Open Play session available');
            }

            calendarDays.appendChild(button);
        }
    }

    function selectSession(session) {

        if (!session || isPastDate(session.date)) {
            return;
        }

        if (Number(session.available_slots) <= 0) {
            return;
        }

        selectedSession = session;
        sessionIdInput.value = session.id;

        const formattedDate = formatDate(session.date);

        selectedDateLabel.textContent = formattedDate;
        summaryDate.textContent = formattedDate;
        sessionDate.textContent = formattedDate;

        sessionTime.textContent =
            formatTime(session.start_time) +
            ' – ' +
            formatTime(session.end_time);

        availableSlots.textContent =
            session.available_slots +
            ' of ' +
            session.capacity +
            ' slots available';

        noSessionSelected.style.display = 'none';
        sessionDetails.style.display = 'block';

        renderCalendar();
        updateSubmitButton();
    }

    function formatTime(time) {
        const parts = time.split(':');
        const date = new Date();

        date.setHours(Number(parts[0]), Number(parts[1] || 0), 0, 0);

        return date.toLocaleTimeString('en-PH', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    function updateSubmitButton() {

        submitButton.disabled = !(
            selectedSession &&
            !isPastDate(selectedSession.date) &&
            Number(selectedSession.available_slots) > 0 &&
            sessionIdInput.value !== '' &&
            paymentMethod.value !== '' &&
            paymentReference.value.trim() !== ''
        );
    }

    previousButton.addEventListener('click', function () {

        if (previousButton.disabled) {
            return;
        }

        viewMonth--;

        if (viewMonth < 0) {
            viewMonth = 11;
            viewYear--;
        }

        renderCalendar();
    });

    nextButton.addEventListener('click', function () {

        viewMonth++;

        if (viewMonth > 11) {
            viewMonth = 0;
            viewYear++;
        }

        renderCalendar();
    });

    paymentMethod.addEventListener('change', updateSubmitButton);
    paymentReference.addEventListener('input', updateSubmitButton);

    form.addEventListener('submit', function (event) {

        if (
            !selectedSession ||
            isPastDate(selectedSession.date) ||
            Number(selectedSession.available_slots) <= 0 ||
            sessionIdInput.value === '' ||
            paymentMethod.value === '' ||
            paymentReference.value.trim() === ''
        ) {
            event.preventDefault();

            alert(
                'Please select an available future Open Play date and complete your payment details.'
            );
        }
    });

    renderCalendar();
    updateSubmitButton();

});
</script>
