<?php
// logout.php
session_start();
session_unset();    // Kosongkan semua data session
session_destroy();  // Hancurkan session
header("Location: index.php"); // Arahkan kembali ke halaman login
exit();
?>