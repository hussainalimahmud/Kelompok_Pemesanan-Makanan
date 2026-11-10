<?php
// dosen/auth_check.php
require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'dosen') {
    header('Location: ../login.php');
    exit;
}
?>
