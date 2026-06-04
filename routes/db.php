<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "monitoring_toren";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

// Kalau file ini diakses langsung, tampilkan info koneksi
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    echo "Koneksi berhasil ke database: " . $db;
}
?>
