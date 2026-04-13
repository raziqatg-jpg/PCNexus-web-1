<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Koneksi database
include '../fungsi/dp.php';

// Cek apakah user adalah penjual dari database
$check_sql = "SELECT is_seller, nama_lengkap, foto_profil FROM pcnexus WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
$is_seller_db = 0;
$nama_user = $_SESSION['nama'] ?? 'User';
$foto_profil_db = $_SESSION['foto_profil'] ?? 'default.png';
if (!$check_stmt) {
    header("Location: ../profil_page/profil.php");
    exit;
}
$check_stmt->bind_param('i', $_SESSION['user_id']);
$check_stmt->execute();
$check_stmt->bind_result($is_seller_db, $nama_user, $foto_profil_db);
$check_stmt->fetch();
$check_stmt->close();

$foto_profil_db = !empty($foto_profil_db) ? $foto_profil_db : 'default.png';

// Jika BUKAN penjual, redirect ke profil
if (!$is_seller_db) {
    header("Location: ../profil_page/profil.php");
    exit;
}

// Set session
$_SESSION['is_seller'] = true;
$_SESSION['nama'] = $_SESSION['nama'] ?? $nama_user;
$_SESSION['nama_toko'] = $_SESSION['nama_toko'] ?? 'Toko Saya';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Penjual – PCNexus</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    /* ── RESET & BASE ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: #f4f2fb;
      color: #1a1a2e;
      min-height: 100vh;
    }

    /* ── TOPBAR ── */
    .topbar {
      background: #fff;
      padding: 14px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 1px 0 rgba(0,0,0,.07);
      position: sticky;
      top: 0;
      z-index: 50;
    }
    .topbar-logo { font-size: 20px; font-weight: 800; color: #7b3fe4; letter-spacing: -0.5px; }
    .topbar-logo span { color: #1a1a2e; }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .topbar-right a {
      text-decoration: none;
      font-size: 13px;
      font-weight: 600;
      color: #7b7b9a;
      padding: 7px 14px;
      border-radius: 10px;
      transition: all .2s;
    }
    .topbar-right a:hover { background: #f4f2fb; color: #7b3fe4; }
    .topbar-right a.active { background: #7b3fe4; color: #fff; }
    .seller-badge {
      background: linear-gradient(135deg, #7b3fe4, #a855f7);
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 20px;
      letter-spacing: .04em;
    }

    /* ── LAYOUT ── */
    .page-wrap {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem 2rem 4rem;
    }

    /* ── TABS ── */
    .tabs {
      display: flex;
      gap: 6px;
      margin-bottom: 2rem;
      background: #fff;
      padding: 6px;
      border-radius: 16px;
      width: fit-content;
      box-shadow: 0 2px 8px rgba(0,0,0,.05);
    }
    .tab-btn {
      padding: 9px 20px;
      border-radius: 11px;
      border: none;
      cursor: pointer;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px;
      font-weight: 600;
      color: #7b7b9a;
      background: transparent;
      transition: all .2s;
      display: flex;
      align-items: center;
      gap: 7px;
    }
    .tab-btn.active { background: #7b3fe4; color: #fff; }
    .tab-btn:hover:not(.active) { background: #f4f2fb; color: #7b3fe4; }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    /* ── STAT CARDS ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      margin-bottom: 2rem;
    }
    .stat-card {
      background: #fff;
      border-radius: 18px;
      padding: 1.4rem 1.5rem;
      box-shadow: 0 2px 12px rgba(0,0,0,.05);
      position: relative;
      overflow: hidden;
      transition: transform .2s;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
    }
    .stat-card.s1::before { background: linear-gradient(90deg, #7b3fe4, #a855f7); }
    .stat-card.s2::before { background: linear-gradient(90deg, #059669, #34d399); }
    .stat-card.s3::before { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
    .stat-card.s4::before { background: linear-gradient(90deg, #3b82f6, #93c5fd); }

    .stat-icon {
      width: 40px; height: 40px;
      border-radius: 11px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px;
      margin-bottom: .9rem;
    }
    .s1 .stat-icon { background: #f3eeff; }
    .s2 .stat-icon { background: #ecfdf5; }
    .s3 .stat-icon { background: #fffbeb; }
    .s4 .stat-icon { background: #eff6ff; }

    .stat-label { font-size: 12px; font-weight: 600; color: #9ca3af; margin-bottom: 4px; text-transform: uppercase; letter-spacing: .04em; }
    .stat-value { font-size: 1.6rem; font-weight: 800; color: #1a1a2e; letter-spacing: -1px; }
    .stat-sub   { font-size: 12px; color: #9ca3af; margin-top: 4px; }
    .stat-up    { color: #059669; font-weight: 600; }
    .stat-dn    { color: #ef4444; font-weight: 600; }

    /* ── CHART AREA ── */
    .charts-row {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 16px;
      margin-bottom: 2rem;
    }
    .chart-card {
      background: #fff;
      border-radius: 18px;
      padding: 1.5rem;
      box-shadow: 0 2px 12px rgba(0,0,0,.05);
    }
    .chart-card h3 {
      font-size: 15px;
      font-weight: 700;
      color: #1a1a2e;
      margin-bottom: 1.25rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .chart-card h3 span {
      font-size: 11px;
      font-weight: 600;
      color: #9ca3af;
      background: #f4f2fb;
      padding: 4px 10px;
      border-radius: 8px;
    }
    .chart-wrap { position: relative; height: 220px; }

    /* ── PRODUK TABLE ── */
    .section-card {
      background: #fff;
      border-radius: 18px;
      padding: 1.5rem;
      box-shadow: 0 2px 12px rgba(0,0,0,.05);
      margin-bottom: 1.5rem;
    }
    .section-card-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.25rem;
    }
    .section-card-head h3 { font-size: 15px; font-weight: 700; }
    .btn-add {
      background: #7b3fe4;
      color: #fff;
      border: none;
      padding: 8px 18px;
      border-radius: 10px;
      cursor: pointer;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px;
      font-weight: 600;
      transition: background .2s;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .btn-add:hover { background: #6a34c9; }

    .prod-table { width: 100%; border-collapse: collapse; }
    .prod-table th {
      font-size: 11px;
      font-weight: 700;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: .04em;
      text-align: left;
      padding: 0 12px 10px;
      border-bottom: 1.5px solid #f0f0f0;
    }
    .prod-table td {
      padding: 12px;
      font-size: 13px;
      color: #374151;
      border-bottom: 1px solid #f9f9f9;
      vertical-align: middle;
    }
    .prod-table tr:last-child td { border-bottom: none; }
    .prod-table tr:hover td { background: #faf9ff; }

    .prod-icon-cell { font-size: 1.6rem; width: 44px; height: 44px; background: #f4f2fb; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .prod-name-cell b { display: block; font-size: 13px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
    .prod-name-cell small { font-size: 11px; color: #9ca3af; }

    .pill {
      display: inline-block;
      font-size: 11px;
      font-weight: 600;
      padding: 3px 10px;
      border-radius: 999px;
    }
    .pill-green  { background: #ecfdf5; color: #059669; }
    .pill-yellow { background: #fffbeb; color: #d97706; }
    .pill-red    { background: #fef2f2; color: #ef4444; }
    .pill-purple { background: #f3eeff; color: #7b3fe4; }

    .stars { color: #f59e0b; font-size: 12px; }

    .act-btn {
      padding: 5px 12px;
      border-radius: 8px;
      border: 1.5px solid #e5e5e5;
      background: #fff;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all .2s;
      color: #374151;
    }
    .act-btn:hover { border-color: #7b3fe4; color: #7b3fe4; background: #f3eeff; }
    .act-btn.del { color: #ef4444; }
    .act-btn.del:hover { border-color: #ef4444; background: #fef2f2; }

    /* ── UPLOAD FORM ── */
    .upload-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.25rem;
    }
    .upload-grid .full { grid-column: 1 / -1; }

    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 13px; font-weight: 600; color: #374151; }
    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 10px 14px;
      border: 1.5px solid #e5e5e5;
      border-radius: 11px;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 13px;
      color: #1a1a2e;
      background: #fafafa;
      outline: none;
      transition: border .2s, background .2s;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: #7b3fe4;
      background: #fff;
    }
    .form-group textarea { resize: vertical; min-height: 90px; }

    .upload-drop {
      border: 2px dashed #d8d0f0;
      border-radius: 14px;
      padding: 2.5rem;
      text-align: center;
      cursor: pointer;
      transition: all .2s;
      background: #faf9ff;
    }
    .upload-drop:hover { border-color: #7b3fe4; background: #f3eeff; }
    .upload-drop .ud-icon { font-size: 2.5rem; margin-bottom: .5rem; }
    .upload-drop p { font-size: 13px; color: #9ca3af; font-weight: 500; }
    .upload-drop p strong { color: #7b3fe4; }

    .btn-submit {
      background: linear-gradient(135deg, #7b3fe4, #a855f7);
      color: #fff;
      border: none;
      padding: 13px 32px;
      border-radius: 12px;
      cursor: pointer;
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-size: 14px;
      font-weight: 700;
      transition: opacity .2s, transform .2s;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }
    .btn-submit:hover { opacity: .9; transform: translateY(-1px); }

    /* ── REVIEW CARDS ── */
    .review-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 14px;
    }
    .review-card {
      background: #faf9ff;
      border: 1.5px solid #ede9fe;
      border-radius: 16px;
      padding: 1.25rem;
    }
    .review-top { display: flex; align-items: center; gap: 10px; margin-bottom: .75rem; }
    .review-avatar {
      width: 36px; height: 36px;
      border-radius: 50%;
      background: linear-gradient(135deg, #7b3fe4, #a855f7);
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-weight: 700; font-size: 13px;
    }
    .review-meta b { display: block; font-size: 13px; font-weight: 700; }
    .review-meta small { font-size: 11px; color: #9ca3af; }
    .review-text { font-size: 13px; color: #374151; line-height: 1.6; margin-bottom: .75rem; }
    .review-prod { font-size: 11px; color: #7b3fe4; font-weight: 600; background: #f3eeff; padding: 3px 10px; border-radius: 8px; display: inline-block; }

    /* ── TOAST ── */
    #seller-toast {
      position: fixed;
      bottom: 28px; left: 50%;
      transform: translateX(-50%) translateY(80px);
      background: #1a1a2e;
      color: #fff;
      padding: 12px 24px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      z-index: 999;
      transition: transform .3s ease;
      white-space: nowrap;
      pointer-events: none;
    }
    #seller-toast.show { transform: translateX(-50%) translateY(0); }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .charts-row { grid-template-columns: 1fr; }
      .upload-grid { grid-template-columns: 1fr; }
      .upload-grid .full { grid-column: 1; }
    }
    @media (max-width: 600px) {
      .page-wrap { padding: 1rem 1rem 3rem; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
      .stat-value { font-size: 1.2rem; }
      .topbar { padding: 12px 16px; }
      .tabs { width: 100%; overflow-x: auto; }
      .prod-table thead { display: none; }
      .prod-table td { display: block; padding: 6px 12px; }
      .prod-table tr { border: 1.5px solid #f0f0f0; border-radius: 12px; display: block; margin-bottom: 10px; }
    }
  </style>
</head>
<body>

<!-- TOPBAR -->
<header class="topbar">
  <div class="topbar-logo">PC<span>Nexus</span></div>
  <div class="topbar-right">
    <span class="seller-badge">✦ Seller</span>
    <a href="../index.php">🏠 Beranda</a>
    <a href="#" class="active">📦 Dashboard Penjual</a>
    <a href="../profil_page/profil.php" style="display:flex;align-items:center;gap:6px;padding:4px 10px;">
      <img src="../uploads/profile/<?= htmlspecialchars($foto_profil_db) ?>" 
           alt="Profil" 
           style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid #7b3fe4;"
           onerror="this.src='../uploads/profile/default.png'">
    </a>
  </div>
</header>

<div class="page-wrap">

  <!-- PAGE TITLE -->
  <div style="margin-bottom:1.75rem">
    <h1 style="font-size:1.5rem;font-weight:800;color:#1a1a2e;letter-spacing:-0.5px">
      Dashboard Penjual
    </h1>
    <p style="font-size:13px;color:#9ca3af;margin-top:4px">
      Selamat datang kembali, <strong style="color:#7b3fe4"><?= htmlspecialchars($_SESSION['nama'] ?? 'Penjual') ?></strong> 👋
    </p>
  </div>

  <!-- TABS -->
  <div class="tabs">
    <button class="tab-btn active" onclick="switchTab('overview', this)">📊 Overview</button>
    <button class="tab-btn" onclick="switchTab('produk', this)">📦 Produk Saya</button>
    <button class="tab-btn" onclick="switchTab('upload', this)">➕ Upload Produk</button>
  </div>

  <!-- ==================== TAB: OVERVIEW ==================== -->
  <div class="tab-panel active" id="tab-overview">

    <!-- STAT CARDS -->
    <div class="stats-grid">
      <div class="stat-card s1">
        <div class="stat-icon">📦</div>
        <div class="stat-label">Total Produk</div>
        <div class="stat-value" id="stat-total-produk">12</div>
        <div class="stat-sub"><span class="stat-up">+2</span> produk bulan ini</div>
      </div>
      <div class="stat-card s2">
        <div class="stat-icon">💰</div>
        <div class="stat-label">Total Keuntungan</div>
        <div class="stat-value" id="stat-keuntungan">Rp42,5jt</div>
        <div class="stat-sub"><span class="stat-up">↑ 18%</span> vs bulan lalu</div>
      </div>
      <div class="stat-card s3">
        <div class="stat-icon">🛒</div>
        <div class="stat-label">Produk Terjual</div>
        <div class="stat-value" id="stat-terjual">847</div>
        <div class="stat-sub"><span class="stat-up">+64</span> minggu ini</div>
      </div>
      <div class="stat-card s4">
        <div class="stat-icon">📦</div>
        <div class="stat-label">Sisa Stok</div>
        <div class="stat-value" id="stat-stock">0</div>
        <div class="stat-sub">total unit yang masih tersedia</div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="charts-row">
      <div class="chart-card">
        <h3>Grafik Penjualan <span>6 Bulan Terakhir</span></h3>
        <div class="chart-wrap">
          <canvas id="chartPenjualan"></canvas>
        </div>
      </div>
      <div class="chart-card">
        <h3>Kategori Produk <span>Distribusi</span></h3>
        <div class="chart-wrap">
          <canvas id="chartKategori"></canvas>
        </div>
      </div>
    </div>

    <!-- TOP PRODUK -->
    <div class="section-card">
      <div class="section-card-head">
        <h3>🏆 Produk Terlaris</h3>
      </div>
      <table class="prod-table" id="top-table">
        <thead>
          <tr>
            <th></th>
            <th>Produk</th>
            <th>Terjual</th>
            <th>Pendapatan</th>
            <th>Rating</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="top-tbody"></tbody>
      </table>
    </div>

    <div class="section-card">
      <div class="section-card-head">
        <h3>🧾 Pembeli Terbaru</h3>
      </div>
      <table class="prod-table">
        <thead>
          <tr>
            <th>Pembeli</th>
            <th>Produk</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th>Waktu</th>
          </tr>
        </thead>
        <tbody id="buyer-tbody">
          <tr><td colspan="5" style="color:#9ca3af">Memuat data pembeli...</td></tr>
        </tbody>
      </table>
    </div>

  </div><!-- end tab-overview -->

  <!-- ==================== TAB: PRODUK SAYA ==================== -->
  <div class="tab-panel" id="tab-produk">
    <div class="section-card">
      <div class="section-card-head">
        <h3>📦 Semua Produk Saya</h3>
        <button class="btn-add" onclick="switchTab('upload', document.querySelectorAll('.tab-btn')[2])">
          ➕ Tambah Produk
        </button>
      </div>
      <table class="prod-table">
        <thead>
          <tr>
            <th></th>
            <th>Produk</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Terjual</th>
            <th>Rating</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="all-tbody"></tbody>
      </table>
    </div>
  </div>

  <!-- ==================== TAB: UPLOAD PRODUK ==================== -->
  <div class="tab-panel" id="tab-upload">
    <div class="section-card">
      <div class="section-card-head">
        <h3>➕ Upload Produk Baru</h3>
      </div>

      <div class="upload-grid">
        <div class="form-group">
          <label>Kategori Produk *</label>
          <select id="u-cat">
            <option value="">Pilih kategori...</option>
            <option value="laptop">💻 Laptop</option>
            <option value="pc">🖥️ PC Desktop</option>
            <option value="sparepart">🔧 Sparepart</option>
            <option value="vga">🎮 VGA / GPU</option>
            <option value="monitor">🖥 Monitor</option>
            <option value="aksesoris">🖱️ Aksesoris</option>
            <option value="storage">💾 Storage</option>
            <option value="networking">📡 Networking</option>
          </select>
        </div>

        <div class="form-group">
          <label>Merek *</label>
          <input id="u-brand" type="text" placeholder="Contoh: Lenovo, ASUS, MSI...">
        </div>

        <div class="form-group full">
          <label>Nama Produk *</label>
          <input id="u-name" type="text" placeholder="Contoh: ThinkPad T495 Ryzen 7 Pro">
        </div>

        <div class="form-group">
          <label>Harga Jual (Rp) *</label>
          <input id="u-price" type="number" placeholder="3000000">
        </div>

        <div class="form-group">
          <label>Stok *</label>
          <input id="u-stock" type="number" placeholder="1" value="1" min="0">
        </div>

        <div class="form-group">
          <label>Kondisi</label>
          <select id="u-cond">
            <option value="Baru">Baru</option>
            <option value="Bekas">Bekas</option>
            <option value="Refurbished">Refurbished</option>
          </select>
        </div>

        <div class="form-group">
          <label>Berat (kg)</label>
          <input id="u-weight" type="number" placeholder="1.5" step="0.1">
        </div>

        <div class="form-group full">
          <label>Spesifikasi / Deskripsi</label>
          <textarea id="u-desc" placeholder="Contoh: Processor i5-2450M, RAM 8GB DDR3, SSD 128GB, VGA Nvidia GT520..."></textarea>
        </div>

        <div class="form-group full">
          <label>Foto Produk</label>
          <div class="upload-drop" onclick="triggerUpload()" id="drop-area">
            <div class="ud-icon">📸</div>
            <p><strong>Klik untuk upload</strong> atau drag & drop</p>
            <p style="margin-top:4px;font-size:11px">PNG, JPG, WEBP – maks 5MB</p>
          </div>
          <input type="file" id="file-input" accept="image/*" style="display:none" onchange="previewFile(this)">
        </div>

        <div class="full" style="padding-top:.5rem">
          <button class="btn-submit" onclick="submitNewProduct()">
            🚀 Upload Produk
          </button>
        </div>
      </div>
    </div>
  </div>

</div><!-- end page-wrap -->

<div id="seller-toast"></div>

<script>
// ── DATA DUMMY ──
const myProducts = [
  { id:1,  cat:'laptop',     brand:'Lenovo',   name:'ThinkPad T495 Ryzen 7', price:3000000,  stock:5,  sold:84,  rating:4.9, cond:'Bekas', icon:'💻', status:'aktif'    },
  { id:2,  cat:'laptop',     brand:'ASUS',     name:'Gaming X441U Core i5',  price:2200000,  stock:2,  sold:31,  rating:4.7, cond:'Bekas', icon:'💻', status:'aktif'    },
  { id:3,  cat:'vga',        brand:'ASUS TUF', name:'RTX 5090 OC 32GB',      price:32000000, stock:1,  sold:3,   rating:5.0, cond:'Baru',  icon:'🎮', status:'aktif'    },
  { id:4,  cat:'sparepart',  brand:'Kingston', name:'RAM DDR4 16GB 3200MHz', price:350000,   stock:20, sold:312, rating:4.8, cond:'Baru',  icon:'🔧', status:'aktif'    },
  { id:5,  cat:'storage',    brand:'Samsung',  name:'SSD 970 EVO 500GB',     price:750000,   stock:0,  sold:88,  rating:5.0, cond:'Baru',  icon:'💾', status:'habis'    },
  { id:6,  cat:'aksesoris',  brand:'Logitech', name:'Mouse Gaming G502',     price:450000,   stock:8,  sold:147, rating:4.9, cond:'Baru',  icon:'🖱️', status:'aktif'    },
  { id:7,  cat:'monitor',    brand:'LG',       name:'Monitor 24" IPS 75Hz',  price:1800000,  stock:3,  sold:29,  rating:4.6, cond:'Baru',  icon:'🖥', status:'aktif'    },
  { id:8,  cat:'networking', brand:'TP-Link',  name:'Router WiFi 6 AX3000',  price:650000,   stock:6,  sold:53,  rating:4.7, cond:'Baru',  icon:'📡', status:'nonaktif' },
];

const reviews = [
  { user:'Budi S.',    rating:5, text:'Barang sesuai deskripsi, pengiriman cepat! Laptop mulus banget kondisinya.', prod:'ThinkPad T495',   date:'2 hari lalu'   },
  { user:'Ayu R.',     rating:5, text:'RAM-nya ori, performa bagus. Seller responsif dan ramah.',                   prod:'RAM DDR4 16GB',    date:'4 hari lalu'   },
  { user:'Dimas P.',   rating:4, text:'GPU kenceng banget buat gaming. Harga sedikit mahal tapi worth it.',         prod:'RTX 5090 OC',      date:'1 minggu lalu' },
  { user:'Sari N.',    rating:5, text:'Mouse G502 sudah pakai 2 minggu, mantap! Rekomended seller ini.',            prod:'Mouse Gaming G502', date:'1 minggu lalu' },
  { user:'Fajar K.',   rating:4, text:'Monitor bagus, warna akurat. Pengiriman agak lama tapi packingnya aman.',    prod:'Monitor 24" IPS',  date:'2 minggu lalu' },
  { user:'Rina M.',    rating:5, text:'SSD cepet banget, boot Windows 10 detik. Penjual terpercaya!',               prod:'SSD 970 EVO 500GB', date:'3 minggu lalu' },
];

function fmt(n) { return 'Rp' + n.toLocaleString('id-ID'); }

function fmtCompact(n) {
  if (n >= 1000000) return 'Rp' + (n/1000000).toFixed(1) + 'jt';
  if (n >= 1000)    return 'Rp' + (n/1000).toFixed(0) + 'rb';
  return 'Rp' + n;
}

function stars(r) {
  const full  = Math.floor(r);
  const empty = 5 - full;
  return '<span class="stars">' + '★'.repeat(full) + '<span style="color:#e5e7eb">' + '★'.repeat(empty) + '</span></span>';
}

function statusPill(s) {
  if (s === 'aktif')    return '<span class="pill pill-green">Aktif</span>';
  if (s === 'habis')    return '<span class="pill pill-red">Habis</span>';
  if (s === 'nonaktif') return '<span class="pill pill-yellow">Nonaktif</span>';
  return s;
}

// ── RENDER PRODUK TABLE ──
function renderAllProd() {
  const tbody = document.getElementById('all-tbody');
  tbody.innerHTML = myProducts.map(p => `
    <tr>
      <td><div class="prod-icon-cell">${p.icon}</div></td>
      <td class="prod-name-cell"><b>${p.name}</b><small>${p.brand} · ${p.cond}</small></td>
      <td><b style="color:#7b3fe4">${fmt(p.price)}</b></td>
      <td><span class="${p.stock === 0 ? 'pill pill-red' : 'pill pill-green'}">${p.stock} unit</span></td>
      <td><b>${p.sold}</b></td>
      <td>${stars(p.rating)} <small style="color:#9ca3af">${p.rating}</small></td>
      <td>${statusPill(p.status)}</td>
      <td style="display:flex;gap:6px;flex-wrap:wrap">
        <button class="act-btn" onclick="editProd(${p.id})">✏️ Edit</button>
        <button class="act-btn del" onclick="deleteProd(${p.id})">🗑</button>
      </td>
    </tr>`).join('');
}

// ── RENDER TOP PRODUK ──
function renderTopProd() {
  const sorted = [...myProducts].sort((a,b) => b.sold - a.sold).slice(0,5);
  document.getElementById('top-tbody').innerHTML = sorted.map(p => `
    <tr>
      <td><div class="prod-icon-cell">${p.icon}</div></td>
      <td class="prod-name-cell"><b>${p.name}</b><small>${p.brand}</small></td>
      <td><b>${p.sold}</b> terjual</td>
      <td><b style="color:#059669">${fmtCompact(p.price * p.sold)}</b></td>
      <td>${stars(p.rating)} ${p.rating}</td>
      <td>${statusPill(p.status)}</td>
    </tr>`).join('');
}

// ── RENDER ULASAN ──
function renderReviews() {
  document.getElementById('review-grid').innerHTML = reviews.map(r => `
    <div class="review-card">
      <div class="review-top">
        <div class="review-avatar">${r.user.slice(0,2).toUpperCase()}</div>
        <div class="review-meta">
          <b>${r.user}</b>
          <small>${stars(r.rating)} · ${r.date}</small>
        </div>
      </div>
      <p class="review-text">"${r.text}"</p>
      <span class="review-prod">📦 ${r.prod}</span>
    </div>`).join('');

  // rating bars
  const counts = [0,0,0,0,0];
  reviews.forEach(r => counts[r.rating - 1]++);
  document.getElementById('rating-bars').innerHTML = [5,4,3,2,1].map(star => {
    const cnt = counts[star-1];
    const pct = reviews.length ? Math.round((cnt/reviews.length)*100) : 0;
    return `<div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
      <span style="font-size:12px;font-weight:600;color:#374151;width:16px">${star}</span>
      <span style="color:#f59e0b;font-size:11px">★</span>
      <div style="flex:1;height:8px;background:#f0f0f0;border-radius:999px;overflow:hidden">
        <div style="height:100%;width:${pct}%;background:linear-gradient(90deg,#7b3fe4,#a855f7);border-radius:999px"></div>
      </div>
      <span style="font-size:11px;color:#9ca3af;width:24px;text-align:right">${cnt}</span>
    </div>`;
  }).join('');
}

// ── CHARTS ──
function initCharts() {
  // Line chart penjualan
  const ctxLine = document.getElementById('chartPenjualan').getContext('2d');
  new Chart(ctxLine, {
    type: 'line',
    data: {
      labels: ['Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr'],
      datasets: [{
        label: 'Penjualan (unit)',
        data: [95, 128, 87, 160, 143, 180],
        borderColor: '#7b3fe4',
        backgroundColor: 'rgba(123,63,228,.08)',
        borderWidth: 2.5,
        pointBackgroundColor: '#7b3fe4',
        pointRadius: 4,
        tension: 0.4,
        fill: true,
      }, {
        label: 'Pendapatan (juta)',
        data: [18, 24, 16, 30, 27, 35],
        borderColor: '#059669',
        backgroundColor: 'rgba(5,150,105,.06)',
        borderWidth: 2.5,
        pointBackgroundColor: '#059669',
        pointRadius: 4,
        tension: 0.4,
        fill: true,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'top', labels: { font: { family: 'Plus Jakarta Sans', size: 11 }, usePointStyle: true } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } },
        y: { grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } } }
      }
    }
  });

  // Doughnut kategori
  const catCount = {};
  myProducts.forEach(p => { catCount[p.cat] = (catCount[p.cat] || 0) + 1; });
  const labels = Object.keys(catCount);
  const data   = Object.values(catCount);
  const colors = ['#7b3fe4','#059669','#f59e0b','#3b82f6','#ef4444','#ec4899','#06b6d4','#8b5cf6'];

  const ctxDot = document.getElementById('chartKategori').getContext('2d');
  new Chart(ctxDot, {
    type: 'doughnut',
    data: {
      labels: labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
      datasets: [{ data, backgroundColor: colors.slice(0, labels.length), borderWidth: 2, borderColor: '#fff' }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '65%',
      plugins: {
        legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11 }, usePointStyle: true, padding: 10 } }
      }
    }
  });
}

// ── TABS ──
function switchTab(name, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  btn.classList.add('active');
}

// ── UPLOAD ──
function triggerUpload() { document.getElementById('file-input').click(); }
function previewFile(input) {
  if (input.files && input.files[0]) {
    const drop = document.getElementById('drop-area');
    drop.innerHTML = '<div class="ud-icon">✅</div><p><strong>' + input.files[0].name + '</strong></p><p style="font-size:11px;margin-top:4px">Klik untuk ganti</p>';
  }
}

function submitNewProduct() {
  const cat   = document.getElementById('u-cat').value;
  const brand = document.getElementById('u-brand').value.trim();
  const name  = document.getElementById('u-name').value.trim();
  const price = parseInt(document.getElementById('u-price').value) || 0;
  const stock = parseInt(document.getElementById('u-stock').value) || 0;
  const cond  = document.getElementById('u-cond').value;
  const desc  = document.getElementById('u-desc').value.trim();

  if (!cat || !brand || !name || !price) {
    toast('⚠️ Lengkapi semua field wajib (*)'); return;
  }

  const icons = { laptop:'💻', pc:'🖥️', sparepart:'🔧', vga:'🎮', monitor:'🖥', aksesoris:'🖱️', storage:'💾', networking:'📡' };
  const newP = { id: myProducts.length+1, cat, brand, name, price, stock, sold:0, rating:5.0, cond, icon: icons[cat]||'📦', status:'aktif' };
  myProducts.unshift(newP);

  // reset form
  ['u-cat','u-brand','u-name','u-price','u-stock','u-desc'].forEach(id => { document.getElementById(id).value = ''; });
  document.getElementById('drop-area').innerHTML = '<div class="ud-icon">📸</div><p><strong>Klik untuk upload</strong> atau drag & drop</p><p style="margin-top:4px;font-size:11px">PNG, JPG, WEBP – maks 5MB</p>';

  toast('🚀 Produk berhasil diupload!');
  renderAllProd();
  updateStats();

  // switch ke tab produk
  setTimeout(() => switchTab('produk', document.querySelectorAll('.tab-btn')[1]), 1200);
}

// ── EDIT / DELETE ──
function editProd(id) { toast('✏️ Fitur edit akan segera hadir!'); }
function deleteProd(id) {
  const idx = myProducts.findIndex(p => p.id === id);
  if (idx === -1) return;
  const name = myProducts[idx].name;
  myProducts.splice(idx, 1);
  renderAllProd();
  updateStats();
  toast('🗑 "' + name.slice(0,20) + '..." dihapus');
}

// ── UPDATE STATS ──
function updateStats() {
  const totalProduk   = myProducts.length;
  const totalTerjual  = myProducts.reduce((a,b) => a+b.sold, 0);
  const totalKeuntungan = myProducts.reduce((a,b) => a + b.price*b.sold, 0);
  const totalStock    = myProducts.reduce((a,b) => a + (parseInt(b.stock || 0)), 0);

  document.getElementById('stat-total-produk').textContent = totalProduk;
  document.getElementById('stat-terjual').textContent      = totalTerjual.toLocaleString('id-ID');
  document.getElementById('stat-keuntungan').textContent   = fmtCompact(totalKeuntungan);
  document.getElementById('stat-stock').textContent        = totalStock.toLocaleString('id-ID');
}

// ── TOAST ──
function toast(msg) {
  const el = document.getElementById('seller-toast');
  el.textContent = msg;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 2800);
}

// ================================================================
// KONEKSI KE BACKEND PHP (penjual_handler.php)
// ================================================================
const HANDLER = '../fungsi/penjual_handler.php';

let chartPenjualanInstance = null;
let chartKategoriInstance  = null;

// ── INIT: load semua data dari DB ─────────────────────────────────
document.addEventListener('DOMContentLoaded', async function() {
  await Promise.all([
    loadProdukDB(),
    loadStatsDB(),
    loadChartDB(),
    loadPembeliDB(),
  ]);
});

// ── Load produk dari DB ───────────────────────────────────────────
async function loadProdukDB() {
  try {
    const form = new FormData();
    form.append('action', 'get_produk');
    const res  = await fetch(HANDLER, { method:'POST', body: form });
    const data = await res.json();
    if (data.success && data.produk.length) {
      // Merge dengan myProducts untuk render (ganti data dummy)
      myProducts.length = 0;
      data.produk.forEach(p => myProducts.push({
        id:     p.id,
        cat:    p.kategori,
        brand:  p.merek,
        name:   p.nama_produk,
        price:  parseInt(p.harga),
        stock:  parseInt(p.stok),
        sold:   parseInt(p.sold),
        rating: parseFloat(p.rating||5),
        cond:   p.kondisi,
        icon:   {laptop:'💻',pc:'🖥️',sparepart:'🔧',vga:'🎮',monitor:'🖥',aksesoris:'🖱️',storage:'💾',networking:'📡'}[p.kategori]||'📦',
        status: p.status,
        foto:   p.foto,
      }));
    }
  } catch(e) { console.warn('loadProdukDB error', e); }
  renderAllProd();
  renderTopProd();
  updateStats();
}

// ── Load statistik dari DB ────────────────────────────────────────
async function loadStatsDB() {
  try {
    const form = new FormData();
    form.append('action', 'get_stats');
    const res  = await fetch(HANDLER, { method:'POST', body: form });
    const data = await res.json();
    if (data.success) {
      document.getElementById('stat-total-produk').textContent = data.total_produk;
      document.getElementById('stat-terjual').textContent      = parseInt(data.total_sold).toLocaleString('id-ID');
      document.getElementById('stat-keuntungan').textContent   = fmtCompact(data.total_revenue);
      document.getElementById('stat-stock').textContent        = parseInt(data.total_stock || 0).toLocaleString('id-ID');
    }
  } catch(e) { /* pakai nilai dari updateStats() saja */ updateStats(); }
}

// ── Load & render grafik dari DB ──────────────────────────────────
async function loadChartDB() {
  let labels      = ['Nov','Des','Jan','Feb','Mar','Apr'];
  let penjualan   = [0,0,0,0,0,0];
  let pendapatan  = [0,0,0,0,0,0];

  try {
    const form = new FormData();
    form.append('action', 'get_chart');
    const res  = await fetch(HANDLER, { method:'POST', body: form });
    const data = await res.json();
    if (data.success) {
      labels     = data.labels;
      penjualan  = data.penjualan;
      pendapatan = data.pendapatan;
    }
  } catch(e) { console.warn('loadChartDB error', e); }

  // Destroy existing charts jika ada
  if (chartPenjualanInstance) chartPenjualanInstance.destroy();
  if (chartKategoriInstance)  chartKategoriInstance.destroy();

  // Line chart penjualan
  const ctxLine = document.getElementById('chartPenjualan').getContext('2d');
  chartPenjualanInstance = new Chart(ctxLine, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Penjualan (unit)',
        data: penjualan,
        borderColor: '#7b3fe4',
        backgroundColor: 'rgba(123,63,228,.08)',
        borderWidth: 2.5,
        pointBackgroundColor: '#7b3fe4',
        pointRadius: 4,
        tension: 0.4,
        fill: true,
      }, {
        label: 'Pendapatan (juta)',
        data: pendapatan,
        borderColor: '#059669',
        backgroundColor: 'rgba(5,150,105,.06)',
        borderWidth: 2.5,
        pointBackgroundColor: '#059669',
        pointRadius: 4,
        tension: 0.4,
        fill: true,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position:'top', labels:{ font:{family:'Plus Jakarta Sans',size:11}, usePointStyle:true } } },
      scales: {
        x: { grid:{display:false}, ticks:{font:{family:'Plus Jakarta Sans',size:11}} },
        y: { grid:{color:'rgba(0,0,0,.04)'}, ticks:{font:{family:'Plus Jakarta Sans',size:11}} }
      }
    }
  });

  // Doughnut kategori
  const catCount = {};
  myProducts.forEach(p => { catCount[p.cat] = (catCount[p.cat]||0) + 1; });
  const dLabels = Object.keys(catCount);
  const dData   = Object.values(catCount);
  const colors  = ['#7b3fe4','#059669','#f59e0b','#3b82f6','#ef4444','#ec4899','#06b6d4','#8b5cf6'];

  const ctxDot = document.getElementById('chartKategori').getContext('2d');
  chartKategoriInstance = new Chart(ctxDot, {
    type: 'doughnut',
    data: {
      labels: dLabels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
      datasets: [{ data: dData.length ? dData : [1], backgroundColor: dData.length ? colors.slice(0, dLabels.length) : ['#e5e7eb'], borderWidth:2, borderColor:'#fff' }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '65%',
      plugins: { legend:{position:'bottom', labels:{font:{family:'Plus Jakarta Sans',size:11}, usePointStyle:true, padding:10}} }
    }
  });
}

// ── Upload produk ke DB (override fungsi lama) ────────────────────
async function submitNewProduct() {
  const cat        = document.getElementById('u-cat').value;
  const brand      = document.getElementById('u-brand').value.trim();
  const name       = document.getElementById('u-name').value.trim();
  const price      = parseInt(document.getElementById('u-price').value) || 0;
  const stock      = parseInt(document.getElementById('u-stock').value) || 0;
  const cond       = document.getElementById('u-cond').value;
  const desc       = document.getElementById('u-desc').value.trim();
  const weight     = document.getElementById('u-weight').value || 1;
  const fileInput  = document.getElementById('file-input');

  if (!cat || !brand || !name || !price) {
    toast('⚠️ Lengkapi semua field wajib (*)'); return;
  }

  const form = new FormData();
  form.append('action',      'upload_produk');
  form.append('kategori',    cat);
  form.append('merek',       brand);
  form.append('nama_produk', name);
  form.append('harga',       price);
  form.append('stok',        stock);
  form.append('kondisi',     cond);
  form.append('berat',       weight);
  form.append('deskripsi',   desc);
  if (fileInput.files[0]) form.append('foto', fileInput.files[0]);

  const btn = document.querySelector('.btn-submit');
  btn.disabled    = true;
  btn.textContent = '⏳ Mengupload...';

  try {
    const res  = await fetch(HANDLER, { method:'POST', body: form });
    const raw = await res.text();
    let data;
    try {
      data = JSON.parse(raw);
    } catch (e) {
      throw new Error(raw || 'Response server bukan JSON');
    }
    if (data.success) {
      toast('🚀 Produk berhasil diupload!');
      // Reset form
      ['u-cat','u-brand','u-name','u-price','u-stock','u-desc','u-weight'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
      });
      document.getElementById('drop-area').innerHTML = '<div class="ud-icon">📸</div><p><strong>Klik untuk upload</strong> atau drag & drop</p><p style="margin-top:4px;font-size:11px">PNG, JPG, WEBP – maks 5MB</p>';
      fileInput.value = '';
      // Reload produk & chart
      await loadProdukDB();
      await loadChartDB();
      setTimeout(() => switchTab('produk', document.querySelectorAll('.tab-btn')[1]), 1200);
    } else {
      toast('❌ ' + (data.message || 'Gagal upload'));
    }
  } catch(e) {
    toast('❌ Gagal terhubung ke server: ' + e.message);
  }

  btn.disabled    = false;
  btn.textContent = '🚀 Upload Produk';
}

// ── Hapus produk (ke DB) ──────────────────────────────────────────
async function deleteProd(id) {
  if (!confirm('Yakin ingin menghapus produk ini?')) return;
  const form = new FormData();
  form.append('action',    'hapus_produk');
  form.append('produk_id', id);
  const res  = await fetch(HANDLER, { method:'POST', body: form });
  const data = await res.json();
  if (data.success) {
    const idx = myProducts.findIndex(p => p.id == id);
    if (idx !== -1) { toast('🗑 "' + myProducts[idx].name.slice(0,20) + '..." dihapus'); myProducts.splice(idx, 1); }
    renderAllProd();
    updateStats();
  }
}

// ── Helper: waktu relatif ─────────────────────────────────────────
function timeSince(dateStr) {
  const d    = new Date(dateStr);
  const secs = Math.floor((new Date() - d) / 1000);
  if (secs < 60)    return 'Baru saja';
  if (secs < 3600)  return Math.floor(secs/60) + ' menit lalu';
  if (secs < 86400) return Math.floor(secs/3600) + ' jam lalu';
  return Math.floor(secs/86400) + ' hari lalu';
}

async function loadPembeliDB() {
  const tbody = document.getElementById('buyer-tbody');
  if (!tbody) return;

  try {
    const form = new FormData();
    form.append('action', 'get_pembeli');
    const res = await fetch(HANDLER, { method: 'POST', body: form });
    const data = await res.json();

    if (!data.success || !Array.isArray(data.pembeli) || data.pembeli.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" style="color:#9ca3af">Belum ada data pembeli.</td></tr>';
      return;
    }

    tbody.innerHTML = data.pembeli.map(row => `
      <tr>
        <td><b>${row.nama_pembeli}</b></td>
        <td>${row.nama_produk}</td>
        <td>${parseInt(row.qty || 0)}</td>
        <td><b style="color:#059669">${fmt(parseInt(row.subtotal || 0))}</b></td>
        <td>${timeSince(row.created_at)}</td>
      </tr>
    `).join('');
  } catch (e) {
    tbody.innerHTML = '<tr><td colspan="5" style="color:#ef4444">Gagal memuat data pembeli.</td></tr>';
  }
}
</script>
</body>
</html>