<?php
// config/alert.php

/**
 * Fungsi global untuk menampilkan popup SweetAlert2 yang elegan di tengah halaman.
 * 
 * @param string $title Judul alert (contoh: "Berhasil!", "Gagal!")
 * @param string $text Pesan informasi di dalam alert
 * @param string $icon Jenis icon ('success', 'error', 'warning', 'info', 'question')
 * @param string $redirect Tujuan URL setelah tombol OK diklik (kosongkan jika hanya ingin kembali/reload)
 */
function tampilkan_alert($title, $text, $icon, $redirect = '') {
    // Jika redirect kosong, arahkan kembali ke halaman sebelumnya
    $action_js = !empty($redirect) 
        ? "window.location = '$redirect';" 
        : "window.history.back();";

    echo "
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$title</title>
        <!-- CDN SweetAlert2 & Google Fonts Inter -->
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        </style>
    </head>
    <body>
        <script>
            Swal.fire({
                title: '" . addslashes($title) . "',
                text: '" . addslashes($text) . "',
                icon: '" . $icon . "',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3b82f6',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    {$action_js}
                }
            });
        </script>
    </body>
    </html>
    ";
    exit();
}
?>