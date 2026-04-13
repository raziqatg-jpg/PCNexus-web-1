<?php
session_start();
include __DIR__ . '/dp.php';

if (!isset($conn) || !$conn) {
    die("Koneksi database tidak tersedia");
}

if (!isset($_POST['email']) || !isset($_POST['password'])) {
    header("Location: ../login.php?popup=invalid_input");
    exit;
}

$email    = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];
$table    = 'pcnexus';

$result = $conn->query("SHOW TABLES LIKE '$table'");
if ($result === false || $result->num_rows === 0) {
    die('Tabel login tidak ditemukan.');
}

$query = "SELECT * FROM `$table` WHERE Email = ?";
$stmt  = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);

if (!$user) {
    header("Location: ../login.php?popup=belum_daftar");
    exit;
}

if (!password_verify($password, $user['password'])) {
    header("Location: ../login.php?popup=password_salah");
    exit;
}

// Set session — termasuk is_seller agar halaman penjual bisa akses
$_SESSION['login']       = true;
$_SESSION['user_id']     = $user['id'];
$_SESSION['username']    = $user['username'];
$_SESSION['nama']        = $user['nama_lengkap'] ?? $user['username'];
$_SESSION['email']       = $user['Email'] ?? $user['email'] ?? $email;
$_SESSION['foto_profil'] = $user['foto_profil'] ?? 'default.png';
$_SESSION['is_seller']   = !empty($user['is_seller']) && $user['is_seller'] == 1;
$_SESSION['nama_toko']   = $user['nama_toko'] ?? '';
$_SESSION['login.time']  = time();

header("Location: ../index.php");
exit;
?>
