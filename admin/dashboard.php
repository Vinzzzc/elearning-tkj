<?php
// admin/dashboard.php
session_start();

// Proteksi Sesi
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Akses Ditolak! Anda bukan Admin.'); window.location='../index.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TKJ E-Learning</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { display: flex; min-height: 100vh; background-color: #f8fafc; color: #1e293b; overflow-x: hidden; }
        
        /* =========================================
           SIDEBAR STYLES (UNIVERSAL)
           ========================================= */
        .sidebar { 
            width: 260px; 
            background-color: #0f172a; /* Biru sangat gelap modern */
            color: white; 
            display: flex; 
            flex-direction: column; 
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            z-index: 1000;
            transition: left 0.3s ease-in-out;
        }
        .sidebar h2 { 
            padding: 25px 20px; 
            font-size: 1.4rem; 
            border-bottom: 1px solid #1e293b; 
            text-align: center; 
            font-weight: 700; 
            letter-spacing: 1px; 
            color: #38bdf8; /* Aksen biru muda */
        }
        .menu { flex: 1; padding-top: 20px; }
        .menu a { 
            display: flex; 
            align-items: center;
            padding: 15px 25px; 
            color: #94a3b8; 
            text-decoration: none; 
            transition: all 0.3s; 
            font-size: 0.95rem; 
            font-weight: 500;
        }
        .menu a:hover, .menu a.active { 
            background-color: #1e293b; 
            color: #ffffff; 
            border-left: 4px solid #38bdf8; 
            padding-left: 21px; 
        }
        .logout { 
            padding: 18px 20px; 
            background-color: #ef4444; 
            color: white; 
            text-align: center; 
            text-decoration: none; 
            transition: 0.3s; 
            font-weight: 600; 
        }
        .logout:hover { background-color: #dc2626; }

        /* =========================================
           MAIN CONTENT & HEADER
           ========================================= */
        .main-content { 
            flex: 1; 
            padding: 40px; 
            width: 100%;
            height: 100vh;
            overflow-y: auto;
        }
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 40px; 
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            border: 1px solid #f1f5f9;
        }
        .header-left { display: flex; align-items: center; gap: 15px; }
        .header-left h2 { font-size: 1.5rem; color: #0f172a; font-weight: 700; }
        
        .header-right { font-size: 0.95rem; color: #475569; }
        .header-right b { color: #0f172a; }

        /* Tombol Hamburger (Disembunyikan di PC) */
        .hamburger-btn {
            display: none;
            background: #f1f5f9;
            border: none;
            font-size: 1.5rem;
            color: #0f172a;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .hamburger-btn:hover { background: #e2e8f0; }

        /* =========================================
           CARD & BUTTON STYLES
           ========================================= */
        .card { 
            background: white; 
            padding: 35px; 
            border-radius: 12px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); 
            border: 1px solid #f1f5f9; 
        }
        .card h3 { color: #0f172a; margin-bottom: 12px; font-size: 1.3rem; }
        .card p { color: #64748b; line-height: 1.6; margin-bottom: 30px; font-size: 1rem; }
        
        .btn-primary { 
            display: inline-block;
            padding: 12px 24px; 
            background: #3b82f6; 
            color: white; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: 600; 
            transition: all 0.3s; 
            border: none; 
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        }
        .btn-primary:hover { 
            background: #2563eb; 
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3);
        }

        /* Latar Belakang Gelap (Overlay) saat menu HP terbuka */
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }

        /* =========================================
           MOBILE RESPONSIVE (Maks 768px)
           ========================================= */
        @media screen and (max-width: 768px) {
            .hamburger-btn { display: block; }
            
            .sidebar {
                position: fixed;
                height: 100vh;
                left: -260px; /* Sembunyikan ke kiri */
            }
            .sidebar.muncul {
                left: 0; /* Tampilkan saat class 'muncul' ditambahkan */
            }
            
            .sidebar-overlay.muncul {
                display: block;
                opacity: 1;
            }

            .main-content { padding: 20px 15px; }
            
            .header { 
                padding: 15px; 
                margin-bottom: 25px; 
                flex-direction: column; 
                align-items: flex-start;
                gap: 15px;
            }
            .header-right { align-self: flex-start; }
            
            .card { padding: 25px 20px; }
            .btn-primary { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>

    <!-- Overlay untuk versi HP -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <h2>TKJ ADMIN</h2>
        <div class="menu">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="kelola-materi.php">Kelola Materi</a>
            <a href="kelola-user.php">Manajemen User</a>
        </div>
        <a href="../logout.php" class="logout">Logout</a>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="header">
            <div class="header-left">
                <button class="hamburger-btn" id="hamburger-btn">☰</button>
                <h2>Dashboard</h2>
            </div>
            <div class="header-right">
                Halo, <b><?= htmlspecialchars($_SESSION['nama']); ?></b>
            </div>
        </div>

        <div class="card">
            <h3>Selamat Datang di Ruang Kendali</h3>
            <p>Dari sini kamu bisa mengelola modul pembelajaran, menambahkan panduan praktikum (seperti konfigurasi MikroTik atau Debian), dan mengunggah gambar topologi jaringan.</p>
            <a href="tambah-materi.php" class="btn-primary">+ Tambah Materi Baru</a>
        </div>
    </div>

    <!-- SCRIPT HAMBURGER MENU -->
    <script>
        const btn = document.getElementById('hamburger-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        // Fungsi Buka/Tutup Sidebar
        function toggleSidebar() {
            sidebar.classList.toggle('muncul');
            overlay.classList.toggle('muncul');
        }

        // Klik Tombol Hamburger
        btn.addEventListener('click', toggleSidebar);

        // Klik Area Gelap (Overlay) untuk menutup menu
        overlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>