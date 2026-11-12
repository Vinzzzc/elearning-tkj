<?php
// pelajar/index.php
session_start();
require '../config/database.php';

// Proteksi Sesi
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'pelajar') {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='../index.php';</script>";
    exit();
}

// 1. Ambil data kategori untuk tombol filter
$stmt_kat = $pdo->query("SELECT * FROM kategori");
$kategori_list = $stmt_kat->fetchAll();

// 2. Ambil parameter pencarian atau filter dari URL (jika ada)
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

// 3. Buat Query dinamis berdasarkan input siswa
$query = "
    SELECT materi.*, kategori.nama_kategori 
    FROM materi 
    JOIN kategori ON materi.id_kategori = kategori.id_kategori 
    WHERE 1=1
";
$params = [];

// Jika siswa mengetik kata kunci di kolom pencarian
if (!empty($keyword)) {
    $query .= " AND (materi.judul LIKE :keyword OR materi.deskripsi_singkat LIKE :keyword OR materi.isi_materi LIKE :keyword)";
    $params['keyword'] = '%' . $keyword . '%';
}

// Jika siswa memilih kategori tertentu
if (!empty($filter_kategori)) {
    $query .= " AND materi.id_kategori = :id_kategori";
    $params['id_kategori'] = $filter_kategori;
}

$query .= " ORDER BY materi.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$materi_list = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Belajar - TKJ E-Learning</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f9fafb; color: #1f2937; }
        
        /* =========================================
           NAVBAR & DROPDOWN PROFIL (UNIVERSAL)
           ========================================= */
        .navbar-modern {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 15px 40px;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .navbar-modern .logo {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: 0.5px;
        }
        .profile-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .profile-btn {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0;
            border-radius: 50%;
            transition: all 0.2s;
        }
        .profile-btn:hover {
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }
        .profile-btn img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid #e5e7eb;
            object-fit: cover;
        }
        .profile-dropdown {
            position: absolute;
            top: 60px;
            right: 0;
            background: #ffffff;
            width: 220px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid #e5e7eb;
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 9999;
        }
        .profile-dropdown.muncul {
            display: flex;
        }
        .dropdown-header {
            padding: 15px;
            background: #f8fafc;
        }
        .dropdown-header strong {
            display: block;
            color: #0f172a;
            font-size: 1rem;
        }
        .dropdown-header span {
            font-size: 0.8rem;
            color: #64748b;
        }
        .profile-dropdown hr {
            border: 0;
            height: 1px;
            background: #e2e8f0;
            margin: 0;
        }
        .profile-dropdown a {
            padding: 12px 15px;
            text-decoration: none;
            color: #334155;
            font-size: 0.9rem;
            transition: background 0.2s, padding-left 0.2s;
        }
        .profile-dropdown a:hover {
            background: #f1f5f9;
            padding-left: 20px;
        }
        .profile-dropdown a.menu-logout {
            color: #ef4444; 
            font-weight: 600;
        }
        .profile-dropdown a.menu-logout:hover {
            background: #fef2f2;
        }

        /* =========================================
           KONTEN UTAMA & GRID MATERI
           ========================================= */
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .header-text { margin-bottom: 30px; }
        .header-text h1 { font-size: 2.2rem; color: #111827; margin-bottom: 8px; }
        .header-text p { color: #6b7280; font-size: 1.1rem; }
        
        /* Form Pencarian & Filter */
        .search-filter-box { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; margin-bottom: 35px; display: flex; gap: 15px; flex-wrap: wrap; }
        .search-filter-box input[type="text"] { flex: 1; min-width: 250px; padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; outline: none; }
        .search-filter-box select { padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 1rem; background: white; outline: none; }
        .btn-search { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .btn-search:hover { background: #2563eb; }
        .btn-reset { background: #e5e7eb; color: #374151; border: none; padding: 10px 15px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-reset:hover { background: #d1d5db; }

        /* Grid Sistem Materi */
        .grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
            gap: 30px; 
            align-items: stretch;
        }
        
        .card { 
            background: white; 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
            transition: all 0.3s ease; 
            border: 1px solid #e5e7eb; 
            text-decoration: none; 
            display: flex; 
            flex-direction: column; 
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 12px 20px -3px rgba(0,0,0,0.1); border-color: #bfdbfe; }
        
        .card-cover { height: 180px; background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; font-weight: 600; background-size: cover; background-position: center; }
        .card-body { padding: 25px; flex: 1; display: flex; flex-direction: column; }
        .badge { display: inline-block; align-self: flex-start; padding: 5px 12px; background: #dbeafe; color: #1d4ed8; font-size: 0.75rem; font-weight: 700; border-radius: 20px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-title { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 10px; line-height: 1.4; }
        .card-text { color: #6b7280; font-size: 0.95rem; line-height: 1.6; flex: 1; }
        
        .empty-state { grid-column: 1 / -1; text-align: center; padding: 50px; background: white; border-radius: 12px; border: 1px dashed #d1d5db; color: #6b7280; }
        
        /* Styling Footer */
        .site-footer {
            margin-top: 60px;
            padding: 25px 40px;
            background-color: #ffffff;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #6b7280;
            font-size: 0.9rem;
            flex-wrap: wrap;
            gap: 15px;
        }
        .site-footer .footer-left { font-weight: 500; }
        .site-footer .footer-right a {
            color: #3b82f6; text-decoration: none; font-weight: 600; background: #eff6ff;
            padding: 6px 14px; border-radius: 20px; transition: 0.3s; display: inline-flex; align-items: center; gap: 6px;
        }
        .site-footer .footer-right a:hover { background: #dbeafe; color: #1d4ed8; }

        /* =========================================
           CSS RESPONSIVE UNTUK HP (Maksimal 768px)
           ========================================= */
        @media screen and (max-width: 768px) {
            .navbar-modern { padding: 12px 20px; }
            .navbar-modern .logo { font-size: 1.2rem; }
            .profile-btn img { width: 38px; height: 38px; }
            .profile-dropdown { top: 55px; }
            
            .container { padding: 0 15px; margin-top: 25px; }
            .header-text h1 { font-size: 1.8rem; }
            
            .grid { grid-template-columns: 1fr; }
            .card { width: 100%; }
            
            .site-footer { padding: 25px 20px; flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>
    
    <!-- NAVBAR -->
    <nav class="navbar-modern">
        <div class="logo">TKJ E-Learning</div>
        
        <div class="profile-wrapper">
            <button id="profile-btn" class="profile-btn">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama'] ?? 'User') ?>&background=3b82f6&color=fff&rounded=true&bold=true" alt="Profil">
            </button>

            <div id="profile-dropdown" class="profile-dropdown">
                <div class="dropdown-header">
                    <strong><?= htmlspecialchars($_SESSION['nama'] ?? 'Pelajar') ?></strong>
                    <span>Pelajar TKJ</span>
                </div>
                <hr>
                <a href="#">👤 Edit Profil</a>
                <a href="#">🔑 Ganti Password</a>
                <a href="../logout.php" class="menu-logout">🚪 Logout</a>
            </div>
        </div>
    </nav>

    <!-- WRAPPER UTAMA -->
    <div class="container">
        
        <div class="header-text">
            <h1>Materi Pembelajaran</h1>
            <p>Cari modul konfigurasi atau panduan praktikum yang kamu butuhkan.</p>
        </div>

        <!-- FORM PENCARIAN & FILTER KATEGORI -->
        <form action="" method="GET" class="search-filter-box">
            <input type="text" name="q" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari materi berdasarkan kata kunci...">
            
            <select name="kategori">
                <option value="">-- Semua Kategori --</option>
                <?php foreach ($kategori_list as $kat): ?>
                    <option value="<?= $kat['id_kategori']; ?>" <?= ($filter_kategori == $kat['id_kategori']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($kat['nama_kategori']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn-search">Cari Materi</button>
            
            <?php if (!empty($keyword) || !empty($filter_kategori)): ?>
                <a href="index.php" class="btn-reset">Reset</a>
            <?php endif; ?>
        </form>

        <!-- GRID DAFTAR MATERI -->
        <div class="grid">
            <?php if (count($materi_list) > 0): ?>
                <?php foreach ($materi_list as $row): ?>
                    <a href="baca-materi.php?slug=<?= $row['slug'] ?? ''; ?>" class="card">
                        
                        <?php if (!empty($row['cover_image'])): ?>
                            <div class="card-cover" style="background-image: url('../uploads/<?= htmlspecialchars($row['cover_image']); ?>');"></div>
                        <?php else: ?>
                            <div class="card-cover">TKJ Modul</div>
                        <?php endif; ?>

                        <div class="card-body">
                            <span class="badge"><?= htmlspecialchars($row['nama_kategori']); ?></span>
                            <h3 class="card-title"><?= htmlspecialchars($row['judul']); ?></h3>
                            <p class="card-text"><?= htmlspecialchars($row['deskripsi_singkat']); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3>Materi tidak ditemukan.</h3>
                    <p>Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
                </div>
            <?php endif; ?>
        </div>

    </div> <!-- Penutup div.container -->

    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="footer-left">
            &copy; Teknik Komputer & Jaringan <?= date('Y'); ?> E-Learning.
        </div>
        <div class="footer-right">
            <a href="https://saweria.co/vinzzzx" target="_blank">
                ☕ Beri Dukungan
            </a>
        </div>
    </footer>

    <!-- SCRIPT DROPDOWN PROFIL -->
    <script>
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('muncul');
        });

        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                profileDropdown.classList.remove('muncul');
            }
        });
    </script>
</body>
</html>