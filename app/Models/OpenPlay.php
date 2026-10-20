<?php

namespace Models;

use Core\Model;

class OpenPlay extends Model
{
    private const PRICE_PER_PLAYER = 200.00;

    /**
     * Get upcoming Open Play sessions and available slots.
     */
    public function getUpcomingSessions($limit = 90)
    {
        $sql = "
            SELECT
                s.id,
                s.session_date,
                s.start_time,
                s.end_time,
                s.capacity,
                s.status,
                COUNT(r.id) FILTER (
                    WHERE r.reservation_status IN ('pending', 'confirmed')
                ) AS reserved_count,
                s.capacity - COUNT(r.id) FILTER (
                    WHERE r.reservation_status IN ('pending', 'confirmed')
                ) AS available_slots
            FROM public.open_play_sessions s
            LEFT JOIN public.open_play_reservations r
                ON r.session_id = s.id
            WHERE s.session_date >=
                (NOW() AT TIME ZONE 'Asia/Manila')::DATE
              AND s.status = 'open'
            GROUP BY s.id
            ORDER BY s.session_date ASC
            LIMIT ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int) $limit]);

        return $stmt->fetchAll();
    }

    /**
     * Get one session by ID.
     */
    public function getSessionByID($sessionId)
    {
        $sql = "
            SELECT
                s.id,
                s.session_date,
                s.start_time,
                s.end_time,
                s.capacity,
                s.status,
                COUNT(r.id) FILTER (
                    WHERE r.reservation_status IN ('pending', 'confirmed')
                ) AS reserved_count,
                s.capacity - COUNT(r.id) FILTER (
                    WHERE r.reservation_status IN ('pending', 'confirmed')
                ) AS available_slots
            FROM public.open_play_sessions s
            LEFT JOIN public.open_play_reservations r
                ON r.session_id = s.id
            WHERE s.id = ?
            GROUP BY s.id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Create a customer's Open Play reservation.
     */
    public function createReservation($data)
    {
        $sessionId = (int) ($data['session_id'] ?? 0);
        $userId = (int) ($data['user_id'] ?? 0);

        $paymentMethod = strtolower(
            trim($data['payment_method'] ?? '')
        );

        $paymentReference = trim(
            $data['payment_reference'] ?? ''
        );

        if ($sessionId <= 0 || $userId <= 0) {
            throw new \InvalidArgumentException(
                'Invalid session or customer.'
            );
        }

        if (!in_array($paymentMethod, ['gcash', 'maya'], true)) {
            throw new \InvalidArgumentException(
                'Please select GCash or Maya.'
            );
        }

        if ($paymentReference === '') {
            throw new \InvalidArgumentException(
                'Payment reference is required.'
            );
        }

        $stmt = $this->db->prepare("
            INSERT INTO public.open_play_reservations (
                session_id,
                user_id,
                total_amount,
                payment_method,
                payment_reference,
                payment_status,
                reservation_status
            )
            VALUES (?, ?, ?, ?, ?, 'pending_verification', 'pending')
            RETURNING id
        ");

        $stmt->execute([
            $sessionId,
            $userId,
            self::PRICE_PER_PLAYER,
            $paymentMethod,
            $paymentReference
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Get a customer's reservation history.
     */
    public function getCustomerReservations($userId)
    {
        $stmt = $this->db->prepare("
            SELECT
                r.*,
                s.session_date,
                s.start_time,
                s.end_time
            FROM public.open_play_reservations r
            INNER JOIN public.open_play_sessions s
                ON s.id = r.session_id
            WHERE r.user_id = ?
            ORDER BY r.created_at DESC
        ");

        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    /**
     * Get all reservations for staff/admin.
     */
    public function getAllReservations()
    {
        $stmt = $this->db->query("
            SELECT
                r.*,
                s.session_date,
                s.start_time,
                s.end_time,
                u.first_name,
                u.last_name,
                u.email
            FROM public.open_play_reservations r
            INNER JOIN public.open_play_sessions s
                ON s.id = r.session_id
            INNER JOIN public.users u
                ON u.id = r.user_id
            ORDER BY
                CASE
                    WHEN r.payment_status = 'pending_verification'
                    THEN 0
                    ELSE 1
                END,
                r.created_at DESC
        ");

        return $stmt->fetchAll();
    }

    /**
     * Find one reservation by ID.
     */
    public function getReservationByID($reservationId)
    {
        $stmt = $this->db->prepare("
            SELECT
                r.*,
                s.session_date,
                s.start_time,
                s.end_time,
                u.first_name,
                u.last_name,
                u.email
            FROM public.open_play_reservations r
            INNER JOIN public.open_play_sessions s
                ON s.id = r.session_id
            INNER JOIN public.users u
                ON u.id = r.user_id
            WHERE r.id = ?
        ");

        $stmt->execute([$reservationId]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Confirm payment and reservation after staff verification.
     */
    public function confirmReservation($reservationId, $staffId, $notes = null)
    {
        $stmt = $this->db->prepare("
            UPDATE public.open_play_reservations
            SET
                payment_status = 'verified',
                reservation_status = 'confirmed',
                verified_by = ?,
                verified_at = NOW(),
                staff_notes = ?,
                updated_at = NOW()
            WHERE id = ?
              AND payment_status = 'pending_verification'
              AND reservation_status = 'pending'
        ");

        $stmt->execute([
            $staffId,
            $notes,
            $reservationId
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Reject a reservation after staff review.
     */
    public function rejectReservation($reservationId, $staffId, $reason)
    {
        $stmt = $this->db->prepare("
            UPDATE public.open_play_reservations
            SET
                payment_status = 'rejected',
                reservation_status = 'rejected',
                verified_by = ?,
                verified_at = NOW(),
                staff_notes = ?,
                updated_at = NOW()
            WHERE id = ?
              AND payment_status = 'pending_verification'
              AND reservation_status = 'pending'
        ");

        $stmt->execute([
            $staffId,
            $reason,
            $reservationId
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getRevenueForPeriod($startDate, $endDate): array
{
    $sql = "
        SELECT
            COALESCE(SUM(r.total_amount), 0) AS open_play_revenue,
            COUNT(*) AS confirmed_reservations
        FROM public.open_play_reservations r
        INNER JOIN public.open_play_sessions s
            ON s.id = r.session_id
        WHERE r.reservation_status = 'confirmed'
          AND r.payment_status = 'verified'
          AND s.session_date >= ?
          AND s.session_date <= ?
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$startDate, $endDate]);

    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [
        'open_play_revenue' => 0,
        'confirmed_reservations' => 0
    ];
}

    /**
     * Get Open Play revenue statistics for admin reports.
     */
    public function getRevenueSummary()
    {
        $stmt = $this->db->query("
            SELECT
                COUNT(*) FILTER (
                    WHERE reservation_status = 'confirmed'
                      AND payment_status = 'verified'
                ) AS confirmed_reservations,

                COALESCE(SUM(total_amount) FILTER (
                    WHERE reservation_status = 'confirmed'
                      AND payment_status = 'verified'
                ), 0) AS verified_revenue,

                COALESCE(SUM(total_amount) FILTER (
                    WHERE reservation_status = 'pending'
                      AND payment_status = 'pending_verification'
                ), 0) AS pending_amount,

                COUNT(*) FILTER (
                    WHERE reservation_status = 'rejected'
                ) AS rejected_reservations

            FROM public.open_play_reservations
        ");

        return $stmt->fetch();
    }
}
