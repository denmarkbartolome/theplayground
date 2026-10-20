<?php

namespace Models;

use Core\Model;
use PDO;
use InvalidArgumentException;

class Bookings extends Model
{
    // Get all bookings
    public function find(): array
    {
        return $this->db->query(
            'SELECT b.*, c.court_name, u.first_name, u.last_name
             FROM bookings b
             JOIN courts c ON b.court_id = c.id
             JOIN users u ON b.user_id = u.id
             ORDER BY b.created_at DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one booking
    public function findByID($id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, c.court_name, u.first_name, u.last_name
             FROM bookings b
             JOIN courts c ON b.court_id = c.id
             JOIN users u ON b.user_id = u.id
             WHERE b.id = ?'
        );

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Get bookings belonging to a customer
    public function findByUserID($userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT b.*, c.court_name
             FROM bookings b
             JOIN courts c ON b.court_id = c.id
             WHERE b.user_id = ?
             ORDER BY b.created_at DESC'
        );

        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get active courts
    public function getCourts(): array
    {
        return $this->db->query(
            'SELECT id, court_name
             FROM courts
             WHERE is_active = TRUE
             ORDER BY id'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get unavailable times for a selected court and date
    public function getUnavailable($courtId, $date): array
    {
        $stmt = $this->db->prepare(
            "SELECT start_time, end_time, status
             FROM bookings
             WHERE court_id = ? AND booking_date = ?
               AND status IN ('pending', 'approved')
             ORDER BY start_time"
        );

        $stmt->execute([$courtId, $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Save a new booking request
    public function save($data): int
{
    // Determine whether the customer owns paddles.
    $hasOwnPaddle = filter_var(
        $data['has_own_paddle'] ?? true,
        FILTER_VALIDATE_BOOLEAN
    );

    // Convert the boolean to an integer for database compatibility.
    $hasOwnPaddleValue = $hasOwnPaddle ? 1 : 0;

    // Only charge for paddles when the customer rents them.
    $quantity = $hasOwnPaddle
        ? 0
        : (int)($data['paddle_quantity'] ?? 0);

    if (!$hasOwnPaddle && ($quantity < 1 || $quantity > 4)) {
        throw new InvalidArgumentException(
            'Choose between 1 and 4 paddles.'
        );
    }

    // Validate booking times.
    $start = strtotime($data['start_time']);
    $end = strtotime($data['end_time']);

    if ($start === false || $end === false || $end <= $start) {
        throw new InvalidArgumentException('Invalid booking time.');
    }

    $hours = ($end - $start) / 3600;

    if (
        $hours != (int)$hours ||
        date('i', $start) !== '00' ||
        date('i', $end) !== '00'
    ) {
        throw new InvalidArgumentException(
            'Use whole-hour booking slots.'
        );
    }

    $startHour = (int)date('G', $start);
    $endHour = (int)date('G', $end);

    if ($startHour < 8 || $endHour > 22) {
        throw new InvalidArgumentException(
            'Bookings must be between 8 AM and 10 PM.'
        );
    }

    if ($startHour < 17 && $endHour > 16) {
        throw new InvalidArgumentException(
            'Bookings cannot cross the 4 PM–5 PM rate gap.'
        );
    }

    if ($startHour >= 16 && $startHour < 17) {
        throw new InvalidArgumentException(
            'The 4 PM–5 PM rate has not been defined.'
        );
    }

    // Calculate court and paddle fees.
    $rate = $startHour >= 17 ? 500 : 300;
    $courtTotal = $hours * $rate;
    $paddlePrice = 50;
    $paddleTotal = $quantity * $paddlePrice;
    $total = $courtTotal + $paddleTotal;

    // Validate payment details.
    $method = $data['payment_method'] ?? '';
    $reference = trim($data['payment_reference'] ?? '');

    if (!in_array($method, ['online', 'onsite'], true)) {
        throw new InvalidArgumentException(
            'Invalid payment method.'
        );
    }

    if ($method === 'online' && $reference === '') {
        throw new InvalidArgumentException(
            'Enter your GCash reference number.'
        );
    }

    // Insert using named parameters.
    $sql = "
        INSERT INTO bookings (
            user_id,
            court_id,
            booking_date,
            start_time,
            end_time,
            hourly_rate,
            court_total,
            has_own_paddle,
            paddle_quantity,
            paddle_price,
            paddle_total,
            total_amount,
            payment_method,
            payment_reference,
            payment_status,
            status,
            customer_notes
        ) VALUES (
            :user_id,
            :court_id,
            :booking_date,
            :start_time,
            :end_time,
            :hourly_rate,
            :court_total,
            :has_own_paddle,
            :paddle_quantity,
            :paddle_price,
            :paddle_total,
            :total_amount,
            :payment_method,
            :payment_reference,
            :payment_status,
            'pending',
            :customer_notes
        )
        RETURNING id
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'user_id' => (int)$data['user_id'],
        'court_id' => (int)$data['court_id'],
        'booking_date' => $data['booking_date'],
        'start_time' => $data['start_time'],
        'end_time' => $data['end_time'],
        'hourly_rate' => $rate,
        'court_total' => $courtTotal,
        'has_own_paddle' => $hasOwnPaddleValue,
        'paddle_quantity' => $quantity,
        'paddle_price' => $paddlePrice,
        'paddle_total' => $paddleTotal,
        'total_amount' => $total,
        'payment_method' => $method,
        'payment_reference' => $method === 'online'
            ? $reference
            : null,
        'payment_status' => $method === 'online'
            ? 'pending'
            : 'unpaid',
        'customer_notes' => $data['customer_notes'] ?? null
    ]);

    return (int)$stmt->fetchColumn();
}

    // Get pending bookings for staff review
    public function findPending(): array
    {
        return $this->db->query(
            "SELECT b.*, c.court_name, u.first_name, u.last_name, u.email
             FROM bookings b
             JOIN courts c ON c.id = b.court_id
             JOIN users u ON u.id = b.user_id
             WHERE b.status = 'pending'
             ORDER BY b.created_at ASC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update booking status; controller must enforce staff authorization
    public function updateStatus($id, $status, $staffId = null, $reason = null): bool
    {
        if (!in_array($status, ['approved', 'rejected', 'cancelled'], true)) {
            throw new InvalidArgumentException('Invalid booking status.');
        }

        if (in_array($status, ['approved', 'rejected'], true)) {
            if (!$staffId) {
                throw new InvalidArgumentException('Staff ID is required.');
            }

            $stmt = $this->db->prepare(
                "UPDATE bookings
                 SET status = ?, reviewed_by = ?, reviewed_at = NOW(),
                     rejection_reason = ?, updated_at = NOW()
                 WHERE id = ? AND status = 'pending'
                   AND (? <> 'approved' OR payment_method <> 'online'
                        OR payment_status = 'pending')
                 RETURNING id"
            );

            $stmt->execute([
                $status,
                $staffId,
                $status === 'rejected' ? $reason : null,
                $id,
                $status
            ]);

            return (bool)$stmt->fetchColumn();
        }

        $stmt = $this->db->prepare(
            "UPDATE bookings
             SET status = 'cancelled', updated_at = NOW()
             WHERE id = ? AND status IN ('pending', 'approved')
             RETURNING id"
        );

        $stmt->execute([$id]);
        return (bool)$stmt->fetchColumn();
    }
    
    //Finds allbooking for staff and admin
    public function findAllBookings()
{
    $sql = "
        SELECT
            b.*,
            c.court_name,
            u.first_name,
            u.last_name,
            u.email
        FROM public.bookings b
        JOIN public.courts c ON c.id = b.court_id
        JOIN public.users u ON u.id = b.user_id
        ORDER BY b.booking_date DESC, b.start_time DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function confirmPayment($bookingId)
{
    $sql = "
        UPDATE public.bookings
        SET
            payment_status = 'paid',
            updated_at = NOW()
        WHERE id = :id
          AND payment_method = 'online'
          AND payment_status = 'pending'
          AND status = 'pending'
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id' => $bookingId]);

    return $stmt->rowCount() === 1;
}


public function confirmBooking($bookingId, $staffId)
{
    $sql = "
        UPDATE public.bookings
        SET
            status = 'approved',
            reviewed_by = :staff_id,
            reviewed_at = NOW(),
            updated_at = NOW()
        WHERE id = :id
          AND status = 'pending'
          AND (
              payment_method = 'onsite'
              OR (
                  payment_method = 'online'
                  AND payment_status = 'paid'
              )
          )
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'staff_id' => $staffId,
        'id' => $bookingId
    ]);

    return $stmt->rowCount() === 1;
}


public function rejectBooking($bookingId, $staffId, $reason)
{
    $sql = "
        UPDATE public.bookings
        SET
            status = 'rejected',
            rejection_reason = :reason,
            reviewed_by = :staff_id,
            reviewed_at = NOW(),
            updated_at = NOW()
        WHERE id = :id
          AND status = 'pending'
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        'reason' => $reason,
        'staff_id' => $staffId,
        'id' => $bookingId
    ]);

    return $stmt->rowCount() === 1;
}


public function confirmFacilityPayment($bookingId): bool
{
    $sql = "
        UPDATE public.bookings
        SET
            payment_status = 'paid',
            updated_at = NOW()
        WHERE id = :id
          AND payment_method = 'onsite'
          AND payment_status = 'unpaid'
          AND status = 'pending'
        RETURNING id
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        'id' => $bookingId
    ]);

    return (bool) $stmt->fetchColumn();
}

public function getRevenueForPeriod($startDate, $endDate): array
{
    $sql = "
        SELECT
            COALESCE(SUM(court_total), 0) AS court_revenue,
            COALESCE(SUM(paddle_total), 0) AS addon_revenue,
            COALESCE(SUM(total_amount), 0) AS booking_revenue,
            COUNT(*) AS paid_bookings
        FROM public.bookings
        WHERE payment_status = 'paid'
          AND booking_date >= ?
          AND booking_date <= ?
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$startDate, $endDate]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
        'court_revenue' => 0,
        'addon_revenue' => 0,
        'booking_revenue' => 0,
        'paid_bookings' => 0
    ];
}


    // Delete a booking
    public function delete($id): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM bookings WHERE id = ?'
        );

        return $stmt->execute([$id]);
    }
}
