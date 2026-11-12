<?php
// admin/hapus-materi.php
session_start();
require '../config/database.php';
require '../config/alert.php';

$id_materi = $_GET['id'] ?? null;

if (!$id_materi) {
    tampilkan_alert('Gagal!', 'ID Materi tidak ditemukan!', 'error', 'kelola-materi.php');
}

// Ambil data cover & hapus file fisik
$stmt = $pdo->prepare("SELECT cover_image FROM materi WHERE id_materi = :id");
$stmt->execute(['id' => $id_materi]);
$materi = $stmt->fetch();

if ($materi) {
    if (!empty($materi['cover_image'])) {
        $file_path = '../uploads/' . $materi['cover_image'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM materi WHERE id_materi = :id");
    $stmt->execute(['id' => $id_materi]);

    tampilkan_alert('Terhapus!', 'Materi dan file gambar berhasil dihapus permanen.', 'success', 'kelola-materi.php');
} else {
    tampilkan_alert('Gagal!', 'Data materi tidak ditemukan di database.', 'error', 'kelola-materi.php');
}
?>