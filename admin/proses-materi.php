<?php
// admin/proses-materi.php
session_start();
require '../config/database.php';
require '../config/alert.php'; // Panggil alert tunggal

// ... (proses upload file & query INSERT database kamu) ...

// Proteksi: Pastikan yang mengakses adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak!");
}

// Fungsi untuk membuat Slug URL (contoh: "Belajar AWS" -> "belajar-aws-1698765")
function createSlug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug . '-' . time(); // Ditambah waktu agar selalu unik
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul             = $_POST['judul'];
    $id_kategori       = $_POST['id_kategori'];
    $deskripsi_singkat = $_POST['deskripsi_singkat'];
    $isi_materi        = $_POST['isi_materi']; // Teks + tag HTML dari TinyMCE
    $id_admin          = $_SESSION['id_user']; // ID admin yang sedang login
    $slug              = createSlug($judul);
    
    $nama_cover = null;

    // --- PROSES UPLOAD GAMBAR COVER ---
    // Cek apakah admin mengunggah gambar cover tanpa error
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['cover_image']['tmp_name'];
        $file_name = $_FILES['cover_image']['name'];
        
        // Ambil ekstensi file (jpg, png, dsb)
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        // Validasi ekstensi (keamanan)
        if (in_array($file_ext, $allowed_ext)) {
            // Beri nama acak agar tidak bentrok dengan file lain
            $nama_cover = uniqid('cover_') . '.' . $file_ext;
            $tujuan_upload = '../uploads/' . $nama_cover;
            
            // Pindahkan file dari penyimpanan sementara ke folder uploads/
            move_uploaded_file($tmp_name, $tujuan_upload);
        } else {
            echo "<script>alert('Gagal: Format gambar hanya boleh JPG, PNG, atau WEBP!'); window.history.back();</script>";
            exit();
        }
    }

    // --- PROSES SIMPAN KE MARIADB ---
    try {
        $sql = "INSERT INTO materi (judul, slug, deskripsi_singkat, isi_materi, cover_image, id_kategori, id_admin) 
                VALUES (:judul, :slug, :deskripsi, :isi, :cover, :kategori, :admin)";
                
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            'judul'     => $judul,
            'slug'      => $slug,
            'deskripsi' => $deskripsi_singkat,
            'isi'       => $isi_materi,
            'cover'     => $nama_cover, // Akan bernilai null jika tidak upload cover
            'kategori'  => $id_kategori,
            'admin'     => $id_admin
        ]);

        try {
    // ... eksekusi insert ...
    tampilkan_alert('Berhasil!', 'Materi TKJ baru telah diposting.', 'success', 'dashboard.php');
} catch (PDOException $e) {
    tampilkan_alert('Gagal Database!', $e->getMessage(), 'error', 'tambah-materi.php');
}

    } catch (PDOException $e) {
        echo "Error Database: " . $e->getMessage();
    }
}
?>