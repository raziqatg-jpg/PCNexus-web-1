<?php
/**
 * get_produk_publik.php
 * Endpoint publik: kembalikan semua produk aktif untuk halaman belanja
 */
session_start();
header('Content-Type: application/json');

include __DIR__ . '/dp.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'produk' => []]); exit;
}

// Pastikan tabel produk ada
$conn->query("CREATE TABLE IF NOT EXISTS `produk` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `nama_toko` VARCHAR(150) DEFAULT '',
    `kategori` VARCHAR(50) NOT NULL,
    `merek` VARCHAR(100) NOT NULL,
    `nama_produk` VARCHAR(200) NOT NULL,
    `harga` BIGINT NOT NULL,
    `stok` INT NOT NULL DEFAULT 1,
    `kondisi` ENUM('Baru','Bekas','Refurbished') DEFAULT 'Baru',
    `berat` DECIMAL(5,2) DEFAULT 1.00,
    `deskripsi` TEXT,
    `foto` VARCHAR(255) DEFAULT '',
    `sold` INT NOT NULL DEFAULT 0,
    `rating` DECIMAL(3,1) DEFAULT 5.0,
    `status` ENUM('aktif','nonaktif','habis') DEFAULT 'aktif',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Kompatibilitas schema lama: copy seller_id ke user_id jika perlu.
$checkUserId = $conn->query("SHOW COLUMNS FROM `produk` LIKE 'user_id'");
if ($checkUserId && $checkUserId->num_rows === 0) {
    $conn->query("ALTER TABLE `produk` ADD COLUMN `user_id` INT NULL AFTER `id`");
}
$hasSellerId = $conn->query("SHOW COLUMNS FROM `produk` LIKE 'seller_id'");
if ($hasSellerId && $hasSellerId->num_rows > 0) {
    $conn->query("UPDATE `produk` SET `user_id` = `seller_id` WHERE `user_id` IS NULL");
}

$kategori = $_GET['kategori'] ?? '';
$search   = trim($_GET['q'] ?? '');

$sql    = "SELECT * FROM produk WHERE status = 'aktif' AND stok > 0";
$params = [];
$types  = '';

if ($kategori && $kategori !== 'all') {
    $sql     .= " AND kategori = ?";
    $params[] = $kategori;
    $types   .= 's';
}
if ($search) {
    $sql     .= " AND (nama_produk LIKE ? OR merek LIKE ? OR deskripsi LIKE ?)";
    $like     = '%' . $search . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= 'sss';
}
$sql .= " ORDER BY created_at DESC LIMIT 100";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res   = $stmt->get_result();
$items = [];
while ($row = $res->fetch_assoc()) $items[] = $row;
$stmt->close();

echo json_encode(['success' => true, 'produk' => $items]);
?>
