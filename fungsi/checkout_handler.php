<?php
/**
 * checkout_handler.php
 * Proses order dari checkout ke DB
 */
session_start();
header('Content-Type: application/json');

include __DIR__ . '/dp.php';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'DB error']); 
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Harus login dulu', 'need_login' => true]); 
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// ========== CHECKOUT DARI LOCALSTORAGE (VIA AJAX) ==========
if ($action === 'checkout') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $alamat       = trim($_POST['alamat'] ?? '');
    $kota         = trim($_POST['kota'] ?? '');
    $kode_pos     = trim($_POST['kode_pos'] ?? '');
    $metode_bayar = trim($_POST['metode_bayar'] ?? 'transfer_bank');
    $items_json   = $_POST['items'] ?? '';
    
    // Validasi
    if (!$nama_lengkap || !$no_hp || !$alamat) {
        echo json_encode(['success' => false, 'message' => 'Data pengiriman tidak lengkap']); 
        exit;
    }
    
    $items = json_decode($items_json, true);
    if (empty($items) || !is_array($items)) {
        echo json_encode(['success' => false, 'message' => 'Keranjang kosong']); 
        exit;
    }

    // Jangan percaya harga dari client.
    // Ambil data produk terbaru dari DB agar total harga selalu akurat.
    $validated_items = [];
    $subtotal = 0;
    $pStmt = $conn->prepare("SELECT id, nama_produk, harga, stok FROM produk WHERE id = ? AND status = 'aktif'");

    foreach ($items as $item) {
        $produk_id = (int)($item['id'] ?? $item['produk_id'] ?? 0);
        $qty       = max(1, (int)($item['qty'] ?? 1));
        if ($produk_id <= 0) {
            continue;
        }

        $pStmt->bind_param('i', $produk_id);
        $pStmt->execute();
        $pRes = $pStmt->get_result()->fetch_assoc();

        if (!$pRes) {
            continue;
        }

        if ((int)$pRes['stok'] < $qty) {
            $pStmt->close();
            echo json_encode([
                'success' => false,
                'message' => 'Stok tidak cukup untuk produk: ' . $pRes['nama_produk']
            ]);
            exit;
        }

        $harga_asli = (int)$pRes['harga'];
        $validated_items[] = [
            'id'    => (int)$pRes['id'],
            'name'  => $pRes['nama_produk'],
            'qty'   => $qty,
            'price' => $harga_asli
        ];
        $subtotal += ($harga_asli * $qty);
    }
    $pStmt->close();

    if (empty($validated_items)) {
        echo json_encode(['success' => false, 'message' => 'Produk checkout tidak valid']); 
        exit;
    }
    
    // Hitung ongkir berdasarkan kota
    $kota_lower = strtolower($kota);
    if (strpos($kota_lower, 'jakarta') !== false || 
        strpos($kota_lower, 'bekasi') !== false || 
        strpos($kota_lower, 'depok') !== false || 
        strpos($kota_lower, 'tangerang') !== false || 
        strpos($kota_lower, 'bogor') !== false) {
        $ongkir = 20000;
    } elseif (strpos($kota_lower, 'bandung') !== false || 
              strpos($kota_lower, 'surabaya') !== false || 
              strpos($kota_lower, 'semarang') !== false ||
              strpos($kota_lower, 'yogyakarta') !== false) {
        $ongkir = 30000;
    } else {
        $ongkir = 50000;
    }
    
    $pajak       = (int)round($subtotal * 0.11);
    $total_harga = $subtotal + $ongkir + $pajak;
    $order_code  = 'PCN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    
    // Mulai transaksi database
    $conn->begin_transaction();
    
    try {
        // 1. Insert ke tabel pesanan
        $stmt = $conn->prepare("INSERT INTO pesanan (user_id, order_code, nama_lengkap, no_hp, email, alamat, kota, kode_pos, metode_bayar, total_harga, ongkir, pajak, status, created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,'pending', NOW())");
        $stmt->bind_param('isssssssssii', $user_id, $order_code, $nama_lengkap, $no_hp, $email, $alamat, $kota, $kode_pos, $metode_bayar, $total_harga, $ongkir, $pajak);
        $stmt->execute();
        $pesanan_id = $stmt->insert_id;
        $stmt->close();
        
        // 2. Insert ke tabel detail_pesanan
        foreach ($validated_items as $item) {
            $subtotal_item = $item['price'] * $item['qty'];
            $dStmt = $conn->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, qty, harga_satuan, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
            $dStmt->bind_param('iissii', $pesanan_id, $item['id'], $item['name'], $item['qty'], $item['price'], $subtotal_item);
            $dStmt->execute();
            $dStmt->close();

            // Kurangi stok dan naikkan angka terjual agar dashboard seller selalu update.
            $uStmt = $conn->prepare("UPDATE produk SET stok = GREATEST(stok - ?, 0), sold = sold + ? WHERE id = ?");
            $uStmt->bind_param('iii', $item['qty'], $item['qty'], $item['id']);
            $uStmt->execute();
            $uStmt->close();
        }
        
        // 3. Commit transaksi
        $conn->commit();
        
        echo json_encode([
            'success'    => true,
            'order_code' => $order_code,
            'total'      => $total_harga,
            'message'    => 'Pesanan berhasil dibuat!'
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false, 
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ========== CHECKOUT DARI DATABASE CART (opsional) ==========
// Ambil isi keranjang dari tabel cart
$stmt = $conn->prepare("
    SELECT c.id as cart_id, c.qty, p.id as produk_id, p.harga, p.nama_produk, p.icon
    FROM cart c
    JOIN produk p ON c.produk_id = p.id
    WHERE c.user_id = ? AND p.status = 'aktif'
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$items = [];
while ($row = $res->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Keranjang kosong (database)']); 
    exit;
}

// Hitung total dari database cart
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['harga'] * $item['qty'];
}

// Hitung ongkir (default Jakarta)
$ongkir = 20000;
$pajak = (int)round($subtotal * 0.11);
$total_harga = $subtotal + $ongkir + $pajak;
$order_code = 'PCN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

// Mulai transaksi
$conn->begin_transaction();

try {
    // Insert pesanan
    $stmt = $conn->prepare("INSERT INTO pesanan (user_id, order_code, total_harga, ongkir, pajak, status, created_at) VALUES (?,?,?,?,?,'pending', NOW())");
    $stmt->bind_param('isiii', $user_id, $order_code, $total_harga, $ongkir, $pajak);
    $stmt->execute();
    $pesanan_id = $stmt->insert_id;
    $stmt->close();
    
    // Insert detail pesanan
    foreach ($items as $item) {
        $subtotal_item = $item['harga'] * $item['qty'];
        $dStmt = $conn->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, qty, harga_satuan, subtotal) VALUES (?,?,?,?,?,?)");
        $dStmt->bind_param('iissii', $pesanan_id, $item['produk_id'], $item['nama_produk'], $item['qty'], $item['harga'], $subtotal_item);
        $dStmt->execute();
        $dStmt->close();

        $uStmt = $conn->prepare("UPDATE produk SET stok = GREATEST(stok - ?, 0), sold = sold + ? WHERE id = ?");
        $uStmt->bind_param('iii', $item['qty'], $item['qty'], $item['produk_id']);
        $uStmt->execute();
        $uStmt->close();
    }
    
    // Kosongkan keranjang
    $clearStmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clearStmt->bind_param('i', $user_id);
    $clearStmt->execute();
    $clearStmt->close();
    
    $conn->commit();
    
    echo json_encode([
        'success'    => true,
        'order_code' => $order_code,
        'total'      => $total_harga,
        'message'    => 'Pesanan berhasil dibuat dari database cart!'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>