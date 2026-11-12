<?php
// config/database.php

$host = '127.0.0.1';
$dbname = 'db-tekaje';
$username = 'root'; 
$password = ''; // Isi dengan password MariaDB kamu!

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Koneksi sukses!"; // Buka komentar ini kalau mau tes koneksi
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>