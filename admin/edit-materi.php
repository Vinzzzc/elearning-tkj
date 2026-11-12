<?php
// admin/edit-materi.php
session_start();
require '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak!");
}

$id_materi = $_GET['id'];

// Ambil data materi saat ini
$stmt = $pdo->prepare("SELECT * FROM materi WHERE id_materi = :id");
$stmt->execute(['id' => $id_materi]);
$materi = $stmt->fetch();

if (!$materi) die("Materi tidak ditemukan!");

// Ambil list kategori
$stmt_kat = $pdo->query("SELECT * FROM kategori");
$kategori_list = $stmt_kat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Materi</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
    <script>
      tinymce.init({
        selector: '#isi_materi',
        plugins: 'image link lists table code',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
        height: 500
      });
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f3f4f6; color: #1f2937; padding: 40px; }
        .container { max-width: 900px; background: white; padding: 30px; border-radius: 12px; margin: auto; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; }
        input[type="text"], select, input[type="file"] { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; }
        .btn-submit { padding: 12px 25px; background: #f59e0b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Materi</h2>
        
        <form action="proses-edit.php" method="POST" enctype="multipart/form-data">
            <!-- ID Materi disembunyikan agar diketahui oleh proses-edit.php -->
            <input type="hidden" name="id_materi" value="<?= $materi['id_materi'] ?>">
            <input type="hidden" name="cover_lama" value="<?= $materi['cover_image'] ?>">

            <div class="form-group">
                <label>Judul Materi</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($materi['judul']) ?>" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="id_kategori" required>
                    <?php foreach ($kategori_list as $kat) : ?>
                        <option value="<?= $kat['id_kategori'] ?>" <?= ($materi['id_kategori'] == $kat['id_kategori']) ? 'selected' : '' ?>>
                            <?= $kat['nama_kategori'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Deskripsi Singkat</label>
                <input type="text" name="deskripsi_singkat" value="<?= htmlspecialchars($materi['deskripsi_singkat']) ?>" required>
            </div>

            <div class="form-group">
                <label>Ganti Gambar Cover (Kosongkan jika tidak ingin ganti)</label>
                <?php if(!empty($materi['cover_image'])): ?>
                    <p style="font-size: 0.8rem; color: #ef4444; margin-bottom: 10px;">Cover saat ini: <?= $materi['cover_image'] ?></p>
                <?php endif; ?>
                <input type="file" name="cover_image" accept="image/*">
            </div>

            <div class="form-group">
                <label>Isi Materi</label>
                <!-- Menampilkan data lama ke dalam textarea -->
                <textarea id="isi_materi" name="isi_materi"><?= htmlspecialchars($materi['isi_materi']) ?></textarea>
            </div>

            <button type="submit" class="btn-submit">Simpan Perubahan</button>
            <a href="kelola-materi.php" style="margin-left: 15px; color: #6b7280; text-decoration: none;">Batal</a>
        </form>
    </div>
</body>
</html>