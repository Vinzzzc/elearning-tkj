<?php
// pelajar/baca-materi.php
session_start();
require '../config/database.php';

// Proteksi Sesi
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelajar') {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='../index.php';</script>";
    exit();
}

// Cek apakah ada parameter 'slug' di URL
if (!isset($_GET['slug'])) {
    header("Location: index.php");
    exit();
}

$slug = $_GET['slug'];

// Ambil data materi berdasarkan slug, sekalian ambil nama kategori dan nama admin penulisnya
$stmt = $pdo->prepare("
    SELECT materi.*, kategori.nama_kategori, users.nama_lengkap AS nama_penulis
    FROM materi 
    JOIN kategori ON materi.id_kategori = kategori.id_kategori
    JOIN users ON materi.id_admin = users.id_user
    WHERE materi.slug = :slug
    LIMIT 1
");
$stmt->execute(['slug' => $slug]);
$materi = $stmt->fetch();

// Jika materi tidak ditemukan di database
if (!$materi) {
    echo "<script>alert('Materi tidak ditemukan!'); window.location='index.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($materi['judul']); ?> - TKJ E-Learning</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f9fafb; color: #1f2937; line-height: 1.7; }
        
        .navbar { background-color: #ffffff; padding: 15px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border-bottom: 1px solid #e5e7eb; }
        .navbar h2 { color: #2563eb; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.5px; }
        
        /* Container untuk membaca materi (dibuat lebih sempit agar nyaman dibaca seperti artikel) */
        .read-container { max-width: 800px; margin: 40px auto; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; }
        
        .btn-back { display: inline-block; margin-bottom: 20px; color: #3b82f6; text-decoration: none; font-weight: 600; }
        .btn-back:hover { text-decoration: underline; }

        /* Meta Info (Judul, Kategori, Penulis, Tanggal) */
        .materi-header { margin-bottom: 30px; border-bottom: 1px solid #e5e7eb; padding-bottom: 20px; }
        .badge { display: inline-block; padding: 5px 12px; background: #dbeafe; color: #1d4ed8; font-size: 0.8rem; font-weight: 700; border-radius: 20px; margin-bottom: 15px; text-transform: uppercase; }
        .materi-title { font-size: 2.2rem; color: #111827; margin-bottom: 10px; line-height: 1.3; }
        .materi-meta { color: #6b7280; font-size: 0.9rem; }

        /* Gambar Cover */
        .cover-img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 30px; }

        /* Area Konten (Tempat hasil TinyMCE ditampilkan) */
        .materi-content { font-size: 1.05rem; color: #374151; }
        
        /* Styling tambahan agar output tabel/gambar dari TinyMCE terlihat rapi di halaman siswa */
        .materi-content img { max-width: 100%; height: auto; border-radius: 6px; margin: 15px 0; }
        .materi-content ul, .materi-content ol { margin-left: 20px; margin-bottom: 15px; }
        .materi-content p { margin-bottom: 15px; }
        .materi-content pre, .materi-content code { background: #f3f4f6; padding: 5px 8px; border-radius: 4px; font-family: monospace; font-size: 0.95rem; color: #ef4444; }
        /* =========================================
   CSS RESPONSIVE UNTUK HP (Maksimal lebar 768px)
   ========================================= */
@media screen and (max-width: 768px) {
    /* Mengurangi jarak kosong (padding) di pinggir layar */
    body {
        padding: 15px !important; 
    }
    
    /* Menyesuaikan lebar kotak / card / form agar tidak terpotong */
    .container, .auth-card {
        width: 100% !important;
        padding: 20px 15px !important;
        margin: 0 !important;
        border-radius: 8px !important;
    }

    /* Jika ada teks judul yang kebesaran, kita kecilkan sedikit */
    h2 {
        font-size: 1.25rem !important;
    }

    /* Membuat tabel bisa digeser ke kanan-kiri (di-scroll) tanpa merusak layout web */
    table, .table-container {
        display: block !important;
        width: 100% !important;
        overflow-x: auto !important;
        white-space: nowrap !important;
    }

    /* Jika menggunakan flexbox (misal sidebar & konten utama) jadi atas-bawah */
    .flex-container {
        flex-direction: column !important;
    }
}    
    </style>
</head>
<body>

    <nav class="navbar">
        <h2>E-Learning TKJ</h2>
        <div class="nav-right">
            //<a href="index.php" style="color: #6b7280; text-decoration: none; font-weight: 600;">Dashboard Siswa</a>
        </div>
    </nav>

    <div class="read-container">
        <a href="index.php" class="btn-back">&larr; Kembali ke Daftar Materi</a>
        
        <div class="materi-header">
            <span class="badge"><?= htmlspecialchars($materi['nama_kategori']); ?></span>
            <h1 class="materi-title"><?= htmlspecialchars($materi['judul']); ?></h1>
            <div class="materi-meta">
                Ditulis oleh <b><?= htmlspecialchars($materi['nama_penulis']); ?></b> &bull; 
                Diperbarui pada <?= date('d M Y, H:i', strtotime($materi['updated_at'])); ?>
            </div>
        </div>

        <!-- Tampilkan Cover jika ada -->
        <?php if (!empty($materi['cover_image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($materi['cover_image']); ?>" alt="Cover" class="cover-img">
        <?php endif; ?>

        <!-- ISI MATERI -->
        <div class="materi-content">
            <!-- 
              PERHATIAN PENTING:
              Di sini kita TIDAK menggunakan htmlspecialchars() karena $materi['isi_materi'] 
              berisi tag HTML asli dari TinyMCE (seperti <b>, <p>, <img>).
              Jika di-htmlspecialchars, desainnya tidak akan terbaca melainkan muncul sebagai kode tag HTML biasa.
            -->
            <?= $materi['isi_materi']; ?>
        </div>
        
    </div>

</body>
</html>