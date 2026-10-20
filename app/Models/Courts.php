<?php

namespace Models;

use Core\Model;
use PDO;
use InvalidArgumentException;

class Courts extends Model
{
    public function find(): array
    {
        $stmt = $this->db->query("
            SELECT id, court_name, is_active, created_at
            FROM public.courts
            ORDER BY id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByID($id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, court_name, is_active, created_at
            FROM public.courts
            WHERE id = ?
        ");

        $stmt->execute([(int) $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(string $courtName): int
    {
        $courtName = trim($courtName);

        if ($courtName === '' || mb_strlen($courtName) > 100) {
            throw new InvalidArgumentException(
                'Court name is required and must be at most 100 characters.'
            );
        }

        $stmt = $this->db->prepare("
            INSERT INTO public.courts (court_name, is_active)
            VALUES (?, TRUE)
            RETURNING id
        ");

        try {
            $stmt->execute([$courtName]);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23505') {
                throw new InvalidArgumentException(
                    'A court with this name already exists.'
                );
            }

            throw $e;
        }

        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, string $courtName): bool
    {
        $courtName = trim($courtName);

        if ($id <= 0 || $courtName === '' || mb_strlen($courtName) > 100) {
            throw new InvalidArgumentException(
                'Enter a valid court ID and court name.'
            );
        }

        $stmt = $this->db->prepare("
            UPDATE public.courts
            SET court_name = ?
            WHERE id = ?
            RETURNING id
        ");

        try {
            $stmt->execute([$courtName, $id]);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23505') {
                throw new InvalidArgumentException(
                    'A court with this name already exists.'
                );
            }

            throw $e;
        }

        return (bool) $stmt->fetchColumn();
    }

    public function setActive(int $id, bool $isActive): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid court ID.');
        }

        $stmt = $this->db->prepare("
            UPDATE public.courts
            SET is_active = ?
            WHERE id = ?
            RETURNING id
        ");

        $stmt->execute([$isActive, $id]);

        return (bool) $stmt->fetchColumn();
    }
}
