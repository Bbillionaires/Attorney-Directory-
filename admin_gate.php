<?php
// Simple admin guard for protected pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in as admin, send to login page
if (empty($_SESSION['is_admin'])) {
    header('Location: /admin/login');
    exit;
}
