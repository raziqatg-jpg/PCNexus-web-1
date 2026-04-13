<?php
/**
 * penjual_handler.php
 * Backend AJAX untuk dashboard penjual
 */
session_start();
header('Content-Type: application/json');
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Include file koneksi database
include __DIR__ . '/dp.php';

function json_out($payload, $code = 200) {
    http_response_code($code);
    if (ob_get_length()) {
        ob_clean();
    }
    echo json_encode($payload);
    exit;
}

// Cek koneksi database
if (!isset($conn) || !$conn) {
    json_out(['success' => false, 'message' => 'Koneksi database gagal'], 500);
}

// Harus login
if (!isset($_SESSION['user_id'])) {
    json_out(['success' => false, 'message' => 'Harus login dulu', 'need_login' => true], 401);
}

// Cek apakah user adalah penjual dari database
$check_sql = "SELECT is_seller FROM pcnexus WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$is_seller_db = 0;
if ($check_stmt) {
    $check_stmt->bind_param('i', $_SESSION['user_id']);
    $check_stmt->execute();
    $check_stmt->bind_result($is_seller_db);
    $check_stmt->fetch();
    $check_stmt->close();
}

if (!$is_seller_db) {
    // Auto-upgrade menjadi seller agar flow upload produk tidak terhambat.
    $upgrade = $conn->prepare("UPDATE pcnexus SET is_seller = 1 WHERE id = ?");
    if ($upgrade) {
        $upgrade->bind_param('i', $_SESSION['user_id']);
        $upgrade->execute();
        $upgrade->close();
        $is_seller_db = 1;
    }
}

// Set session is_seller jika belum ada
if (!isset($_SESSION['is_seller']) || $_SESSION['is_seller'] !== true) {
    $_SESSION['is_seller'] = true;
}

$seller_id = (int)$_SESSION['user_id'];
$action  = $_POST['action'] ?? $_GET['action'] ?? '';

// Pastikan tabel produk ada (pakai user_id agar konsisten lintas modul)
$conn->query("CREATE TABLE IF NOT EXISTS `produk` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `nama_toko` VARCHAR(100),
    `kategori` VARCHAR(50),
    `merek` VARCHAR(100),
    `nama_produk` VARCHAR(255) NOT NULL,
    `harga` BIGINT NOT NULL,
    `stok` INT DEFAULT 0,
    `kondisi` ENUM('Baru','Bekas','Refurbished') DEFAULT 'Baru',
    `berat` DECIMAL(10,2) DEFAULT 1,
    `deskripsi` TEXT,
    `foto` VARCHAR(255),
    `rating` DECIMAL(3,1) DEFAULT 5.0,
    `sold` INT DEFAULT 0,
    `status` ENUM('aktif','nonaktif','habis') DEFAULT 'aktif',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Kompatibilitas schema lama: jika masih pakai seller_id, migrasikan ke user_id.
$checkUserId = $conn->query("SHOW COLUMNS FROM `produk` LIKE 'user_id'");
if ($checkUserId && $checkUserId->num_rows === 0) {
    $conn->query("ALTER TABLE `produk` ADD COLUMN `user_id` INT NULL AFTER `id`");
    $hasSellerId = $conn->query("SHOW COLUMNS FROM `produk` LIKE 'seller_id'");
    if ($hasSellerId && $hasSellerId->num_rows > 0) {
        $conn->query("UPDATE `produk` SET `user_id` = `seller_id` WHERE `user_id` IS NULL");
    }
}

// Pastikan tabel pesanan ada
$conn->query("CREATE TABLE IF NOT EXISTS `pesanan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` VARCHAR(50) UNIQUE NOT NULL,
    `user_id` INT NOT NULL,
    `alamat_id` INT NOT NULL,
    `total_harga` BIGINT NOT NULL,
    `status` ENUM('pending','diproses','dikirim','selesai','batal') DEFAULT 'pending',
    `payment_method` VARCHAR(50),
    `payment_status` ENUM('pending','paid','failed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS `detail_pesanan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pesanan_id` INT NOT NULL,
    `produk_id` INT,
    `nama_produk` VARCHAR(255) NOT NULL,
    `harga` BIGINT NOT NULL,
    `qty` INT NOT NULL,
    `subtotal` BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$conn->query("CREATE TABLE IF NOT EXISTS `ulasan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `produk_id` INT,
    `video_id` INT,
    `rating` TINYINT CHECK (rating >= 1 AND rating <= 5),
    `komentar` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

switch ($action) {

    // ── GET produk milik penjual ────────────────────────────────────
    case 'get_produk':
        $stmt = $conn->prepare("SELECT * FROM produk WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param('i', $seller_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $items = [];
        while ($row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt->close();
        
        echo json_encode(['success' => true, 'produk' => $items]);
        break;

    // ── UPLOAD produk baru ──────────────────────────────────────────
    case 'upload_produk':
        $kategori    = trim($_POST['kategori'] ?? '');
        $merek       = trim($_POST['merek'] ?? '');
        $nama_produk = trim($_POST['nama_produk'] ?? '');
        $harga       = (int)($_POST['harga'] ?? 0);
        $stok        = (int)($_POST['stok'] ?? 0);
        $kondisi     = $_POST['kondisi'] ?? 'Baru';
        $berat       = (float)($_POST['berat'] ?? 1);
        $deskripsi   = trim($_POST['deskripsi'] ?? '');
        
        // Ambil nama toko dari database
        $nama_toko = $_SESSION['nama_toko'] ?? '';
        if (empty($nama_toko)) {
            $stmt = $conn->prepare("SELECT nama_toko FROM pcnexus WHERE id = ?");
            $stmt->bind_param('i', $seller_id);
            $stmt->execute();
            $stmt->bind_result($nama_toko);
            $stmt->fetch();
            $stmt->close();
            $_SESSION['nama_toko'] = $nama_toko;
        }
        if (empty($nama_toko)) {
            $nama_toko = 'Toko Seller #' . $seller_id;
            $_SESSION['nama_toko'] = $nama_toko;
            $u = $conn->prepare("UPDATE pcnexus SET nama_toko = ? WHERE id = ?");
            if ($u) {
                $u->bind_param('si', $nama_toko, $seller_id);
                $u->execute();
                $u->close();
            }
        }
        
        if (!$kategori || !$merek || !$nama_produk || !$harga) {
            echo json_encode(['success' => false, 'message' => 'Field wajib belum diisi (kategori, merek, nama, harga)']); 
            exit;
        }
        
        // Icon default berdasarkan kategori
        $icons = [
            'laptop' => '💻', 'pc' => '🖥️', 'sparepart' => '🔧', 
            'vga' => '🎮', 'monitor' => '🖥', 'aksesoris' => '🖱️', 
            'storage' => '💾', 'networking' => '📡'
        ];
        $foto = $icons[$kategori] ?? '📦';
        
        $stmt = $conn->prepare("INSERT INTO produk (user_id, nama_toko, kategori, merek, nama_produk, harga, stok, kondisi, berat, deskripsi, foto, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,'aktif')");
        if (!$stmt) {
            json_out(['success' => false, 'message' => 'Prepare query gagal: ' . $conn->error], 500);
        }
        $stmt->bind_param('issssiisdss', $seller_id, $nama_toko, $kategori, $merek, $nama_produk, $harga, $stok, $kondisi, $berat, $deskripsi, $foto);
        
        if ($stmt->execute()) {
            json_out(['success' => true, 'message' => 'Produk berhasil diupload!', 'produk_id' => $stmt->insert_id]);
        } else {
            json_out(['success' => false, 'message' => 'Gagal upload: ' . $stmt->error], 500);
        }
        break;

    // ── HAPUS / nonaktifkan produk ─────────────────────────────────
    case 'hapus_produk':
        $produk_id = (int)($_POST['produk_id'] ?? 0);
        $stmt = $conn->prepare("UPDATE produk SET status='nonaktif' WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $produk_id, $seller_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
        $stmt->close();
        break;

    // ── GET statistik dashboard ─────────────────────────────────────
    case 'get_stats':
        // Total produk
        $s = $conn->prepare("SELECT COUNT(*) FROM produk WHERE user_id=?");
        $s->bind_param('i', $seller_id); 
        $s->execute(); 
        $s->bind_result($total_produk); 
        $s->fetch(); 
        $s->close();

        $ss = $conn->prepare("SELECT COALESCE(SUM(stok),0) FROM produk WHERE user_id=? AND status='aktif'");
        $ss->bind_param('i', $seller_id);
        $ss->execute();
        $ss->bind_result($total_stock);
        $ss->fetch();
        $ss->close();

        // Total sold & pendapatan
        $s2 = $conn->prepare("
            SELECT COALESCE(SUM(dp.qty),0) as total_sold,
                   COALESCE(SUM(dp.subtotal),0) as total_revenue
            FROM detail_pesanan dp
            JOIN produk pr ON dp.produk_id = pr.id
            JOIN pesanan pe ON dp.pesanan_id = pe.id
            WHERE pr.user_id = ? AND pe.status != 'batal'
        ");
        $s2->bind_param('i', $seller_id); 
        $s2->execute(); 
        $s2->bind_result($total_sold, $total_revenue); 
        $s2->fetch(); 
        $s2->close();

        // Rata-rata rating
        $s3 = $conn->prepare("SELECT COALESCE(AVG(rating),5.0) FROM produk WHERE user_id=? AND rating > 0");
        $s3->bind_param('i', $seller_id); 
        $s3->execute(); 
        $s3->bind_result($avg_rating); 
        $s3->fetch(); 
        $s3->close();

        echo json_encode([
            'success' => true,
            'total_produk'  => (int)$total_produk,
            'total_stock'   => (int)$total_stock,
            'total_sold'    => (int)$total_sold,
            'total_revenue' => (int)$total_revenue,
            'avg_rating'    => round((float)$avg_rating, 1),
        ]);
        break;

    // ── GET data grafik penjualan 6 bulan terakhir ─────────────────
    case 'get_chart':
        $rows_penjualan = [];
        $rows_pendapatan = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $bulan  = date('Y-m', strtotime("-$i months"));
            $labels[] = date('M', strtotime("-$i months"));

            $stmt = $conn->prepare("
                SELECT COALESCE(SUM(dp.qty),0) as total_unit,
                       COALESCE(SUM(dp.subtotal),0) as total_rp
                FROM detail_pesanan dp
                JOIN produk pr ON dp.produk_id = pr.id
                JOIN pesanan pe ON dp.pesanan_id = pe.id
                WHERE pr.user_id = ?
                  AND DATE_FORMAT(pe.created_at, '%Y-%m') = ?
                  AND pe.status != 'batal'
            ");
            $stmt->bind_param('is', $seller_id, $bulan);
            $stmt->execute();
            $stmt->bind_result($unit, $rp);
            $stmt->fetch();
            $stmt->close();

            $rows_penjualan[]  = (int)$unit;
            $rows_pendapatan[] = round((float)$rp / 1000000, 1);
        }

        echo json_encode([
            'success'     => true,
            'labels'      => $labels,
            'penjualan'   => $rows_penjualan,
            'pendapatan'  => $rows_pendapatan,
        ]);
        break;

    // ── GET ulasan untuk produk penjual ────────────────────────────
    case 'get_ulasan':
        $stmt = $conn->prepare("
            SELECT u.rating, u.komentar, u.created_at,
                   p.nama_produk,
                   u.user_id as pembeli_id
            FROM ulasan u
            JOIN produk p ON u.produk_id = p.id
            WHERE p.user_id = ?
            ORDER BY u.created_at DESC
            LIMIT 20
        ");
        $stmt->bind_param('i', $seller_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $items = [];
        while ($row = $res->fetch_assoc()) {
            // Ambil nama pembeli dari users
            $user_stmt = $conn->prepare("SELECT nama_lengkap FROM pcnexus WHERE id = ?");
            $user_stmt->bind_param('i', $row['pembeli_id']);
            $user_stmt->execute();
            $user_stmt->bind_result($nama_pembeli);
            $user_stmt->fetch();
            $user_stmt->close();
            
            $row['nama_pembeli'] = $nama_pembeli ?? 'Pelanggan #' . $row['pembeli_id'];
            $items[] = $row;
        }
        $stmt->close();
        echo json_encode(['success' => true, 'ulasan' => $items]);
        break;

    // ── GET pembeli terbaru untuk seller ────────────────────────────
    case 'get_pembeli':
        $stmt = $conn->prepare("
            SELECT 
                COALESCE(u.nama_lengkap, CONCAT('User #', pe.user_id)) as nama_pembeli,
                dp.nama_produk,
                dp.qty,
                dp.subtotal,
                pe.created_at
            FROM detail_pesanan dp
            JOIN pesanan pe ON pe.id = dp.pesanan_id
            JOIN produk pr ON pr.id = dp.produk_id
            LEFT JOIN pcnexus u ON u.id = pe.user_id
            WHERE pr.user_id = ?
            ORDER BY pe.created_at DESC
            LIMIT 15
        ");
        $stmt->bind_param('i', $seller_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        echo json_encode(['success' => true, 'pembeli' => $rows]);
        break;

    default:
        json_out(['success' => false, 'message' => 'Aksi tidak dikenal: ' . $action], 400);
}
?>