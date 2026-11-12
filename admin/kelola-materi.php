<?php
// admin/kelola-materi.php
session_start();
require '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak!");
}

// Ambil semua data materi beserta nama kategorinya
$stmt =$pdo->query("
    SELECT materi.id_materi, materi.judul, materi.created_at, materi.updated_at, kategori.nama_kategori 
    FROM materi 
    JOIN kategori ON materi.id_kategori = kategori.id_kategori
    ORDER BY materi.created_at DESC
");
$materi_list =$stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Materi - Admin TKJ</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f3f4f6; color: #1f2937; padding: 40px; }
        .container { max-width: 1000px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin: auto; }
        h2 { margin-bottom: 20px; color: #111827; }
        
        .btn-back { display: inline-block; margin-bottom: 20px; color: #6b7280; text-decoration: none; font-weight: 600; }
        .btn-add { display: inline-block; padding: 10px 15px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; margin-bottom: 20px; float: right; }
        
        /* Styling Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background-color: #f9fafb; font-weight: 600; color: #374151; }
        tr:hover { background-color: #f3f4f6; }
        
        /* Perbaikan Label Kategori */
        .badge { 
            display: inline-block; /* Wajib agar padding atas-bawah berfungsi */
            white-space: nowrap; /* Mencegah teks terpotong ke bawah */
            background: #dbeafe; 
            color: #1d4ed8; 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 0.8rem; 
            font-weight: 700; 
        }
        
        /* Perbaikan Tombol Aksi */
        .btn-edit, .btn-delete {
            display: inline-block; /* Wajib agar margin-bottom berfungsi */
            margin-bottom: 6px; /* Memberi jarak jika tombol turun ke bawah */
            padding: 7px 14px; 
            text-decoration: none; 
            border-radius: 4px; 
            font-size: 0.85rem; 
        }
        .btn-edit { background: #3b82f6; color: white; margin-right: 5px; }
        .btn-delete { background: #ef4444; color: white; }
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

    <div class="container">
        <a href="dashboard.php" class="btn-back">&larr; Kembali ke Dashboard</a>
        <a href="tambah-materi.php" class="btn-add">+ Tambah Materi Baru</a>
        
        <h2>Kelola Materi Pembelajaran</h2>

        <table>
            <thead>
                <tr>
                    <th>Judul Materi</th>
                    <th>Kategori</th>
                    <th>Dibuat Pada</th>
                    <th>Terakhir Update</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($materi_list as$row): ?>
                <tr>
                    <td><b><?= htmlspecialchars($row['judul']) ?></b></td>
                    <td><span class="badge"><?= htmlspecialchars($row['nama_kategori']) ?></span></td>
                    <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                    <td><?= date('d M Y, H:i', strtotime($row['updated_at'])) ?></td>
                    <td>
                        <a href="edit-materi.php?id=<?= $row['id_materi'] ?>" class="btn-edit">Edit</a>
                        <!-- Tambahkan konfirmasi javascript sebelum menghapus -->
                        <a href="hapus-materi.php?id=<?= $row['id_materi'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus materi ini? Gambar juga akan terhapus permanen!');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>