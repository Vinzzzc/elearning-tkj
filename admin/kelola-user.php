<?php
// admin/kelola-user.php
session_start();
require '../config/database.php';

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Akses Ditolak!");
}

// Ambil semua data user dari database
$stmt = $pdo->query("SELECT id_user, nama_lengkap, username, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin TKJ</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f3f4f6; color: #1f2937; padding: 40px; }
        .container { max-width: 1000px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin: auto; }
        h2 { margin-bottom: 20px; color: #111827; }
        
        .btn-back { display: inline-block; margin-bottom: 20px; color: #6b7280; text-decoration: none; font-weight: 600; }
        .btn-back:hover { color: #374151; }

        /* Styling Table */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
        th { background-color: #f9fafb; font-weight: 600; color: #374151; }
        tr:hover { background-color: #f3f4f6; }
        
        /* Badge Role */
        .badge-admin { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; }
        .badge-pelajar { background: #dbeafe; color: #1d4ed8; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; }

        /* Form Ubah Role langsung di tabel */
        form.inline-form { display: flex; gap: 8px; align-items: center; }
        select { padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 4px; font-size: 0.85rem; background: white; }
        .btn-save { background: #3b82f6; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem; font-weight: 600; }
        .btn-save:hover { background: #2563eb; }
        
        .btn-delete { background: #ef4444; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 0.85rem; display: inline-block; }
        .btn-delete:hover { background: #dc2626; }
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
        <h2>Manajemen Pengguna & Hak Akses</h2>

        <table>
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>Username / Email</th>
                    <th>Role Saat Ini</th>
                    <th>Ubah Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><b><?= htmlspecialchars($u['nama_lengkap']) ?></b></td>
                    <td>
                        <?= htmlspecialchars($u['username']) ?><br>
                        <small style="color: #6b7280;"><?= htmlspecialchars($u['email']) ?></small>
                    </td>
                    <td>
                        <?php if ($u['role'] == 'admin'): ?>
                            <span class="badge-admin">Admin</span>
                        <?php else: ?>
                            <span class="badge-pelajar">Pelajar</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Form untuk mengubah role secara instan -->
                        <form action="proses-role.php" method="POST" class="inline-form">
                            <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">
                            <select name="role">
                                <option value="pelajar" <?= ($u['role'] == 'pelajar') ? 'selected' : '' ?>>Pelajar</option>
                                <option value="admin" <?= ($u['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <button type="submit" class="btn-save">Simpan</button>
                        </form>
                    </td>
                    <td>
                        <!-- Mencegah admin menghapus akunnya sendiri yang sedang aktif login -->
                        <?php if ($u['id_user'] != $_SESSION['id_user']): ?>
                            <a href="hapus-user.php?id=<?= $u['id_user'] ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus akun ini?');">Hapus</a>
                        <?php else: ?>
                            <small style="color: #9ca3af; font-style: italic;">Akun Anda</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>