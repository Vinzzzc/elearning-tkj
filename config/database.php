<?php
// config/database.php

$host = getenv('DB_HOST') ?: 'localhost';
$username= getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname   = getenv('DB_NAME') ?: 'db_sekolah';

$conn = mysqli_connect($host, $username, $password, $dbname);

try {
    $pdo = new PDO("mysql:host=$host; dbname=$dbname; charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Koneksi sukses!"; // Buka komentar ini kalau mau tes koneksi
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>