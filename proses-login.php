<?php
// proses-login.php
session_start();

// Sembunyikan error mentah di layar
error_reporting(0);
ini_set('display_errors', 0);

require 'config/database.php';
require 'config/alert.php';

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $login_id = trim($_POST['login_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($login_id) || empty($password)) {
        tampilkan_alert('Peringatan', 'Username/Email dan Password wajib diisi!', 'warning', 'index.php');
        exit();
    }

    // Cari user berdasarkan username ATAU email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :login_id OR email = :login_id");
    $stmt->execute(['login_id' => $login_id]);
    $user = $stmt->fetch();

    // Ambil password dari kolom 'password_hash' sesuai struktur database kamu
    $db_password = $user['password_hash'] ?? '';

    // Cek apakah user ditemukan dan password cocok
    if ($user && !empty($db_password) && password_verify($password, $db_password)) {
        
        // Simpan sesi login
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama']    = $user['nama_lengkap'];
        $_SESSION['role']    = $user['role'];

        // Arahkan berdasarkan role dengan pop-up SweetAlert2
        if ($user['role'] == 'admin') {
            tampilkan_alert('Berhasil Login!', 'Selamat datang kembali, Admin ' . $user['nama_lengkap'], 'success', 'admin/dashboard.php');
        } else {
            tampilkan_alert('Berhasil Login!', 'Selamat datang, ' . $user['nama_lengkap'], 'success', 'pelajar/index.php');
        }

    } else {
        // Jika gagal
        tampilkan_alert('Login Gagal!', 'Username/Email atau password yang Anda masukkan salah.', 'error', 'index.php');
    }
}
?>