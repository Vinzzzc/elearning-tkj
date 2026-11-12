<?php
// admin/proses-role.php
session_start();
require '../config/database.php';
require '../config/alert.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_user = $_POST['id_user'];
    $role_baru = $_POST['role'];

    if ($role_baru == 'admin' || $role_baru == 'pelajar') {
        $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id_user = :id");
        $stmt->execute([
            'role' => $role_baru,
            'id'   => $id_user
        ]);

        tampilkan_alert('Berhasil!', 'Hak akses (role) pengguna berhasil diubah.', 'success', 'kelola-user.php');
    } else {
        tampilkan_alert('Gagal!', 'Pilihan role tidak valid.', 'error', 'kelola-user.php');
    }
}
?>