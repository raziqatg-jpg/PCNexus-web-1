<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data user dari database
include 'fungsi/dp.php';
$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT nama_lengkap, foto_profil FROM pcnexus WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    $user = ['nama' => 'User', 'foto_profil' => 'default.png'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - PCNexus</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* Tambahan style untuk profile-nav */
    .profile-nav img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<header class="navbar">
  <a href="index.php" class="logo">PCNexus</a>
  <div class="search-box">
    <input type="text" placeholder="Cari laptop...">
    <button type="button">🔍</button>
  </div>
  <div class="nav-right">
    <button class="cart-btn" onclick="showCart()">🛒 <span class="cart-badge" id="cart-count">0</span></button>
    <a href="profil_page/profil.php" class="profile-nav">
      <img src="uploads/profile/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Profil" onerror="this.src='uploads/profile/default.png'">
    </a>
  </div>
</header>

<!-- MODAL OVERLAY (cuma 1) -->
<div class="modal-overlay" id="modal-overlay" onclick="closeModal(event)">
  <div class="modal" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="closeModal(null, true)">✕</button>
    <div id="modal-content"></div>
  </div>
</div>

<!-- TOAST (cuma 1) -->
<div class="toast" id="toast"></div>

<!-- ===== HERO ===== -->
<section class="hero">
  <div class="hero-text">
    <span class="badge">Best Prices</span>
    <h1>Incredible Prices<br>on All Your<br>Favorite Hardware</h1>
    <p>Get more for less on selected brands</p>
    <button class="btn">Shop Now</button>
  </div>

  <div class="hero-card">
    <div class="swiper hero-swiper">
      <div class="swiper-wrapper">
        <div class="swiper-slide"><img src="image/I_4.jpg" alt="Slide 1"></div>
        <div class="swiper-slide"><img src="image/I_2.jpg" alt="Slide 2"></div>
        <div class="swiper-slide"><img src="image/I_3.jpg" alt="Slide 3"></div>
      </div>
      <div class="swiper-pagination"></div>
    </div>
  </div>
</section>

<!-- ===== CATEGORY UNGGULAN ===== -->
<section class="category-featured">
  <p class="section-label">Fitur Utama</p>
  <h2 class="section-title">Category Unggulan</h2>

  <div class="featured-grid">
    <div class="featured-card red">
      <div class="feat-icon">🛒</div>
      <h3>Membeli</h3>
      <p>Produk pilihan terbaik untuk kamu</p>
      <a href="#">Cari sekarang →</a>
    </div>
    <div class="featured-card purple">
      <div class="feat-icon">⭐</div>
      <h3>Merekomendasi</h3>
      <p>Produk terlaris minggu ini</p>
      <a href="fitur_unggulan/rekomendasi_video.php">Cari sekarang →</a>
    </div>
    <div class="featured-card blue">
      <div class="feat-icon">🔧</div>
      <h3>Mereparasi</h3> 
      <p>Pilihan favorit pengguna</p>
      <a href="fitur_unggulan/mereparasi.php">Cari sekarang →</a>
    </div>
  </div>
</section>

<!-- ===== PRODUCT SECTION ===== -->
<section class="product-section">
  <div class="section-head">
    <h2>Kategori</h2>
  </div>
  <div class="cats" id="cat-list"></div>

  <div class="divider"></div>

  <div class="section-head">
    <h2 id="section-title">Semua Produk</h2>
    <span class="see-all" id="product-count"></span>
  </div>
  <div class="section-desc" id="section-desc"></div>
  <div class="grid" id="product-grid"></div>
</section>

<footer class="pcn-footer">
  <div class="footer-container">
    <div class="footer-brand">
      <h2>PCNexus</h2>                                          
      <p>Marketplace hardware terpercaya untuk laptop, PC, sparepart, dan aksesoris terbaik.</p>
    </div>
    <div class="footer-links">
      <h3>Navigasi</h3>
      <a href="#">Beranda</a>
      <a href="#">Kategori</a>
      <a href="#">Jual Produk</a>
      <a href="#">Kontak</a>
    </div>
    <div class="footer-links">
      <h3>Layanan</h3>
      <a href="#">Bantuan</a>
      <a href="#">Kebijakan Privasi</a>
      <a href="#">Syarat & Ketentuan</a>
      <a href="#">FAQ</a>
    </div>
    <div class="footer-social">
      <h3>Ikuti Kami</h3>
      <div class="social-icons">
        <a href="#">🌐</a>
        <a href="#">📘</a>
        <a href="#">📸</a>
        <a href="#">🐦</a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    © 2026 PCNexus. All Rights Reserved.
  </div>
</footer>

<style>
  .pcn-footer {
    background: #1a1a2e;
    color: white;
    margin-top: 60px;
    padding-top: 50px;
  }
  .footer-container {
    max-width: 1200px;
    margin: auto;
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
    gap: 30px;
    padding: 0 40px 40px;
  }
  .footer-brand h2 {
    font-family: 'Syne', sans-serif;
    font-size: 28px;
    color: #6c63ff;
    margin-bottom: 12px;
  }
  .footer-brand p {
    color: rgba(255,255,255,.7);
    line-height: 1.6;
    font-size: 14px;
  }
  .footer-links h3,
  .footer-social h3 {
    margin-bottom: 14px;
    font-size: 18px;
  }
  .footer-links a {
    display: block;
    color: rgba(255,255,255,.7);
    text-decoration: none;
    margin-bottom: 10px;
    transition: 0.2s;
  }
  .footer-links a:hover {
    color: #6c63ff;
  }
  .social-icons {
    display: flex;
    gap: 12px;
  }
  .social-icons a {
    width: 40px;
    height: 40px;
    background: #2a2a40;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    text-decoration: none;
    color: white;
    font-size: 18px;
    transition: 0.2s;
  }
  .social-icons a:hover {
    background: #6c63ff;
  }
  .footer-bottom {
    text-align: center;
    padding: 20px;
    border-top: 1px solid rgba(255,255,255,.08);
    font-size: 14px;
    color: rgba(255,255,255,.5);
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="assets/script.js"></script>

<script>
// Inisialisasi Swiper
new Swiper('.hero-swiper', {
  loop: true,
  autoplay: { delay: 3000 },
  pagination: { el: '.swiper-pagination', clickable: true }
});

// Fungsi untuk keranjang (localStorage)
// Fungsi untuk keranjang (localStorage)
function addToCart(id, name, price, icon) {
  // BUGS FIX: Proteksi jika price null/undefined, jadikan string '0'
  let rawPrice = price ? price.toString() : '0';
  let cleanPrice = parseInt(rawPrice.replace(/[^0-9]/g, '')) || 0;

  let cart = localStorage.getItem('cart');
  if (!cart) {
    cart = [];
  } else {
    cart = JSON.parse(cart);
  }
  
  let existing = cart.find(item => item.id == id);
  if (existing) {
    existing.qty += 1;
  } else {
    cart.push({ id: id, name: name, price: cleanPrice, qty: 1, icon: icon });
  }
  
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartCount();
  showToast('✅ ' + name + ' ditambahkan ke keranjang!');
}

function updateCartCount() {
  let cart = localStorage.getItem('cart');
  if (!cart) {
    document.getElementById('cart-count').textContent = '0';
    return;
  }
  cart = JSON.parse(cart);
  let total = cart.reduce((sum, item) => sum + item.qty, 0);
  document.getElementById('cart-count').textContent = total;
}

function showCart() {
  let cart = localStorage.getItem('cart');
  if (!cart || JSON.parse(cart).length === 0) {
    showToast('🛒 Keranjang masih kosong');
    return;
  }
  window.location.href = 'troli.php';
}

function showToast(msg) {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 2500);
}

function closeModal(e, force) {
  if (force || (e && e.target === document.getElementById('modal-overlay'))) {
    document.getElementById('modal-overlay').classList.remove('open');
  }
}

// Update cart count saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
  updateCartCount();
});
</script>

</body>
</html>