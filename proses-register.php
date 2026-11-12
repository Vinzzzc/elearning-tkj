<?php
// proses-register.php
require 'config/database.php'; // Panggil koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email        = $_POST['email'];
    $username     = $_POST['username'];
    $password     = $_POST['password'];
    
    // Hash password demi keamanan
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $role = 'pelajar'; // Set role otomatis jadi pelajar

    try {
        // Query untuk memasukkan data
        $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, username, password_hash, role) VALUES (:nama_lengkap, :email, :username, :password_hash, :role)");
        
        $stmt->execute([
            'nama_lengkap'  => $nama_lengkap,
            'email'         => $email,
            'username'      => $username,
            'password_hash' => $password_hash,
            'role'          => $role
        ]);

        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='index.php';</script>";
        
    } catch (PDOException $e) {
        // Cek jika username atau email sudah ada di database (Error Code 23000)
        if ($e->getCode() == 23000) {
            echo "<script>alert('Error: Username atau Email sudah terdaftar!'); window.history.back();</script>";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>