<?php
// PHP akan mencoba mengambil data asli dari Railway dulu, 
// kalau tidak ada baru pakai data di dalam kutip.
$host = getenv('MYSQLHOST') ?: "mysql.railway.internal";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "fwlGPXZDNfmqzGKEyVRVxEchqPJetVvi";
$db   = getenv('MYSQLDATABASE') ?: "railway";
$port = getenv('MYSQLPORT') ?: 3306;

$koneksi = mysqli_connect($host, $user, $pass, $db, $port);

if (!$koneksi) {
    // Menampilkan error yang lebih spesifik jika gagal
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
