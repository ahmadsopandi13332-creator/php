<?php
// MENGIZINKAN CORS - HARUS DI PALING ATAS, SEBELUM APAPUN
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// PHP akan mencoba mengambil data asli dari Railway dulu, 
// kalau tidak ada baru pakai data di dalam kutip.
$host = getenv('MYSQLHOST') ?: "mysql.railway.internal";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "fwlGPXZDNfmqzGKEyVRVxEchqPJetVvi";
$db   = getenv('MYSQLDATABASE') ?: "railway";
$port = getenv('MYSQLPORT') ?: 3306;

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    // Menampilkan error yang lebih spesifik jika gagal.
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
