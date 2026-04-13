<?php
session_start();
$debug = false;
function dbg($msg) {
    global $debug;
    error_log($msg);
    if ($debug) echo "<p>DEBUG: $msg</p>";
}

include __DIR__ . '/dp.php';

if (!isset($conn) || !$conn) {
    die("Koneksi database gagal");
}

$table = 'pcnexus';

// Pastikan tabel ada + kolom lengkap termasuk is_seller, nama_toko, dll
$createSql = "CREATE TABLE IF NOT EXISTS `$table` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(100) NOT NULL,
    `nama_lengkap` VARCHAR(150) NOT NULL,
    `Email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `foto_profil` VARCHAR(255) NOT NULL DEFAULT 'default.png',
    `is_seller` TINYINT(1) NOT NULL DEFAULT 0,
    `nama_toko` VARCHAR(150) DEFAULT NULL,
    `no_hp_toko` VARCHAR(20) DEFAULT NULL,
    `deskripsi_toko` TEXT DEFAULT NULL,
    `seller_since` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (!$conn->query($createSql)) {
    die("Gagal memastikan tabel: " . $conn->error);
}

// Tambahkan kolom jika belum ada (untuk tabel lama)
$columns_to_add = [
    'foto_profil'     => "ALTER TABLE `$table` ADD COLUMN `foto_profil` VARCHAR(255) NOT NULL DEFAULT 'default.png'",
    'is_seller'       => "ALTER TABLE `$table` ADD COLUMN `is_seller` TINYINT(1) NOT NULL DEFAULT 0",
    'nama_toko'       => "ALTER TABLE `$table` ADD COLUMN `nama_toko` VARCHAR(150) DEFAULT NULL",
    'no_hp_toko'      => "ALTER TABLE `$table` ADD COLUMN `no_hp_toko` VARCHAR(20) DEFAULT NULL",
    'deskripsi_toko'  => "ALTER TABLE `$table` ADD COLUMN `deskripsi_toko` TEXT DEFAULT NULL",
    'seller_since'    => "ALTER TABLE `$table` ADD COLUMN `seller_since` DATE DEFAULT NULL",
];
foreach ($columns_to_add as $col => $sql) {
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
    if ($check && $check->num_rows === 0) {
        $conn->query($sql);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../daftar.php");
    exit;
}

$username     = trim($_POST['username'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$email        = trim($_POST['email'] ?? '');
$password     = $_POST['password'] ?? '';

if (empty($username) || empty($nama_lengkap) || empty($email) || empty($password)) {
    header("Location: ../daftar.php?error=Semua+field+wajib+diisi");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../daftar.php?error=Email+tidak+valid");
    exit;
}

// Cek email sudah ada
$stmt = $conn->prepare("SELECT id FROM `$table` WHERE Email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
$existing_id = null;
if ($stmt->num_rows > 0) {
    $stmt->bind_result($existing_id);
    $stmt->fetch();
}
$stmt->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);

$foto_profil = 'default.png';
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === 0) {
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
    if (in_array($ext, $allowed_ext)) {
        $foto_profil = 'user_' . time() . '.' . $ext;
        $upload_dir  = __DIR__ . '/../uploads/profile/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        if (!move_uploaded_file($_FILES['foto_profil']['tmp_name'], $upload_dir . $foto_profil)) {
            $foto_profil = 'default.png';
        }
    }
}

if ($existing_id) {
    $stmt = $conn->prepare("UPDATE `$table` SET username=?, nama_lengkap=?, password=?, foto_profil=? WHERE id=?");
    $stmt->bind_param('ssssi', $username, $nama_lengkap, $hashed, $foto_profil, $existing_id);
    if (!$stmt->execute()) {
        $stmt->close();
        header("Location: ../daftar.php?popup=server_error");
        exit;
    }
    $stmt->close();
    $user_id = $existing_id;
} else {
    // FIX: $username1 -> $username (bug lama)
    $stmt = $conn->prepare("INSERT INTO `$table` (username, nama_lengkap, Email, password, foto_profil) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $username, $nama_lengkap, $email, $hashed, $foto_profil);
    if (!$stmt->execute()) {
        $stmt->close();
        header("Location: ../daftar.php?popup=server_error");
        exit;
    }
    $user_id = $stmt->insert_id;
    $stmt->close();
}

$_SESSION['login']       = true;
$_SESSION['user_id']     = $user_id;
$_SESSION['username']    = $username;
$_SESSION['nama']        = $nama_lengkap;
$_SESSION['email']       = $email;
$_SESSION['foto_profil'] = $foto_profil;
$_SESSION['is_seller']   = false;
$_SESSION['login.time']  = time();

header("Location: ../index.php");
exit;
?>
