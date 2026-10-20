<form method="POST"
      action="<?= BASE_URL ?>/AllBookings/confirm"
      onsubmit="return confirm('Are you sure you want to approve this booking?');">

    <input type="hidden"
           name="booking_id"
           value="<?= (int) $booking['id'] ?>">

    <button type="submit" class="btn btn-success btn-sm">
        <i class="fas fa-check"></i>
        Confirm Booking
    </button>
</form>
