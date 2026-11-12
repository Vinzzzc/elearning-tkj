<?php
// admin/proses-edit.php
session_start();
require '../config/database.php';
require '../config/alert.php'; // Memanggil fungsi pop-up

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_materi         = $_POST['id_materi'];
    $judul             = $_POST['judul'];
    $id_kategori       = $_POST['id_kategori'];
    $deskripsi_singkat = $_POST['deskripsi_singkat'];
    $isi_materi        = $_POST['isi_materi'];
    $cover_lama        = $_POST['cover_lama'];
    
    $nama_cover = $cover_lama; // Secara default, gunakan cover lama

    // Cek apakah ada file cover baru yang diunggah
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['cover_image']['tmp_name'];
        $file_name = $_FILES['cover_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            // Hapus gambar lama dari server jika ada
            if (!empty($cover_lama) && file_exists('../uploads/' . $cover_lama)) {
                unlink('../uploads/' . $cover_lama);
            }
            
            // Upload gambar baru
            $nama_cover = uniqid('cover_') . '.' . $file_ext;
            move_uploaded_file($tmp_name, '../uploads/' . $nama_cover);
        } else {
            echo "<script>alert('Gagal: Format gambar salah!'); window.history.back();</script>";
            exit();
        }
    }

    // Update data di database. 
    // Catatan: Kolom 'updated_at' akan otomatis diperbarui oleh MariaDB!
    $sql = "UPDATE materi SET 
            judul = :judul, 
            deskripsi_singkat = :deskripsi, 
            isi_materi = :isi, 
            cover_image = :cover, 
            id_kategori = :kategori 
            WHERE id_materi = :id";
            
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        'judul'     => $judul,
        'deskripsi' => $deskripsi_singkat,
        'isi'       => $isi_materi,
        'cover'     => $nama_cover,
        'kategori'  => $id_kategori,
        'id'        => $id_materi
    ]);

    tampilkan_alert('Berhasil!', 'Materi TKJ berhasil diperbarui.', 'success', 'kelola-materi.php');

}
?>