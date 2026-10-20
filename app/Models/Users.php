<?php

namespace Models;

use Core\Model;

class Users extends Model
{
    public function find()
    {
        $stmt = $this->db->query(
            'SELECT id, email, role, first_name, last_name
             FROM users
             ORDER BY last_name, first_name'
        );

        return $stmt->fetchAll();
    }

    public function findByID($id)
    {
        $stmt = $this->db->prepare(
            'SELECT id, email, role, first_name, last_name
             FROM users
             WHERE id = ?'
        );

        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare(
            'SELECT id, email, password, role, first_name, last_name
             FROM users
             WHERE email = ?'
        );

        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }

    
public function emailExists($email, $excludeId = null): bool
{
    if ($excludeId !== null) {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM users
             WHERE email = ? AND id != ?'
        );

        $stmt->execute([$email, $excludeId]);
    } else {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM users WHERE email = ?'
        );

        $stmt->execute([$email]);
    }

    return (int) $stmt->fetchColumn() > 0;
}


    public function save($data)
    {
        $now = date('Y-m-d H:i:s');

        if (!empty($data['id'])) {
            if (!empty($data['password'])) {
                $stmt = $this->db->prepare(
                    'UPDATE users
                     SET email = ?, password = ?, role = ?, first_name = ?, last_name = ?, updated_at = ?
                     WHERE id = ?'
                );

                return $stmt->execute([
                    $data['email'],
                    password_hash($data['password'], PASSWORD_DEFAULT),
                    $data['role'],
                    $data['first_name'],
                    $data['last_name'],
                    $now,
                    $data['id']
                ]);
            }

            $stmt = $this->db->prepare(
                'UPDATE users
                 SET email = ?, role = ?, first_name = ?, last_name = ?, updated_at = ?
                 WHERE id = ?'
            );

            return $stmt->execute([
                $data['email'],
                $data['role'],
                $data['first_name'],
                $data['last_name'],
                $now,
                $data['id']
            ]);
        }

        $stmt = $this->db->prepare(
            'INSERT INTO users
             (email, password, role, first_name, last_name)
             VALUES (?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'] ?? 'customer',
            $data['first_name'],
            $data['last_name']
        ]);
    }

    public function delete($id): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM users WHERE id = ?'
        );

        return $stmt->execute([$id]);
    }
}