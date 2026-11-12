<?php
// admin/hapus-user.php
session_start();
require '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak!");
}

$id_user = $_GET['id'];

// Cegah admin menghapus dirinya sendiri
if ($id_user == $_SESSION['id_user']) {
    echo "<script>alert('Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!'); window.location='kelola-user.php';</script>";
    exit();
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id_user = :id");
$stmt->execute(['id' => $id_user]);

echo "<script>alert('Akun pengguna berhasil dihapus!'); window.location='kelola-user.php';</script>";
?>