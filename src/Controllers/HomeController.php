<?php
/**
 * HomeController - Punto de entrada inicial
 * Redirige según estado de autenticación.
 */
namespace App\Controllers;

class HomeController {
    public function index() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
        } else {
            $this->redirect('/dashboard');
        }
    }

    private function redirect($path) {
        header('Location: ' . BASE_URL . ltrim($path, '/'));
        exit;
    }
}
