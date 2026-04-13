<?php
/**
 * update_profil.php
 * Simpan perubahan profil user ke DB
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Harus login']); exit;
}

include __DIR__ . '/../../fungsi/dp.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'DB error']); exit;
}

$data     = json_decode(file_get_contents('php://input'), true);
$user_id  = (int)$_SESSION['user_id'];
$nama     = trim($data['nama'] ?? '');
$bio      = trim($data['bio'] ?? '');
$email    = trim($data['email'] ?? '');

if (!$nama) {
    echo json_encode(['success' => false, 'message' => 'Nama tidak boleh kosong']); exit;
}

// Cek apakah ada kolom bio (tambahkan jika belum ada)
$check = $conn->query("SHOW COLUMNS FROM `pcnexus` LIKE 'bio'");
if ($check && $check->num_rows === 0) {
    $conn->query("ALTER TABLE `pcnexus` ADD COLUMN `bio` TEXT DEFAULT NULL");
}

$stmt = $conn->prepare("UPDATE pcnexus SET nama_lengkap=?, bio=?, Email=? WHERE id=?");
$stmt->bind_param('sssi', $nama, $bio, $email, $user_id);

if ($stmt->execute()) {
    $_SESSION['nama']  = $nama;
    $_SESSION['email'] = $email;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}
$stmt->close();
?>
