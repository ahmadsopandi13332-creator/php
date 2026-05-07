<?php
// index.php - Main Router untuk Railway

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Include koneksi database
require_once 'koneksi.php';

// Dapatkan URI dan Method
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// ============================================
// ROUTING
// ============================================

// 1. API ENDPOINTS (diutamakan)
if (strpos($request_uri, '/api') === 0) {
    // Set header API
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    
    // Handle preflight
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    
    // Panggil API handler
    require_once 'api.php';
    exit();
}

// 2. WEB INTERFACE (HTML)
?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP CRUD Railway</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 8px; text-align: left; }
        form { margin: 20px 0; }
        input, button { padding: 8px; margin: 5px; }
    </style>
</head>
<body>
    <h2>Tambah Data</h2>
    <form method="POST">
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="password" name="sandi" placeholder="Password" required>
        <button type="submit" name="tambah">Simpan</button>
    </form>

    <?php
    // Logika Create
    if(isset($_POST['tambah'])){
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $sandi = mysqli_real_escape_string($koneksi, $_POST['sandi']);
        
        // Hash password untuk keamanan
        $sandi_hash = password_hash($sandi, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (nama, sandi) VALUES('$nama', '$sandi_hash')";
        if(mysqli_query($koneksi, $query)) {
            echo "<p style='color: green;'>Data berhasil ditambah!</p>";
        } else {
            echo "<p style='color: red;'>Error: " . mysqli_error($koneksi) . "</p>";
        }
    }

    // Logika Delete
    if(isset($_GET['hapus'])){
        $id = (int)$_GET['hapus'];
        mysqli_query($koneksi, "DELETE FROM users WHERE id=$id");
        header("Location: index.php");
        exit();
    }
    ?>

    <h2>Data Users</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Aksi</th>
        </tr>
        <?php
        $data = mysqli_query($koneksi, "SELECT id, nama FROM users");
        while($d = mysqli_fetch_array($data)){
        ?>
        <tr>
            <td><?php echo htmlspecialchars($d['id']); ?></td>
            <td><?php echo htmlspecialchars($d['nama']); ?></td>
            <td>
                <a href="index.php?hapus=<?php echo $d['id']; ?>" 
                   onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
    
    <hr>
    <p>API Endpoint: <a href="/api">/api</a></p>
</body>
</html>
