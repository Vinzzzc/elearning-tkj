<?php
// admin/tambah-materi.php
session_start();
require '../config/database.php'; // Panggil koneksi database

// Proteksi Sesi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses Ditolak!'); window.location='../index.php';</script>";
    exit();
}

// Ambil data kategori dari database untuk dropdown
$stmt = $pdo->query("SELECT * FROM kategori");
$kategori_list = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Materi - Admin TKJ</title>
    
    <!-- Memanggil TinyMCE dari CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
    
    <!-- Script untuk mengaktifkan TinyMCE pada textarea -->
    <script>
      tinymce.init({
        selector: '#isi_materi',
        plugins: 'image link lists table code',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
        height: 500,
        // Fitur ini memungkinkan admin drag & drop gambar langsung ke editor (gambar di-convert ke base64 otomatis)
        paste_data_images: true 
      });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f3f4f6; color: #1f2937; padding: 40px; }
        
        .container { max-width: 900px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin: auto; }
        h2 { margin-bottom: 25px; color: #111827; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: #374151; }
        input[type="text"], select, input[type="file"] { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; }
        
        .btn-submit { padding: 12px 25px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: 0.3s; }
        .btn-submit:hover { background: #2563eb; }
        .btn-back { display: inline-block; margin-bottom: 20px; color: #6b7280; text-decoration: none; font-weight: 600; }
        .btn-back:hover { color: #374151; }
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
        <h2>Buat Materi Baru</h2>

        <!-- Form akan dikirim ke proses-materi.php, enctype wajib ada untuk upload file -->
        <form action="proses-materi.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label>Judul Materi</label>
                <input type="text" name="judul" required placeholder="Contoh: Konfigurasi Dasar MikroTik">
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="id_kategori" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($kategori_list as $kat) : ?>
                        <option value="<?= $kat['id_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Deskripsi Singkat (Muncul di kartu menu siswa)</label>
                <input type="text" name="deskripsi_singkat" required placeholder="Jelaskan sedikit tentang materi ini...">
            </div>

            <div class="form-group">
                <label>Upload Gambar Cover (Opsional)</label>
                <input type="file" name="cover_image" accept="image/*">
            </div>

            <div class="form-group">
                <label>Isi Materi (Panduan & Langkah-langkah)</label>
                <!-- Textarea ini akan disulap jadi Microsoft Word oleh TinyMCE -->
                <textarea id="isi_materi" name="isi_materi"></textarea>
            </div>

            <button type="submit" class="btn-submit">Posting Materi</button>
        </form>
    </div>

</body>
</html>