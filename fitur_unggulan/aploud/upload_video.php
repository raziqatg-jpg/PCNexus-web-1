<?php
session_start();
require_once __DIR__ . '/../../fungsi/dp.php';

// Pastikan tabel videos ada
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

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, $_POST['title'] ?? '');
$category = mysqli_real_escape_string($conn, $_POST['category'] ?? 'tutorial');
$video_url = mysqli_real_escape_string($conn, $_POST['video_url'] ?? '');
$description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
$spareparts = json_decode($_POST['spareparts'] ?? '[]', true);

if (empty($title)) {
    echo json_encode(['success' => false, 'message' => 'Judul video wajib diisi']);
    exit;
}

// Upload video file
$video_path = '';
if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/videos/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $ext = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $target = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target)) {
        $video_path = $filename;
    }
}

// Upload thumbnail
$thumbnail_path = '';
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/thumbnails/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $target = $upload_dir . $filename;
    
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target)) {
        $thumbnail_path = $filename;
    }
}

// Hitung total harga dari sparepart
$total_price = 0;
foreach ($spareparts as $part) {
    $total_price += intval($part['price']);
}

// Insert video
$query = "INSERT INTO videos (user_id, title, category, video_url, video_file, thumbnail, description, total_price, created_at) 
          VALUES ($user_id, '$title', '$category', '$video_url', '$video_path', '$thumbnail_path', '$description', $total_price, NOW())";
mysqli_query($conn, $query);
$video_id = mysqli_insert_id($conn);

// Insert spareparts
foreach ($spareparts as $part) {
    $name = mysqli_real_escape_string($conn, $part['name']);
    $spec = mysqli_real_escape_string($conn, $part['spec']);
    $price = intval($part['price']);
    
    $query_part = "INSERT INTO video_spareparts (video_id, name, specification, price) 
                   VALUES ($video_id, '$name', '$spec', $price)";
    mysqli_query($conn, $query_part);
}

echo json_encode(['success' => true, 'video_id' => $video_id]);
?>