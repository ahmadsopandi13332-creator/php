<?php
// koneksi.php - Database Connection

// Konfigurasi dari environment variables
$host = getenv('MYSQLHOST') ?: "mysql.railway.internal";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "fwlGPXZDNfmqzGKEyVRVxEchqPJetVvi";
$db   = getenv('MYSQLDATABASE') ?: "railway";
$port = getenv('MYSQLPORT') ?: 3306;

// Buat koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

// Cek koneksi
if (!$koneksi) {
    error_log("Koneksi gagal: " . mysqli_connect_error());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit();
}

// Set charset
mysqli_set_charset($koneksi, "utf8mb4");
?>
