<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - E-Learning TKJ</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px; }
        .auth-card { background: #ffffff; width: 100%; max-width: 450px; padding: 40px 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #f3f4f6; }
        .auth-header { text-align: center; margin-bottom: 30px; }
        .auth-header h2 { color: #111827; font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; }
        .auth-header p { color: #6b7280; font-size: 0.9rem; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; color: #374151; font-size: 0.9rem; font-weight: 500; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem; color: #1f2937; transition: all 0.3s; background: #f9fafb; }
        .form-control:focus { outline: none; border-color: #3b82f6; background: #ffffff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .btn-primary { width: 100%; padding: 12px; background: #10b981; color: white; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.3s; margin-top: 10px; }
        .btn-primary:hover { background: #059669; }
        .auth-footer { text-align: center; margin-top: 25px; font-size: 0.9rem; color: #6b7280; }
        .auth-footer a { color: #10b981; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .auth-footer a:hover { color: #047857; text-decoration: underline; }
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

    <div class="auth-card">
        <div class="auth-header">
            <h2>Buat Akun Baru</h2>
            <p>Bergabunglah untuk mulai belajar materi TKJ</p>
        </div>

        <form action="proses-register.php" method="POST">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Davin Rayhan" required>
            </div>

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Buat username (tanpa spasi)" required>
            </div>

            <div class="form-group">
                <label>Alamat Email</label>
                <input type="email" name="email" class="form-control" placeholder="contoh@email.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Buat password yang kuat" required>
            </div>

            <!-- Tombol register menggunakan warna hijau (Emerald) agar beda dengan login -->
            <button type="submit" class="btn-primary">Daftar Sekarang</button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="index.php">Masuk di sini</a>
        </div>
    </div>

</body>
</html>