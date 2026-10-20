<?php

namespace Controllers;

use Core\BaseController;
use Core\Auth;
use Models\Courts;

class CourtsController extends BaseController
{
    private Courts $courts;

    public function __construct()
    {
        $this->courts = new Courts();
    }

    private function authorizeAdmin(): void
    {
        Auth::requireLogin();

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '/Dashboard');
            exit;
        }
    }

    public function index(): void
    {
        $this->authorizeAdmin();

        $this->view('Courts/index', [
            'title' => '',
            'courts' => $this->courts->find(),
            'error' => $_SESSION['court_error'] ?? null,
            'success' => $_SESSION['court_success'] ?? null
        ]);

        unset($_SESSION['court_error'], $_SESSION['court_success']);
    }

    public function create(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/Courts');
            exit;
        }

        try {
            $this->courts->create(
                (string) ($_POST['court_name'] ?? '')
            );

            $_SESSION['court_success'] = 'Court added successfully.';
        } catch (\InvalidArgumentException $e) {
            $_SESSION['court_error'] = $e->getMessage();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['court_error'] = 'Unable to save the court.';
        }

        header('Location: ' . BASE_URL . '/Courts');
        exit;
    }

    public function update(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/Courts');
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        try {
            if (!$id || !$this->courts->update(
                (int) $id,
                (string) ($_POST['court_name'] ?? '')
            )) {
                $_SESSION['court_error'] = 'Court not found or unchanged.';
            } else {
                $_SESSION['court_success'] = 'Court updated successfully.';
            }
        } catch (\InvalidArgumentException $e) {
            $_SESSION['court_error'] = $e->getMessage();
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['court_error'] = 'Unable to update the court.';
        }

        header('Location: ' . BASE_URL . '/Courts');
        exit;
    }

    public function toggleStatus(): void
    {
        $this->authorizeAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/Courts');
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $active = ($_POST['is_active'] ?? '') === '1';

        try {
            if (!$id || !$this->courts->setActive((int) $id, $active)) {
                $_SESSION['court_error'] = 'Court not found or status unchanged.';
            } else {
                $_SESSION['court_success'] = $active
                    ? 'Court activated successfully.'
                    : 'Court deactivated successfully.';
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['court_error'] = 'Unable to change the court status.';
        }

        header('Location: ' . BASE_URL . '/Courts');
        exit;
    }
}
