
<form method="POST"
      action="<?= BASE_URL ?>/AllBookings/reject"
      onsubmit="return confirm('Are you sure you want to reject this booking?');">

    <input type="hidden"
           name="booking_id"
           value="<?= (int) $booking['id'] ?>">

    <input type="text"
           name="rejection_reason"
           class="form-control form-control-sm mb-2"
           placeholder="Reason for rejection"
           maxlength="500"
           required>

    <button type="submit" class="btn btn-danger btn-sm">
        <i class="fas fa-times"></i>
        Reject Booking
    </button>
</form>
