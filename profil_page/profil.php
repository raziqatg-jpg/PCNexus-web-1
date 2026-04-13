<?php
session_start();
require_once __DIR__ . '/../fungsi/dp.php';

// Ambil foto_profil segar dari DB
$foto_profil_fresh = $_SESSION['foto_profil'] ?? 'default.png';
$alamat_from_db = [];
if (isset($conn) && isset($_SESSION['user_id'])) {
  $fp_stmt = $conn->prepare("SELECT foto_profil, nama, email, bio, is_seller, seller_store_name FROM users WHERE id = ?");
  if ($fp_stmt) {
    $fp_stmt->bind_param('i', $_SESSION['user_id']);
    $fp_stmt->execute();
    $fp_stmt->bind_result($fp_db, $nama_db, $email_db, $bio_db, $is_seller_db, $store_name_db);
    $fp_stmt->fetch();
    $fp_stmt->close();
    if (!empty($fp_db)) {
      $foto_profil_fresh = $fp_db;
      $_SESSION['foto_profil'] = $fp_db;
    }
    if (!empty($nama_db)) $_SESSION['nama'] = $nama_db;
    if (!empty($email_db)) $_SESSION['email'] = $email_db;
    if (!empty($bio_db)) $_SESSION['bio'] = $bio_db;
    $_SESSION['is_seller'] = $is_seller_db == 1;
    if (!empty($store_name_db)) $_SESSION['seller_store_name'] = $store_name_db;
  }

  // Ambil alamat tersimpan dari tabel utama akun (pcnexus) jika tersedia.
  $col_check = $conn->query("SHOW COLUMNS FROM `pcnexus` LIKE 'alamat_json'");
  if ($col_check && $col_check->num_rows > 0) {
    $addr_stmt = $conn->prepare("SELECT alamat_json FROM pcnexus WHERE id = ?");
    if ($addr_stmt) {
      $uid = (int)$_SESSION['user_id'];
      $addr_stmt->bind_param('i', $uid);
      $addr_stmt->execute();
      $addr_stmt->bind_result($alamat_json);
      $addr_stmt->fetch();
      $addr_stmt->close();
      if (!empty($alamat_json)) {
        $decoded = json_decode($alamat_json, true);
        if (is_array($decoded)) {
          $alamat_from_db = $decoded;
        }
      }
    }
  }
}

// Data user
$user = [
  'nama'         => $_SESSION['nama']        ?? 'Rizky Pratama',
  'email'        => $_SESSION['email']       ?? 'rizky@email.com',
  'bio'          => $_SESSION['bio']         ?? 'Tech enthusiast | Suka laptop gaming & produktivitas',
  'foto_profil'  => $foto_profil_fresh,
  'banner'       => $_SESSION['banner']      ?? '',
  'joined'       => $_SESSION['joined']      ?? 'Maret 2024',
  'is_seller'    => $_SESSION['is_seller']   ?? false,
  'store_name'   => $_SESSION['seller_store_name'] ?? '',
  'alamat'       => $alamat_from_db,
  'pesanan' => [
    ['id' => 'PCN-20240315-001', 'tgl' => '15 Mar 2024', 'produk' => 'ASUS VivoBook 15 OLED', 'harga' => 'Rp 8.499.000', 'status' => 'selesai'],
    ['id' => 'PCN-20240401-002', 'tgl' => '1 Apr 2024', 'produk' => 'RAM Corsair Vengeance 16GB', 'harga' => 'Rp 650.000', 'status' => 'dikirim'],
    ['id' => 'PCN-20240412-003', 'tgl' => '12 Apr 2024', 'produk' => 'Lenovo IdeaPad Gaming 3', 'harga' => 'Rp 11.200.000', 'status' => 'diproses'],
  ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya - PCNexus</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f7f9fc;
      color: #1e293b;
    }

    /* ========== NAVBAR ========== */
    .navbar {
      background: #fff;
      padding: 12px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .logo {
      font-size: 22px;
      font-weight: 800;
      color: #7c3aed;
      text-decoration: none;
      letter-spacing: -0.5px;
    }

    .search-box {
      display: flex;
      align-items: center;
      background: #f1f5f9;
      border-radius: 40px;
      padding: 0 16px;
      width: 100%;
      max-width: 360px;
    }

    .search-box input {
      flex: 1;
      border: none;
      background: transparent;
      padding: 10px 0;
      font-size: 14px;
      outline: none;
    }

    .search-box button {
      background: none;
      border: none;
      cursor: pointer;
      color: #94a3b8;
      font-size: 16px;
    }

    .nav-right {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .cart-btn {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 20px;
      color: #475569;
      position: relative;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .cart-badge {
      background: #7c3aed;
      color: #fff;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .profile-nav img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #e2e8f0;
    }

    /* ========== BREADCRUMB ========== */
    .breadcrumb {
      padding: 16px 40px;
      background: #fff;
      border-bottom: 1px solid #e2e8f0;
      font-size: 13px;
    }

    .breadcrumb a {
      color: #7c3aed;
      text-decoration: none;
    }

    .breadcrumb span {
      color: #94a3b8;
    }

    /* ========== MAIN CONTENT ========== */
    .profile-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 24px 20px;
    }

    /* ========== PROFILE CARD ========== */
    .profile-card {
      background: #fff;
      border-radius: 24px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.05);
      margin-bottom: 24px;
    }

    /* Banner */
    .banner-area {
      height: 140px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      position: relative;
    }

    .banner-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .btn-edit-banner {
      position: absolute;
      bottom: 12px;
      right: 16px;
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(4px);
      color: #fff;
      border: none;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 11px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    /* Profile Info - DIPERBAIKI */
    .profile-info {
      padding: 20px 28px 24px;
      position: relative;
    }

    .avatar-section {
      display: flex;
      align-items: center;
      gap: 24px;
      margin-top: 0;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .avatar-wrapper {
      position: relative;
      flex-shrink: 0;
    }

    .avatar-img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .btn-edit-avatar {
      position: absolute;
      bottom: 4px;
      right: 4px;
      background: #7c3aed;
      border: none;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      color: #fff;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
    }

    .user-details {
      flex: 1;
      min-width: 200px;
    }

    .user-name {
      font-size: 24px;
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .seller-badge {
      background: linear-gradient(135deg, #7c3aed, #a855f7);
      color: #fff;
      font-size: 11px;
      font-weight: 600;
      padding: 4px 12px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .store-name {
      background: #f1f5f9;
      color: #475569;
      font-size: 12px;
      font-weight: 500;
      padding: 4px 12px;
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .user-bio {
      color: #64748b;
      font-size: 14px;
      margin-bottom: 8px;
      line-height: 1.5;
    }

    .user-meta {
      color: #94a3b8;
      font-size: 12px;
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-top: 4px;
    }

    .user-meta i {
      width: 16px;
      margin-right: 4px;
    }

    .action-buttons {
      display: flex;
      gap: 12px;
      margin-top: 16px;
      flex-wrap: wrap;
    }

    .btn-primary {
      background: #7c3aed;
      color: #fff;
      border: none;
      padding: 10px 24px;
      border-radius: 40px;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary:hover {
      background: #6d28d9;
      transform: translateY(-1px);
    }

    .btn-outline {
      background: transparent;
      border: 1.5px solid #e2e8f0;
      padding: 10px 24px;
      border-radius: 40px;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #475569;
    }

    .btn-outline:hover {
      border-color: #7c3aed;
      color: #7c3aed;
      background: #faf5ff;
    }

    .btn-gradient {
      background: linear-gradient(135deg, #f59e0b, #f97316);
      color: #fff;
      border: none;
      padding: 10px 24px;
      border-radius: 40px;
      font-weight: 600;
      font-size: 13px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    /* Stats Bar */
    .stats-bar {
      display: flex;
      justify-content: space-around;
      padding: 20px 28px;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
    }

    .stat-item {
      text-align: center;
    }

    .stat-number {
      font-size: 24px;
      font-weight: 800;
      color: #0f172a;
    }

    .stat-label {
      font-size: 12px;
      color: #64748b;
      margin-top: 4px;
    }

    /* ========== SECTION CARDS ========== */
    .section-card {
      background: #fff;
      border-radius: 20px;
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 12px;
    }

    .section-title {
      font-size: 18px;
      font-weight: 700;
      color: #0f172a;
    }

    .section-link {
      color: #7c3aed;
      font-size: 13px;
      font-weight: 600;
      text-decoration: none;
    }

    /* ========== ORDER LIST ========== */
    .order-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .order-item {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px;
      background: #f8fafc;
      border-radius: 16px;
      transition: all 0.2s;
    }

    .order-item:hover {
      background: #f1f5f9;
    }

    .order-icon {
      width: 48px;
      height: 48px;
      background: #fff;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
    }

    .order-icon.selesai { background: #dcfce7; color: #15803d; }
    .order-icon.dikirim { background: #dbeafe; color: #1d4ed8; }
    .order-icon.diproses { background: #fef3c7; color: #b45309; }

    .order-details {
      flex: 1;
    }

    .order-product {
      font-weight: 700;
      font-size: 14px;
      margin-bottom: 4px;
    }

    .order-meta {
      font-size: 11px;
      color: #94a3b8;
    }

    .order-right {
      text-align: right;
    }

    .order-price {
      font-weight: 700;
      color: #0f172a;
      margin-bottom: 4px;
    }

    .order-status {
      font-size: 11px;
      font-weight: 600;
      padding: 4px 12px;
      border-radius: 20px;
      display: inline-block;
    }

    .status-selesai { background: #dcfce7; color: #15803d; }
    .status-dikirim { background: #dbeafe; color: #1d4ed8; }
    .status-diproses { background: #fef3c7; color: #b45309; }

    /* ========== ADDRESS GRID ========== */
    .address-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 16px;
    }

    .address-card {
      border: 1px solid #e2e8f0;
      border-radius: 16px;
      padding: 18px;
      position: relative;
      transition: all 0.2s;
    }

    .address-card:hover {
      border-color: #c4b5fd;
      box-shadow: 0 4px 12px rgba(124,58,237,0.08);
    }

    .address-card.primary {
      border-color: #7c3aed;
      background: #faf5ff;
    }

    .primary-badge {
      position: absolute;
      top: -10px;
      right: 16px;
      background: #7c3aed;
      color: #fff;
      font-size: 10px;
      font-weight: 600;
      padding: 2px 12px;
      border-radius: 20px;
    }

    .address-label {
      display: inline-block;
      background: #f1f5f9;
      padding: 2px 10px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      margin-bottom: 12px;
    }

    .address-card.primary .address-label {
      background: #ddd6fe;
      color: #7c3aed;
    }

    .address-name {
      font-weight: 700;
      margin-bottom: 4px;
    }

    .address-phone, .address-detail, .address-city {
      font-size: 12px;
      color: #64748b;
      margin-bottom: 4px;
    }

    .address-actions {
      display: flex;
      gap: 8px;
      margin-top: 12px;
    }

    .address-actions button {
      background: none;
      border: 1px solid #e2e8f0;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 11px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .address-actions button:hover {
      border-color: #7c3aed;
      color: #7c3aed;
    }

    /* ========== MODAL ========== */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }

    .modal.open {
      display: flex;
    }

    .modal-content {
      background: #fff;
      border-radius: 24px;
      max-width: 500px;
      width: 90%;
      max-height: 85vh;
      overflow-y: auto;
    }

    .modal-header {
      padding: 20px 24px;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-header h3 {
      font-size: 18px;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #94a3b8;
    }

    .modal-body {
      padding: 24px;
    }

    .modal-footer {
      padding: 16px 24px;
      border-top: 1px solid #e2e8f0;
      display: flex;
      justify-content: flex-end;
      gap: 12px;
    }

    /* Form */
    .form-group {
      margin-bottom: 16px;
    }

    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 6px;
      color: #334155;
    }

    .form-group input, .form-group select, .form-group textarea {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      font-family: inherit;
      font-size: 14px;
      transition: all 0.2s;
    }

    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
      outline: none;
      border-color: #7c3aed;
      box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    /* Toast */
    .toast {
      position: fixed;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%);
      background: #1e293b;
      color: #fff;
      padding: 12px 24px;
      border-radius: 40px;
      font-size: 13px;
      z-index: 1100;
      display: none;
    }

    .toast.show {
      display: block;
      animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateX(-50%) translateY(10px); }
      to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .navbar { padding: 12px 20px; flex-wrap: wrap; gap: 12px; }
      .search-box { order: 3; width: 100%; max-width: 100%; }
      .breadcrumb { padding: 12px 20px; }
      .profile-container { padding: 16px; }
      .profile-info { padding: 0 20px 20px; }
      .avatar-section { flex-direction: column; align-items: center; text-align: center; }
      .user-details { text-align: center; }
      .user-name { justify-content: center; }
      .user-meta { justify-content: center; }
      .action-buttons { justify-content: center; }
      .stats-bar { flex-wrap: wrap; gap: 16px; }
      .order-item { flex-wrap: wrap; }
      .order-right { width: 100%; display: flex; justify-content: space-between; align-items: center; }
      .form-row { grid-template-columns: 1fr; }
    }

    @media (max-width: 480px) {
      .address-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a href="../index.php" class="logo">PCNexus</a>
  <div class="search-box">
    <input type="text" placeholder="Cari produk...">
    <button><i class="fas fa-search"></i></button>
  </div>
  <div class="nav-right">
    <button class="cart-btn" onclick="showCart()">
      <i class="fas fa-shopping-cart"></i>
      <span class="cart-badge" id="cart-count">0</span>
    </button>
    <a href="profil.php" class="profile-nav">
      <img src="../uploads/profile/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Profil" onerror="this.src='../uploads/profile/default.png'">
    </a>
  </div>
</nav>

<!-- BREADCRUMB -->
<div class="breadcrumb">
  <a href="../index.php">Home</a> <span>/</span> <span>Profil Saya</span>
</div>

<!-- MAIN CONTENT -->
<div class="profile-container">

  <!-- PROFILE CARD -->
  <div class="profile-card">
    <div class="banner-area">
      <?php if ($user['banner']): ?>
        <img src="../uploads/banner/<?= htmlspecialchars($user['banner']) ?>" class="banner-img">
      <?php else: ?>
        <div class="banner-area" style="background: linear-gradient(135deg, #667eea, #764ba2);"></div>
      <?php endif; ?>
      <button class="btn-edit-banner" onclick="openEditBanner()">
        <i class="fas fa-camera"></i> Ganti Banner
      </button>
    </div>

    <div class="profile-info">
      <div class="avatar-section">
        <div class="avatar-wrapper">
          <img src="../uploads/profile/<?= htmlspecialchars($user['foto_profil']) ?>" class="avatar-img" id="avatarPreview" onerror="this.src='../uploads/profile/default.png'">
          <button class="btn-edit-avatar" onclick="openEditAvatar()">
            <i class="fas fa-pencil-alt"></i>
          </button>
        </div>
        <div class="user-details">
          <div class="user-name">
            <?= htmlspecialchars($user['nama']) ?>
            <?php if ($user['is_seller']): ?>
              <span class="seller-badge"><i class="fas fa-store"></i> Penjual</span>
            <?php endif; ?>
          </div>
          <?php if ($user['is_seller'] && !empty($user['store_name'])): ?>
            <div class="store-name">
              <i class="fas fa-tag"></i> <?= htmlspecialchars($user['store_name']) ?>
            </div>
          <?php endif; ?>
          <div class="user-bio"><?= htmlspecialchars($user['bio']) ?></div>
          <div class="user-meta">
            <span><i class="far fa-calendar-alt"></i> Bergabung <?= $user['joined'] ?></span>
            <span><i class="far fa-envelope"></i> <?= htmlspecialchars($user['email']) ?></span>
          </div>
          <div class="action-buttons">
            <?php if ($user['is_seller']): ?>
              <a href="../penjual/penjual.php" class="btn-primary"><i class="fas fa-chart-line"></i> Dashboard Penjual</a>
            <?php else: ?>
              <button class="btn-gradient" onclick="openSellerModal()"><i class="fas fa-store"></i> Daftar Jadi Penjual</button>
            <?php endif; ?>
            <button class="btn-outline" onclick="openEditProfil()"><i class="fas fa-user-edit"></i> Edit Profil</button>
          </div>
        </div>
      </div>
    </div>

    <div class="stats-bar">
      <div class="stat-item">
        <div class="stat-number"><?= count($user['pesanan']) ?></div>
        <div class="stat-label">Pesanan</div>
      </div>
      <div class="stat-item">
        <div class="stat-number"><?= count($user['alamat']) ?></div>
        <div class="stat-label">Alamat</div>
      </div>
      <div class="stat-item">
        <div class="stat-number">0</div>
        <div class="stat-label">Wishlist</div>
      </div>
    </div>
  </div>

  <!-- RIWAYAT PESANAN -->
  <div class="section-card">
    <div class="section-header">
      <h3 class="section-title"><i class="fas fa-box"></i> Riwayat Pesanan</h3>
      <a href="pesanan.php" class="section-link">Lihat semua <i class="fas fa-arrow-right"></i></a>
    </div>
    <div class="order-list">
      <?php foreach ($user['pesanan'] as $order): ?>
      <div class="order-item">
        <div class="order-icon <?= $order['status'] ?>">
          <?= $order['status'] == 'selesai' ? '✓' : ($order['status'] == 'dikirim' ? '→' : '⟳') ?>
        </div>
        <div class="order-details">
          <div class="order-product"><?= htmlspecialchars($order['produk']) ?></div>
          <div class="order-meta"><?= $order['id'] ?> • <?= $order['tgl'] ?></div>
        </div>
        <div class="order-right">
          <div class="order-price"><?= $order['harga'] ?></div>
          <span class="order-status status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ALAMAT PENGIRIMAN -->
  <div class="section-card">
    <div class="section-header">
      <h3 class="section-title"><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h3>
      <button class="section-link" onclick="openTambahAlamat()" style="background:none; border:none; cursor:pointer">
        <i class="fas fa-plus"></i> Tambah Alamat
      </button>
    </div>
    <div class="address-grid" id="addressGrid"></div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<!-- MODAL EDIT PROFIL -->
<div class="modal" id="editProfilModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Edit Profil</h3>
      <button class="modal-close" onclick="closeModal('editProfilModal')">&times;</button>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label>Nama Lengkap</label>
        <input type="text" id="edit_nama" value="<?= htmlspecialchars($user['nama']) ?>">
      </div>
      <div class="form-group">
        <label>Bio</label>
        <textarea id="edit_bio" rows="3"><?= htmlspecialchars($user['bio']) ?></textarea>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" id="edit_email" value="<?= htmlspecialchars($user['email']) ?>">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" onclick="closeModal('editProfilModal')">Batal</button>
      <button class="btn-primary" onclick="saveProfile()">Simpan Perubahan</button>
    </div>
  </div>
</div>

<!-- MODAL DAFTAR PENJUAL -->
<div class="modal" id="sellerModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3><i class="fas fa-store"></i> Daftar Menjadi Penjual</h3>
      <button class="modal-close" onclick="closeModal('sellerModal')">&times;</button>
    </div>
    <div class="modal-body">
      <p style="margin-bottom: 20px; color: #64748b;">Jual produk hardware-mu dan raih keuntungan bersama PCNexus!</p>
      <div class="form-group">
        <label>Nama Toko</label>
        <input type="text" id="store_name" placeholder="Contoh: Toko Komputer Jaya">
      </div>
      <div class="form-group">
        <label>Nomor Telepon</label>
        <input type="tel" id="store_phone" placeholder="08xxxxxxxxxx">
      </div>
      <div class="form-group">
        <label>Deskripsi Toko (Opsional)</label>
        <textarea id="store_desc" rows="3" placeholder="Ceritakan tentang toko Anda..."></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" onclick="closeModal('sellerModal')">Batal</button>
      <button class="btn-gradient" onclick="registerSeller()">Daftar Sekarang</button>
    </div>
  </div>
</div>

<!-- MODAL ALAMAT -->
<div class="modal" id="alamatModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="alamatModalTitle">Tambah Alamat</h3>
      <button class="modal-close" onclick="closeModal('alamatModal')">&times;</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="alamatIndex" value="-1">
      <div class="form-group"><label>Label</label><input type="text" id="alamat_label" placeholder="Rumah / Kantor"></div>
      <div class="form-group"><label>Nama Penerima</label><input type="text" id="alamat_nama" placeholder="Nama penerima"></div>
      <div class="form-group"><label>No HP</label><input type="text" id="alamat_hp" placeholder="08xxxxxxxxxx"></div>
      <div class="form-group"><label>Detail Alamat</label><textarea id="alamat_detail" rows="3" placeholder="Jalan, nomor rumah, RT/RW"></textarea></div>
      <div class="form-row">
        <div class="form-group"><label>Kota</label><input type="text" id="alamat_kota" placeholder="Kota"></div>
        <div class="form-group"><label>Kode Pos</label><input type="text" id="alamat_kode_pos" placeholder="Kode pos"></div>
      </div>
      <div class="form-group"><label><input type="checkbox" id="alamat_utama"> Jadikan alamat utama</label></div>
    </div>
    <div class="modal-footer">
      <button class="btn-outline" onclick="closeModal('alamatModal')">Batal</button>
      <button class="btn-primary" onclick="saveAlamat()">Simpan Alamat</button>
    </div>
  </div>
</div>

<script>
let cart = [];
let products = [];
const initialAddresses = <?= json_encode($user['alamat'], JSON_UNESCAPED_UNICODE) ?>;
let addresses = Array.isArray(initialAddresses) ? [...initialAddresses] : [];

function showToast(msg) {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 2500);
}

function showCart() {
  window.location.href = '../troli.php';
}

function openModal(id) {
  document.getElementById(id).classList.add('open');
}

function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}

function escHtml(text) {
  return String(text || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function openEditProfil() { openModal('editProfilModal'); }
function openSellerModal() { openModal('sellerModal'); }
function openEditBanner() { showToast('Fitur ganti banner akan segera hadir'); }
function openEditAvatar() { showToast('Fitur ganti foto profil akan segera hadir'); }

async function saveProfile() {
  const payload = {
    nama: document.getElementById('edit_nama').value.trim(),
    bio: document.getElementById('edit_bio').value.trim(),
    email: document.getElementById('edit_email').value.trim()
  };
  const res = await fetch('../profil_page/update/update_profil.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const data = await res.json();
  if (data.success) {
    showToast('Profil berhasil diperbarui');
    closeModal('editProfilModal');
    setTimeout(() => window.location.reload(), 600);
  } else {
    showToast(data.message || 'Gagal memperbarui profil');
  }
}

async function registerSeller() {
  const payload = {
    store_name: document.getElementById('store_name').value.trim(),
    phone: document.getElementById('store_phone').value.trim(),
    store_desc: document.getElementById('store_desc').value.trim()
  };
  const res = await fetch('../profil_page/update/dftr_penjual.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const data = await res.json();
  if (data.success) {
    showToast('Berhasil daftar sebagai penjual');
    closeModal('sellerModal');
    setTimeout(() => window.location.reload(), 600);
  } else {
    showToast(data.message || 'Gagal mendaftar');
  }
}

function renderAddresses() {
  const grid = document.getElementById('addressGrid');
  if (!addresses.length) {
    grid.innerHTML = '<div class="empty-cat" style="grid-column:1/-1">Belum ada alamat. Tambahkan alamat utama untuk checkout.</div>';
    return;
  }
  grid.innerHTML = addresses.map((addr, idx) => `
    <div class="address-card ${addr.utama ? 'primary' : ''}">
      <div class="address-label">${escHtml(addr.label || 'Alamat')}</div>
      <div class="address-name">${escHtml(addr.nama || '-')}</div>
      <div class="address-phone">${escHtml(addr.hp || '-')}</div>
      <div class="address-detail">${escHtml(addr.detail || '-')}</div>
      <div class="address-city">${escHtml(addr.kota || '-')}, ${escHtml(addr.kode_pos || '-')}</div>
      <div class="address-actions">
        <button type="button" onclick="openEditAlamat(${idx})"><i class="fas fa-pen"></i> Edit</button>
        <button type="button" onclick="hapusAlamat(${idx})"><i class="fas fa-trash"></i> Hapus</button>
      </div>
    </div>
  `).join('');
}

function openTambahAlamat() {
  document.getElementById('alamatModalTitle').textContent = 'Tambah Alamat';
  document.getElementById('alamatIndex').value = -1;
  document.getElementById('alamat_label').value = '';
  document.getElementById('alamat_nama').value = '';
  document.getElementById('alamat_hp').value = '';
  document.getElementById('alamat_detail').value = '';
  document.getElementById('alamat_kota').value = '';
  document.getElementById('alamat_kode_pos').value = '';
  document.getElementById('alamat_utama').checked = addresses.length === 0;
  openModal('alamatModal');
}

function openEditAlamat(index) {
  const addr = addresses[index];
  if (!addr) return;
  document.getElementById('alamatModalTitle').textContent = 'Edit Alamat';
  document.getElementById('alamatIndex').value = index;
  document.getElementById('alamat_label').value = addr.label || '';
  document.getElementById('alamat_nama').value = addr.nama || '';
  document.getElementById('alamat_hp').value = addr.hp || '';
  document.getElementById('alamat_detail').value = addr.detail || '';
  document.getElementById('alamat_kota').value = addr.kota || '';
  document.getElementById('alamat_kode_pos').value = addr.kode_pos || '';
  document.getElementById('alamat_utama').checked = !!addr.utama;
  openModal('alamatModal');
}

async function saveAlamat() {
  const index = parseInt(document.getElementById('alamatIndex').value, 10);
  const alamatBaru = {
    label: document.getElementById('alamat_label').value.trim(),
    nama: document.getElementById('alamat_nama').value.trim(),
    hp: document.getElementById('alamat_hp').value.trim(),
    detail: document.getElementById('alamat_detail').value.trim(),
    kota: document.getElementById('alamat_kota').value.trim(),
    kode_pos: document.getElementById('alamat_kode_pos').value.trim(),
    utama: document.getElementById('alamat_utama').checked
  };

  if (!alamatBaru.label || !alamatBaru.nama || !alamatBaru.hp || !alamatBaru.detail || !alamatBaru.kota) {
    showToast('Lengkapi data alamat terlebih dahulu');
    return;
  }

  if (alamatBaru.utama) addresses = addresses.map(a => ({ ...a, utama: false }));
  if (index >= 0) addresses[index] = alamatBaru;
  else addresses.push(alamatBaru);
  if (!addresses.some(a => a.utama) && addresses.length) addresses[0].utama = true;

  const ok = await persistAddresses();
  if (!ok) return;
  closeModal('alamatModal');
  renderAddresses();
  showToast('Alamat berhasil disimpan');
}

async function hapusAlamat(index) {
  addresses.splice(index, 1);
  if (addresses.length && !addresses.some(a => a.utama)) addresses[0].utama = true;
  const ok = await persistAddresses();
  if (!ok) return;
  renderAddresses();
  showToast('Alamat berhasil dihapus');
}

async function persistAddresses() {
  try {
    const res = await fetch('../profil_page/update/address_handler.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'save', addresses })
    });
    const data = await res.json();
    if (!data.success) {
      showToast(data.message || 'Gagal menyimpan alamat');
      return false;
    }
    return true;
  } catch (error) {
    showToast('Gagal terhubung ke server');
    return false;
  }
}

renderAddresses();
</script>
</body>
</html>