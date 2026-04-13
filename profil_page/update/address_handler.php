<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../../fungsi/dp.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Harus login', 'need_login' => true]);
    exit;
}

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

$check = $conn->query("SHOW COLUMNS FROM `pcnexus` LIKE 'alamat_json'");
if ($check && $check->num_rows === 0) {
    $conn->query("ALTER TABLE `pcnexus` ADD COLUMN `alamat_json` LONGTEXT NULL");
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT nama_lengkap, Email, alamat_json FROM pcnexus WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($nama_lengkap, $email, $alamat_json);
    $stmt->fetch();
    $stmt->close();

    $addresses = [];
    if (!empty($alamat_json)) {
        $decoded = json_decode($alamat_json, true);
        if (is_array($decoded)) {
            $addresses = $decoded;
        }
    }

    echo json_encode([
        'success' => true,
        'profile' => [
            'nama' => $nama_lengkap ?: ($_SESSION['nama'] ?? ''),
            'email' => $email ?: ($_SESSION['email'] ?? '')
        ],
        'addresses' => $addresses
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$input = is_array($input) ? $input : [];

$addresses = $input['addresses'] ?? [];
if (!is_array($addresses)) {
    echo json_encode(['success' => false, 'message' => 'Format alamat tidak valid']);
    exit;
}

$clean = [];
foreach ($addresses as $a) {
    if (!is_array($a)) continue;
    $clean[] = [
        'label' => trim((string)($a['label'] ?? 'Alamat')),
        'nama' => trim((string)($a['nama'] ?? '')),
        'hp' => trim((string)($a['hp'] ?? '')),
        'detail' => trim((string)($a['detail'] ?? '')),
        'kota' => trim((string)($a['kota'] ?? '')),
        'kode_pos' => trim((string)($a['kode_pos'] ?? '')),
        'utama' => !empty($a['utama'])
    ];
}

if (!empty($clean) && !array_filter($clean, fn($x) => !empty($x['utama']))) {
    $clean[0]['utama'] = true;
}

$json = json_encode($clean, JSON_UNESCAPED_UNICODE);
$stmt = $conn->prepare("UPDATE pcnexus SET alamat_json = ? WHERE id = ?");
$stmt->bind_param('si', $json, $user_id);
$ok = $stmt->execute();
$stmt->close();

echo json_encode([
    'success' => $ok,
    'addresses' => $clean,
    'message' => $ok ? 'Alamat berhasil disimpan' : 'Gagal menyimpan alamat'
]);
?>
