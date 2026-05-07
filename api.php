<?php
// api.php - API Handler

global $koneksi;

$method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$response = [];

// Fungsi helper untuk get input
function getInput() {
    $input = json_decode(file_get_contents('php://input'), true);
    return $input ?: $_POST;
}

// Fungsi send response
function sendResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit();
}

// Route tanpa ID (collection)
if ($request_uri == '/api' || $request_uri == '/api/') {
    switch ($method) {
        case 'GET':
            // Ambil semua users
            $query = mysqli_query($koneksi, "SELECT id, nama, sandi FROM users");
            $users = [];
            while ($row = mysqli_fetch_assoc($query)) {
                $users[] = $row;
            }
            sendResponse(["status" => "success", "data" => $users]);
            break;

        case 'POST':
            // Tambah user baru
            $input = getInput();
            $nama = $input['nama'] ?? null;
            $sandi = $input['sandi'] ?? null;

            if (!$nama || !$sandi) {
                sendResponse(["status" => "error", "message" => "Nama dan sandi wajib diisi"], 400);
            }

            $nama = mysqli_real_escape_string($koneksi, $nama);
            $sandi = mysqli_real_escape_string($koneksi, $sandi);
            
            $query = "INSERT INTO users (nama, sandi) VALUES ('$nama', '$sandi')";
            if (mysqli_query($koneksi, $query)) {
                sendResponse(["status" => "success", "message" => "Data berhasil ditambah", "id" => mysqli_insert_id($koneksi)], 201);
            } else {
                sendResponse(["status" => "error", "message" => mysqli_error($koneksi)], 500);
            }
            break;

        default:
            sendResponse(["status" => "error", "message" => "Method tidak diizinkan"], 405);
    }
}

// Route dengan ID (single item)
if (preg_match('/^\/api\/(\d+)$/', $request_uri, $matches)) {
    $id = (int)$matches[1];
    
    switch ($method) {
        case 'GET':
            // Ambil satu user
            $query = mysqli_query($koneksi, "SELECT id, nama, sandi FROM users WHERE id=$id");
            if ($row = mysqli_fetch_assoc($query)) {
                sendResponse(["status" => "success", "data" => $row]);
            } else {
                sendResponse(["status" => "error", "message" => "User tidak ditemukan"], 404);
            }
            break;

        case 'PUT':
            // Update user
            $input = getInput();
            $nama = $input['nama'] ?? null;
            $sandi = $input['sandi'] ?? null;

            if (!$nama || !$sandi) {
                sendResponse(["status" => "error", "message" => "Nama dan sandi wajib diisi"], 400);
            }

            $nama = mysqli_real_escape_string($koneksi, $nama);
            $sandi = mysqli_real_escape_string($koneksi, $sandi);
            
            $query = "UPDATE users SET nama='$nama', sandi='$sandi' WHERE id=$id";
            if (mysqli_query($koneksi, $query)) {
                if (mysqli_affected_rows($koneksi) > 0) {
                    sendResponse(["status" => "success", "message" => "User berhasil diupdate"]);
                } else {
                    sendResponse(["status" => "error", "message" => "User tidak ditemukan"], 404);
                }
            } else {
                sendResponse(["status" => "error", "message" => mysqli_error($koneksi)], 500);
            }
            break;

        case 'DELETE':
            // Hapus user
            $query = "DELETE FROM users WHERE id=$id";
            if (mysqli_query($koneksi, $query)) {
                if (mysqli_affected_rows($koneksi) > 0) {
                    sendResponse(["status" => "success", "message" => "User berhasil dihapus"]);
                } else {
                    sendResponse(["status" => "error", "message" => "User tidak ditemukan"], 404);
                }
            } else {
                sendResponse(["status" => "error", "message" => mysqli_error($koneksi)], 500);
            }
            break;

        default:
            sendResponse(["status" => "error", "message" => "Method tidak diizinkan"], 405);
    }
}

// 404 untuk route API yang tidak dikenal
sendResponse(["status" => "error", "message" => "API endpoint tidak ditemukan"], 404);
?>
