<?php
/**
 * keranjang_handler.php
 * Backend handler untuk semua aksi keranjang belanja
 * Dipanggil via AJAX dari troli.php
 */
session_start();
header('Content-Type: application/json');

include __DIR__ . '/dp.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$action  = $_POST['action'] ?? $_GET['action'] ?? '';

// Pastikan tabel cart ada
$conn->query("CREATE TABLE IF NOT EXISTS `cart` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `produk_id` INT NOT NULL,
    `qty` INT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `user_produk` (`user_id`, `produk_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Pastikan tabel produk ada & punya kolom penjual
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

switch ($action) {

    // ── GET: ambil isi keranjang user ──────────────────────────────
    case 'get':
        if (!$user_id) {
            echo json_encode(['success' => true, 'items' => [], 'total' => 0]);
            exit;
        }
        $stmt = $conn->prepare("
            SELECT c.id as cart_id, c.qty, p.id as produk_id, p.nama_produk, p.harga,
                   p.kategori, p.merek, p.foto, p.kondisi, p.stok, p.nama_toko
            FROM cart c
            JOIN produk p ON c.produk_id = p.id
            WHERE c.user_id = ? AND p.status = 'aktif' AND p.stok > 0
        ");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res   = $stmt->get_result();
        $items = [];
        $total = 0;
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
            $total  += $row['harga'] * $row['qty'];
        }
        $stmt->close();
        echo json_encode(['success' => true, 'items' => $items, 'total' => $total]);
        break;

    // ── ADD: tambah ke keranjang ───────────────────────────────────
    case 'add':
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Harus login untuk menambah ke keranjang', 'need_login' => true]);
            exit;
        }
        $produk_id = (int)($_POST['produk_id'] ?? 0);
        $qty       = max(1, (int)($_POST['qty'] ?? 1));

        if (!$produk_id) {
            echo json_encode(['success' => false, 'message' => 'Produk tidak valid']);
            exit;
        }

        // Cek produk ada dan stoknya cukup
        $pStmt = $conn->prepare("SELECT id, stok, nama_produk FROM produk WHERE id = ? AND status = 'aktif'");
        $pStmt->bind_param('i', $produk_id);
        $pStmt->execute();
        $pRes = $pStmt->get_result()->fetch_assoc();
        $pStmt->close();

        if (!$pRes) {
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
            exit;
        }

        // Cek qty di cart saat ini
        $curStmt = $conn->prepare("SELECT qty FROM cart WHERE user_id = ? AND produk_id = ?");
        $curStmt->bind_param('ii', $user_id, $produk_id);
        $curStmt->execute();
        $curStmt->bind_result($current_qty);
        $curStmt->fetch();
        $curStmt->close();
        $current_qty = $current_qty ?? 0;

        if ($current_qty + $qty > $pRes['stok']) {
            echo json_encode(['success' => false, 'message' => 'Stok tidak cukup (tersisa ' . $pRes['stok'] . ')']);
            exit;
        }

        // Insert atau update qty
        $stmt = $conn->prepare("
            INSERT INTO cart (user_id, produk_id, qty)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty)
        ");
        $stmt->bind_param('iii', $user_id, $produk_id, $qty);
        if ($stmt->execute()) {
            // Hitung total item di keranjang
            $cntStmt = $conn->prepare("SELECT SUM(qty) FROM cart WHERE user_id = ?");
            $cntStmt->bind_param('i', $user_id);
            $cntStmt->execute();
            $cntStmt->bind_result($total_qty);
            $cntStmt->fetch();
            $cntStmt->close();
            echo json_encode(['success' => true, 'message' => $pRes['nama_produk'] . ' ditambahkan ke keranjang', 'cart_count' => (int)$total_qty]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan: ' . $stmt->error]);
        }
        $stmt->close();
        break;

    // ── UPDATE qty ─────────────────────────────────────────────────
    case 'update':
        if (!$user_id) { echo json_encode(['success' => false]); exit; }
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        $qty     = (int)($_POST['qty'] ?? 1);

        if ($qty <= 0) {
            // Hapus item jika qty 0 atau negatif
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->bind_param('ii', $cart_id, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE cart SET qty = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param('iii', $qty, $cart_id, $user_id);
        }
        $stmt->execute();
        $stmt->close();

        // Return updated total
        $totStmt = $conn->prepare("SELECT SUM(c.qty * p.harga) as total, SUM(c.qty) as count FROM cart c JOIN produk p ON c.produk_id = p.id WHERE c.user_id = ?");
        $totStmt->bind_param('i', $user_id);
        $totStmt->execute();
        $totStmt->bind_result($total, $count);
        $totStmt->fetch();
        $totStmt->close();
        echo json_encode(['success' => true, 'total' => (int)$total, 'cart_count' => (int)$count]);
        break;

    // ── REMOVE item ────────────────────────────────────────────────
    case 'remove':
        if (!$user_id) { echo json_encode(['success' => false]); exit; }
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        $stmt    = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $cart_id, $user_id);
        $stmt->execute();
        $stmt->close();

        $cntStmt = $conn->prepare("SELECT SUM(qty) FROM cart WHERE user_id = ?");
        $cntStmt->bind_param('i', $user_id);
        $cntStmt->execute();
        $cntStmt->bind_result($total_qty);
        $cntStmt->fetch();
        $cntStmt->close();
        echo json_encode(['success' => true, 'cart_count' => (int)$total_qty]);
        break;

    // ── CLEAR semua isi keranjang ──────────────────────────────────
    case 'clear':
        if (!$user_id) { echo json_encode(['success' => false]); exit; }
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['success' => true]);
        break;

    // ── COUNT: jumlah item di keranjang (untuk badge nav) ──────────
    case 'count':
        if (!$user_id) { echo json_encode(['count' => 0]); exit; }
        $stmt = $conn->prepare("SELECT COALESCE(SUM(qty),0) FROM cart WHERE user_id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($cnt);
        $stmt->fetch();
        $stmt->close();
        echo json_encode(['count' => (int)$cnt]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal']);
}
?>
