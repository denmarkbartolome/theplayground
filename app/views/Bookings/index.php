<?php
$today = date('Y-m-d');
?>

<div class="container-fluid">

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/Booking/store"
          method="POST" id="bookingForm">

        <input type="hidden" name="booking_date" id="booking_date" required>
        <input type="hidden" name="court_id" id="court_id" required>
        <input type="hidden" name="start_time" id="start_time" required>
        <input type="hidden" name="end_time" id="end_time" required>

        <div class="row">

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
                            <button type="button" class="btn btn-default"
                                    id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>

                            <h5 class="mb-0 font-weight-bold" id="monthLabel"></h5>

                            <button type="button" class="btn btn-default"
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
                            <span class="badge badge-primary">&nbsp;</span>
                            Selected date
                            <span class="badge badge-light ml-3">Unavailable</span>
                            Past date
                        </div>

                        <p class="mt-3 mb-0">
                            Selected date:
                            <strong id="selectedDateLabel">Choose a date</strong>
                        </p>
                    </div>
                </div>

                <!-- COURTS -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table-tennis mr-2"></i>
                            2. Choose a Court
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="row" id="courtButtons">

                            <?php foreach ($courts as $court): ?>
                                <div class="col-6 col-md-3 mb-2">
                                    <button type="button"
                                            class="btn btn-outline-primary court-button btn-block"
                                            data-court-id="<?= (int) $court['id'] ?>">
                                        <i class="fas fa-table-tennis mb-2"></i>
                                        <br>
                                        <?= htmlspecialchars($court['court_name']) ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

                <!-- TIME SLOTS -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock mr-2"></i>
                            3. Choose a Time
                        </h3>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="duration">Booking Duration</label>
                            <select id="duration" class="form-control" required>
                                <option value="1">1 hour</option>
                                <option value="2">2 hours</option>
                                <option value="3">3 hours</option>
                                <option value="4">4 hours</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <span class="badge badge-success">Available</span>
                            <span class="badge badge-danger ml-2">Unavailable</span>
                            <span class="badge badge-primary ml-2">Selected</span>
                        </div>

                        <div id="timeMessage" class="text-muted mb-3">
                            Choose a date and court to view available times.
                        </div>

                        <div class="row" id="timeSlots"></div>
                    </div>
                </div>

                <!-- PADDLE RENTAL -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">4. Paddle Rental</h3>
                    </div>

                    <div class="card-body">

                        <div class="custom-control custom-radio mb-2">
                            <input class="custom-control-input"
                                   type="radio" id="own_paddle"
                                   name="has_own_paddle" value="1" checked>
                            <label class="custom-control-label" for="own_paddle">
                                I have my own paddle
                            </label>
                        </div>

                        <div class="custom-control custom-radio mb-3">
                            <input class="custom-control-input"
                                   type="radio" id="rent_paddle"
                                   name="has_own_paddle" value="0">
                            <label class="custom-control-label" for="rent_paddle">
                                I need to rent paddles — ₱50 each
                            </label>
                        </div>

                        <div id="paddleQuantityGroup" style="display:none;">
                            <label for="paddle_quantity">Number of paddles</label>
                            <select name="paddle_quantity" id="paddle_quantity"
                                    class="form-control" disabled>
                                <option value="1" selected>1 paddle</option>
                                <option value="2">2 paddles</option>
                                <option value="3">3 paddles</option>
                                <option value="4">4 paddles</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- PAYMENT -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">5. Payment Method</h3>
                    </div>

                    <div class="card-body">

                        <div class="form-group">
                            <label for="payment_method">Choose payment method</label>
                            <select name="payment_method" id="payment_method"
                                    class="form-control" required>
                                <option value="">Select payment method</option>
                                <option value="online">GCash</option>
                                <option value="onsite">Pay at the facility</option>
                            </select>
                        </div>

                        <div id="gcashGroup" class="alert alert-info"
                             style="display:none;">
                            <strong>GCash payment</strong>
                            <p class="mb-2">
                                Send the payment to The Play Ground's GCash account.
                            </p>
                            <p>
                                <strong>GCash number:</strong>
                                09475058229.
                            </p>

                            <label for="payment_reference">
                                Transaction reference number
                            </label>
                            <input type="text" name="payment_reference"
                                   id="payment_reference" maxlength="100"
                                   class="form-control"
                                   placeholder="Enter GCash reference number"
                                   disabled>
                        </div>

                        <div class="form-group">
                            <label for="customer_notes">Notes (optional)</label>
                            <textarea name="customer_notes" id="customer_notes"
                                      class="form-control" rows="3"></textarea>
                        </div>

                    </div>
                </div>

            </div>

            <!-- PRICE SUMMARY -->
            <div class="col-lg-4">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Booking Summary</h3>
                    </div>

                    <div class="card-body">

                        <p>Date: <strong id="summaryDate">Not selected</strong></p>
                        <p>Court: <strong id="summaryCourt">Not selected</strong></p>
                        <p>Time: <strong id="summaryTime">Not selected</strong></p>
                        <p>Duration: <strong id="summaryDuration">1 hour</strong></p>

                        <hr>

                        <p>Hourly rate: <strong id="hourlyRate">—</strong></p>
                        <p>Court total: <strong id="courtTotal">₱0.00</strong></p>
                        <p>Paddle rental: <strong id="paddleTotal">₱0.00</strong></p>

                        <hr>

                        <h4 class="text-success">
                            Total: <span id="grandTotal">₱0.00</span>
                        </h4>

                        <div class="alert alert-warning mt-3">
                            Your booking will be pending until staff review.
                            Pending and approved bookings reserve their time slots.
                        </div>

                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-check-circle mr-1"></i>
                            Submit Booking
                        </button>
                    </div>
                </div>

                <div class="card card-info">
                    <div class="card-body">
                        <h5>Court Rates</h5>
                        <p class="mb-1">8 AM–4 PM: ₱300/hour</p>
                        <p class="mb-1">5 PM–10 PM: ₱500/hour</p>
                        <small class="text-muted">
                            The 4 PM–5 PM period is unavailable until its rate is defined.
                        </small>
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
    min-height: 48px;
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

.calendar-day.selected {
    background: #007bff;
    border-color: #007bff;
    color: #fff;
}

.calendar-day:disabled {
    background: #f1f1f1;
    color: #aaa;
    cursor: not-allowed;
}

.court-button {
    min-height: 85px;
    white-space: normal;
}

.court-button.active {
    background: #007bff;
    color: white;
}

.time-slot {
    min-height: 75px;
    white-space: normal;
}

.time-slot .slot-status {
    display: block;
    font-size: 11px;
    margin-top: 4px;
    font-weight: 700;
}

.time-slot:disabled {
    opacity: 0.75;
    cursor: not-allowed;
}

@media (max-width: 575px) {
    .calendar-grid {
        gap: 3px;
    }

    .calendar-day {
        min-height: 40px;
        font-size: 13px;
    }
}
</style>

<script>
// Waits until jQuery is available, so this works even if the layout
// loads jQuery in the footer (after this view is printed).
(function waitForJquery() {
    if (!window.jQuery) {
        setTimeout(waitForJquery, 50);
        return;
    }

    jQuery(function ($) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        let viewYear = today.getFullYear();
        let viewMonth = today.getMonth();
        let selectedDate = '';
        let selectedCourt = '';
        let selectedCourtName = '';
        let selectedStart = '';
        let bookings = [];

        const $dateField = $('#booking_date');
        const $courtField = $('#court_id');
        const $startField = $('#start_time');
        const $endField = $('#end_time');
        const $durationField = $('#duration');

        const $calendarDays = $('#calendarDays');
        const $monthLabel = $('#monthLabel');
        const $timeSlots = $('#timeSlots');
        const $timeMessage = $('#timeMessage');

        const money = amount => '₱' + amount.toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        function localDateString(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function renderCalendar() {
            $calendarDays.empty();

            $monthLabel.text(
                new Date(viewYear, viewMonth, 1).toLocaleDateString('en-PH', {
                    month: 'long',
                    year: 'numeric'
                })
            );

            const firstDay = new Date(viewYear, viewMonth, 1).getDay();
            const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                $calendarDays.append($('<div>'));
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(viewYear, viewMonth, day);
                const dateString = localDateString(date);

                const $button = $('<button>', {
                    type: 'button',
                    class: 'calendar-day',
                    text: day
                });

                if (date < today) {
                    $button.prop('disabled', true);
                }

                if (dateString === selectedDate) {
                    $button.addClass('selected');
                }

                $button.on('click', function () {
                    selectedDate = dateString;
                    $dateField.val(selectedDate);
                    selectedStart = '';
                    $startField.val('');
                    $endField.val('');

                    $('#selectedDateLabel').text(
                        date.toLocaleDateString('en-PH', {
                            weekday: 'short',
                            month: 'short',
                            day: 'numeric',
                            year: 'numeric'
                        })
                    );

                    $('#summaryDate').text($('#selectedDateLabel').text());

                    renderCalendar();
                    loadAvailability();
                    updateSummary();
                });

                $calendarDays.append($button);
            }
        }

        $('#prevMonth').on('click', function () {
            const previous = new Date(viewYear, viewMonth - 1, 1);
            const currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);

            if (previous >= currentMonth) {
                viewYear = previous.getFullYear();
                viewMonth = previous.getMonth();
                renderCalendar();
            }
        });

        $('#nextMonth').on('click', function () {
            const next = new Date(viewYear, viewMonth + 1, 1);
            viewYear = next.getFullYear();
            viewMonth = next.getMonth();
            renderCalendar();
        });

        $('.court-button').on('click', function () {
            const $this = $(this);

            selectedCourt = $this.data('courtId');
            selectedCourtName = $.trim($this.text());

            $courtField.val(selectedCourt);
            selectedStart = '';
            $startField.val('');
            $endField.val('');

            $('.court-button').removeClass('active');
            $this.addClass('active');

            $('#summaryCourt').text(selectedCourtName);

            loadAvailability();
            updateSummary();
        });

        function loadAvailability() {
            $timeSlots.empty();
            bookings = [];

            if (!selectedDate || !selectedCourt) {
                $timeMessage.text('Choose a date and court to view available times.');
                return;
            }

            $timeMessage.text('Loading availability...');

            $.getJSON('<?= BASE_URL ?>/Booking/availability', {
                court_id: selectedCourt,
                date: selectedDate
            })
                .done(function (data) {
                    bookings = data;
                    renderTimeSlots();
                })
                .fail(function () {
                    $timeMessage.text('Could not load times. Please try again.');
                });
        }

        function renderTimeSlots() {
            $timeSlots.empty();
            selectedStart = '';
            $startField.val('');
            $endField.val('');

            const duration = parseInt($durationField.val(), 10);
            const isToday = localDateString(today) === selectedDate;
            let availableCount = 0;

            $timeMessage.text('Unavailable slots are marked and cannot be selected.');

            // Morning starts: 8 AM through 3 PM.
            // Evening starts: 5 PM through 9 PM.
            const startHours = [8, 9, 10, 11, 12, 13, 14, 15,
                                17, 18, 19, 20, 21];

            $.each(startHours, function (_, hour) {
                const endHour = hour + duration;

                // Keep each booking within its pricing period.
                const validMorning = hour < 16 && endHour <= 16;
                const validEvening = hour >= 17 && endHour <= 22;

                let unavailable = !(validMorning || validEvening);

                if (isToday && hour <= new Date().getHours()) {
                    unavailable = true;
                }

                const start = String(hour).padStart(2, '0') + ':00';
                const end = String(endHour).padStart(2, '0') + ':00';

                const overlaps = bookings.some(function (booking) {
                    const bookedStart = booking.start_time.substring(0, 5);
                    const bookedEnd = booking.end_time.substring(0, 5);

                    return start < bookedEnd && end > bookedStart;
                });

                if (overlaps) {
                    unavailable = true;
                }

                const $column = $('<div>', { class: 'col-6 col-md-4 mb-2' });

                const $button = $('<button>', {
                    type: 'button',
                    class: 'btn btn-block time-slot ' +
                        (unavailable ? 'btn-outline-danger' : 'btn-outline-success'),
                    html: `<strong>${formatTime(hour)} – ${formatTime(endHour)}</strong>` +
                          `<span class="slot-status">${unavailable ? 'UNAVAILABLE' : 'AVAILABLE'}</span>`
                });

                $button.prop('disabled', unavailable);

                if (!unavailable) {
                    availableCount++;

                    $button.on('click', function () {
                        $('.time-slot').each(function () {
                            const $slot = $(this);

                            if (!$slot.prop('disabled')) {
                                $slot.removeClass('btn-primary')
                                     .addClass('btn-outline-success');
                                $slot.find('.slot-status').text('AVAILABLE');
                            }
                        });

                        $button.removeClass('btn-outline-success')
                               .addClass('btn-primary');
                        $button.find('.slot-status').text('SELECTED');

                        selectedStart = start;
                        $startField.val(start);
                        $endField.val(end);

                        updateSummary();
                    });
                }

                $timeSlots.append($column);
                $column.append($button);
            });

            if (availableCount === 0) {
                $timeMessage.text(
                    'No available time slots for this duration. Try another duration or date.'
                );
            }
        }

        function formatTime(hour) {
            const suffix = hour >= 12 ? 'PM' : 'AM';
            return (hour % 12 || 12) + ':00 ' + suffix;
        }

        function updatePaddles() {
            const renting = $('#rent_paddle').is(':checked');
            const $quantity = $('#paddle_quantity');

            $('#paddleQuantityGroup').css('display', renting ? 'block' : 'none');
            $quantity.prop('disabled', !renting);
            $quantity.prop('required', renting);

            updateSummary();
        }

        function updatePayment() {
            const online = $('#payment_method').val() === 'online';
            const $reference = $('#payment_reference');

            $('#gcashGroup').css('display', online ? 'block' : 'none');
            $reference.prop('disabled', !online);
            $reference.prop('required', online);
        }

        function updateSummary() {
            const duration = parseInt($durationField.val(), 10);
            const hasOwnPaddle = $('#own_paddle').is(':checked');
            const quantity = hasOwnPaddle ? 0 :
                parseInt($('#paddle_quantity').val(), 10);

            let rate = 0;
            let courtTotal = 0;

            if (selectedStart) {
                const hour = parseInt(selectedStart.split(':')[0], 10);
                rate = hour >= 17 ? 500 : 300;
                courtTotal = rate * duration;
            }

            const paddleTotal = quantity * 50;

            $('#summaryTime').text(
                selectedStart
                    ? formatTime(parseInt(selectedStart.split(':')[0], 10)) +
                      ' – ' +
                      formatTime(parseInt($endField.val().split(':')[0], 10))
                    : 'Not selected'
            );

            $('#summaryDuration').text(
                duration + (duration === 1 ? ' hour' : ' hours')
            );

            $('#hourlyRate').text(rate ? money(rate) + '/hour' : '—');

            $('#courtTotal').text(money(courtTotal));
            $('#paddleTotal').text(money(paddleTotal));
            $('#grandTotal').text(money(courtTotal + paddleTotal));
        }

        $durationField.on('change', function () {
            selectedStart = '';
            $startField.val('');
            $endField.val('');
            renderTimeSlots();
            updateSummary();
        });

        $('input[name="has_own_paddle"]').on('change', updatePaddles);

        $('#paddle_quantity').on('change', updateSummary);

        $('#payment_method').on('change', updatePayment);

        $('#bookingForm').on('submit', function (event) {
            if (!$dateField.val() || !$courtField.val() ||
                !$startField.val() || !$endField.val()) {
                event.preventDefault();
                alert('Please select a date, court, and available time slot.');
                return;
            }

            const selectedSlotOverlaps = bookings.some(function (booking) {
                const bookedStart = booking.start_time.substring(0, 5);
                const bookedEnd = booking.end_time.substring(0, 5);

                return $startField.val() < bookedEnd &&
                       $endField.val() > bookedStart;
            });

            if (selectedSlotOverlaps) {
                event.preventDefault();
                alert('That time is no longer available. Please select another slot.');
            }
        });

        renderCalendar();
        updatePaddles();
        updatePayment();
        updateSummary();
    });
})();
</script>