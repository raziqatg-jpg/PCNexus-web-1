<?php
session_start();
require_once __DIR__ . '/../../fungsi/dp.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Anda belum login']);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$input = is_array($input) ? $input : [];

$store_name = $input['store_name'] ?? '';
$store_phone = $input['phone'] ?? '';
$store_desc = $input['store_desc'] ?? '';

if (empty($store_name)) {
    echo json_encode(['success' => false, 'message' => 'Nama toko harus diisi']);
    exit;
}

// Pastikan kolom seller tersedia untuk database lama.
$columns_to_add = [
    'is_seller'      => "ALTER TABLE `pcnexus` ADD COLUMN `is_seller` TINYINT(1) NOT NULL DEFAULT 0",
    'nama_toko'      => "ALTER TABLE `pcnexus` ADD COLUMN `nama_toko` VARCHAR(150) DEFAULT NULL",
    'no_hp_toko'     => "ALTER TABLE `pcnexus` ADD COLUMN `no_hp_toko` VARCHAR(20) DEFAULT NULL",
    'deskripsi_toko' => "ALTER TABLE `pcnexus` ADD COLUMN `deskripsi_toko` TEXT DEFAULT NULL",
    'seller_since'   => "ALTER TABLE `pcnexus` ADD COLUMN `seller_since` DATE DEFAULT NULL",
];
foreach ($columns_to_add as $col => $sql) {
    $check = $conn->query("SHOW COLUMNS FROM `pcnexus` LIKE '$col'");
    if ($check && $check->num_rows === 0) {
        $conn->query($sql);
    }
}

// Update user menjadi penjual (tabel pcnexus)
$query = "UPDATE pcnexus SET is_seller = 1, nama_toko = ?, no_hp_toko = ?, deskripsi_toko = ?, seller_since = CURDATE() WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Query update penjual tidak valid']);
    exit;
}
mysqli_stmt_bind_param($stmt, 'sssi', $store_name, $store_phone, $store_desc, $user_id);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['is_seller'] = true;
    $_SESSION['nama_toko'] = $store_name;
    $_SESSION['seller_store_name'] = $store_name;
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'message' => 'Berhasil menjadi penjual']);
} else {
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data penjual']);
}
?>