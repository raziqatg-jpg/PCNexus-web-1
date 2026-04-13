<?php
session_start();
require_once __DIR__ . '/../fungsi/dp.php'; // Sesuaikan path ke file koneksi Anda
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Harap login terlebih dahulu']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method tidak valid']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Pastikan tabel video tersedia.
$conn->query("CREATE TABLE IF NOT EXISTS `videos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `category` VARCHAR(50) DEFAULT 'tutorial',
    `video_url` VARCHAR(500) DEFAULT '',
    `video_file` VARCHAR(255) DEFAULT '',
    `thumbnail` VARCHAR(255) DEFAULT '',
    `description` TEXT,
    `total_price` BIGINT DEFAULT 0,
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS `video_spareparts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `video_id` INT NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    `specification` TEXT,
    `price` BIGINT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Buat folder jika belum ada
$video_dir = __DIR__ . '/../uploads/videos/';
$thumbnail_dir = __DIR__ . '/../uploads/thumbnails/';
if (!file_exists($video_dir)) mkdir($video_dir, 0777, true);
if (!file_exists($thumbnail_dir)) mkdir($thumbnail_dir, 0777, true);

$title = trim($_POST['title'] ?? '');
$category = $_POST['category'] ?? 'tutorial';
$video_url = trim($_POST['video_url'] ?? '');
$description = trim($_POST['description'] ?? '');
$spareparts_json = $_POST['spareparts'] ?? '[]';

if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Judul video wajib diisi']);
    exit;
}

if (empty($video_url) && (!isset($_FILES['video_file']) || $_FILES['video_file']['error'] === UPLOAD_ERR_NO_FILE)) {
    echo json_encode(['success' => false, 'message' => 'Isi URL video atau upload file video']);
    exit;
}

// Upload video file
$video_path = '';
if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
    $max_size = 100 * 1024 * 1024; // 100MB
    if ($_FILES['video_file']['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'Ukuran video maksimal 100MB']);
        exit;
    }
    $ext = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
    $allowed = ['mp4', 'webm', 'mov'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Format tidak didukung (mp4, webm, mov)']);
        exit;
    }
    $filename = 'video_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    if (move_uploaded_file($_FILES['video_file']['tmp_name'], $video_dir . $filename)) {
        $video_path = $filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal upload video, cek permission folder']);
        exit;
    }
} elseif (isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
    echo json_encode(['success' => false, 'message' => 'Upload video gagal (kode error: ' . $_FILES['video_file']['error'] . ')']);
    exit;
}

// Upload thumbnail
$thumbnail_path = '';
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
    $allowed_img = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($ext, $allowed_img)) {
        $thumbname = 'thumb_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_dir . $thumbname)) {
            $thumbnail_path = $thumbname;
        }
    }
} elseif (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] !== UPLOAD_ERR_NO_FILE) {
    echo json_encode(['success' => false, 'message' => 'Upload thumbnail gagal (kode error: ' . $_FILES['thumbnail']['error'] . ')']);
    exit;
}

// Simpan ke tabel videos
$query = "INSERT INTO videos (user_id, title, category, video_url, video_file, thumbnail, description, created_at) 
          VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query insert video tidak valid']);
    exit;
}
mysqli_stmt_bind_param($stmt, 'issssss', $user_id, $title, $category, $video_url, $video_path, $thumbnail_path, $description);

if (mysqli_stmt_execute($stmt)) {
    $video_id = mysqli_insert_id($conn);
    
    // Simpan sparepart
    $spareparts = json_decode($spareparts_json, true);
    if (!is_array($spareparts)) {
        $spareparts = [];
    }
    $total_price = 0;
    if (is_array($spareparts) && count($spareparts) > 0) {
        $part_query = "INSERT INTO video_spareparts (video_id, name, specification, price) VALUES (?, ?, ?, ?)";
        $part_stmt = mysqli_prepare($conn, $part_query);
        if (!$part_stmt) {
            echo json_encode(['success' => false, 'message' => 'Query sparepart tidak valid: ' . mysqli_error($conn)]);
            exit;
        }
        foreach ($spareparts as $part) {
            $name = trim($part['name'] ?? '');
            $spec = trim($part['spec'] ?? '');
            $price = (int)($part['price'] ?? 0);
            if ($name && $price > 0) {
                mysqli_stmt_bind_param($part_stmt, 'issi', $video_id, $name, $spec, $price);
                mysqli_stmt_execute($part_stmt);
                $total_price += $price;
            }
        }
    }
    
    // Update total_price di videos
    mysqli_query($conn, "UPDATE videos SET total_price = $total_price WHERE id = $video_id");
    
    echo json_encode(['success' => true, 'message' => 'Video berhasil diupload']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . mysqli_error($conn)]);
}
mysqli_stmt_close($stmt);
?>