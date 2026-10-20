<?php

namespace Models;

use Core\Model;

class Customers extends Model
{
    /**
     * Get customers with optional search and sorting.
     */
    public function find($search = '', $filter = 'all')
    {
        $sql = "
            SELECT id, email, first_name, last_name, created_at
            FROM users
            WHERE role = 'customer'
        ";

        $params = [];

        if ($search !== '') {
            $sql .= "
                AND (
                    first_name ILIKE ?
                    OR last_name ILIKE ?
                    OR email ILIKE ?
                    OR (first_name || ' ' || last_name) ILIKE ?
                )
            ";

            $term = '%' . $search . '%';
            $params = [$term, $term, $term, $term];
        }

        if ($filter === 'recent') {
            $sql .= " ORDER BY created_at DESC NULLS LAST";
        } else {
            $sql .= " ORDER BY last_name ASC, first_name ASC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Check whether an email is already registered.
     */
    public function emailExists($email)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM users
            WHERE LOWER(email) = LOWER(?)
        ");

        $stmt->execute([$email]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Create a customer account.
     * Role is fixed to customer.
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users
                (email, password, role, first_name, last_name)
            VALUES (?, ?, 'customer', ?, ?)
        ");

        return $stmt->execute([
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['first_name'],
            $data['last_name']
        ]);
    }
}
