<?php
session_start();
$foto = $_SESSION['foto_profil'] ?? 'default.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PCNexus - Marketplace Hardware</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    *{box-sizing:border-box;margin:0;padding:0}
    :root{--brand:#1a1a2e;--accent:#6c63ff;--accent2:#00d4aa;--light:#f8f9fe;--card:#fff;--txt:#1a1a2e;--txt2:#6b7280;--border:#e5e7eb}
    body{font-family:'DM Sans',sans-serif;background:var(--light);color:var(--txt);min-height:100vh}
    .page{display:none}.page.active{display:block}
    .navbar{background:#fff;padding:12px 40px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 1px 3px rgba(0,0,0,0.05);position:sticky;top:0;z-index:100}
    .logo{font-family:'Syne',sans-serif;font-weight:700;font-size:1.2rem;color:var(--brand);letter-spacing:-0.5px;text-decoration:none}
    .search-box{display:flex;align-items:center;background:#f1f5f9;border-radius:40px;padding:0 16px;width:100%;max-width:360px}
    .search-box input{flex:1;border:none;background:transparent;padding:10px 0;font-size:14px;outline:none}
    .search-box button{background:none;border:none;cursor:pointer;color:#94a3b8;font-size:16px}
    .nav-right{display:flex;align-items:center;gap:16px}
    .cart-btn{padding:6px 14px;border-radius:20px;border:1.5px solid var(--accent);cursor:pointer;font-size:13px;font-weight:500;background:transparent;color:var(--accent);display:flex;align-items:center;gap:6px}
    .cart-badge{background:var(--accent);color:#fff;border-radius:50%;width:18px;height:18px;font-size:11px;display:inline-flex;align-items:center;justify-content:center}
    .profile-nav img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #e2e8f0}
    .hero{background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);padding:3rem 1.5rem;display:flex;align-items:center;gap:2rem;overflow:hidden;position:relative}
    .hero::after{content:'';position:absolute;right:-80px;top:-80px;width:300px;height:300px;border-radius:50%;background:rgba(108,99,255,.15);pointer-events:none}
    .hero-txt h1{font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:700;color:#fff;line-height:1.1;margin-bottom:.75rem}
    .hero-txt h1 span{color:var(--accent2)}
    .hero-txt p{color:rgba(255,255,255,.65);font-size:.9rem;margin-bottom:1.25rem;max-width:380px}
    .hero-cta{background:var(--accent);color:#fff;border:none;padding:10px 22px;border-radius:24px;cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:500;font-size:.9rem;display:inline-flex;align-items:center;gap:8px}
    .hero-img{font-size:5rem;opacity:.3}
    .section{padding:1.5rem}
    .section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem}
    .section-head h2{font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:600}
    .see-all{font-size:.8rem;color:var(--accent);cursor:pointer;text-decoration:none;font-weight:500}
    .cats{display:flex;gap:8px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none}
    .cat-chip{padding:8px 16px;border-radius:20px;border:1.5px solid var(--border);cursor:pointer;font-size:.8rem;font-weight:500;white-space:nowrap;background:#fff;color:var(--txt2);transition:all .2s;display:flex;align-items:center;gap:6px}
    .cat-chip.active,.cat-chip:hover{border-color:var(--accent);background:#f0f0ff;color:var(--accent)}
    .cat-icon{font-size:14px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px}
    .card{background:#fff;border-radius:14px;border:1px solid var(--border);overflow:hidden;cursor:pointer;transition:all .2s}
    .card:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.08);border-color:var(--accent)}
    .card-img{width:100%;height:120px;background:#f0f0f8;display:flex;align-items:center;justify-content:center;font-size:2.5rem;position:relative}
    .badge-used{position:absolute;top:8px;left:8px;background:#fff3cd;color:#856404;font-size:10px;padding:2px 7px;border-radius:8px;font-weight:500}
    .badge-new{position:absolute;top:8px;left:8px;background:#d1fae5;color:#065f46;font-size:10px;padding:2px 7px;border-radius:8px;font-weight:500}
    .card-body{padding:.75rem}
    .card-brand{font-size:.7rem;color:var(--txt2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px}
    .card-name{font-size:.85rem;font-weight:500;line-height:1.3;margin-bottom:.5rem;color:var(--txt)}
    .card-price{font-family:'Syne',sans-serif;font-size:1rem;font-weight:600;color:var(--accent)}
    .card-bottom{display:flex;align-items:center;justify-content:space-between;margin-top:.5rem}
    .rating{font-size:.7rem;color:var(--txt2);display:flex;align-items:center;gap:3px}
    .star{color:#f59e0b;font-size:11px}
    .add-btn{width:26px;height:26px;border-radius:50%;background:var(--accent);border:none;cursor:pointer;color:#fff;font-size:16px;display:flex;align-items:center;justify-content:center;transition:transform .15s}
    .add-btn:active{transform:scale(.9)}
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;align-items:flex-end}
    .modal-overlay.open{display:flex}
    .modal{background:#fff;border-radius:20px 20px 0 0;width:100%;max-height:90vh;overflow-y:auto;padding:1.5rem;animation:slideUp .3s ease}
    @keyframes slideUp{from{transform:translateY(100%)}to{transform:translateY(0)}}
    .modal-close{float:right;background:var(--border);border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;color:var(--txt2)}
    .modal-img{width:100%;height:180px;background:#f0f0f8;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:4rem;margin:1rem 0}
    .modal-brand{font-size:.75rem;color:var(--txt2);text-transform:uppercase;letter-spacing:.5px}
    .modal-name{font-family:'Syne',sans-serif;font-size:1.3rem;font-weight:700;margin:.25rem 0 .5rem}
    .modal-price{font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:700;color:var(--accent);margin-bottom:1rem}
    .spec-row{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem}
    .spec-tag{background:#f0f0ff;color:var(--accent);font-size:.75rem;padding:4px 10px;border-radius:8px;font-weight:500}
    .seller-box{background:#f8f9fe;border-radius:12px;padding:.75rem;display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem}
    .seller-avatar{width:36px;height:36px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:.8rem}
    .seller-info small{display:block;color:var(--txt2);font-size:.7rem}
    .seller-info b{font-size:.85rem}
    .modal-actions{display:flex;gap:.75rem}
    .btn-primary{flex:1;background:var(--accent);color:#fff;border:none;padding:12px;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:500;font-size:.9rem;transition:opacity .2s}
    .btn-primary:hover{opacity:.9}
    .btn-sec{flex:1;background:#fff;color:var(--accent);border:2px solid var(--accent);padding:12px;border-radius:12px;cursor:pointer;font-family:'DM Sans',sans-serif;font-weight:500;font-size:.9rem}
    .options-row{display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap}
    .opt-btn{padding:6px 14px;border-radius:8px;border:1.5px solid var(--border);cursor:pointer;font-size:.8rem;background:#fff;color:var(--txt);transition:all .2s}
    .opt-btn.selected,.opt-btn:hover{border-color:var(--accent);background:#f0f0ff;color:var(--accent)}
    .seller-page{padding:1.5rem;max-width:600px;margin:0 auto}
    .seller-hero{background:linear-gradient(135deg,#1a1a2e,#0f3460);border-radius:16px;padding:1.5rem;color:#fff;margin-bottom:1.5rem;text-align:center}
    .seller-hero h2{font-family:'Syne',sans-serif;font-size:1.4rem;margin-bottom:.25rem}
    .seller-hero p{opacity:.7;font-size:.85rem}
    .form-group{margin-bottom:1.25rem}
    .form-label{display:block;font-size:.85rem;font-weight:500;margin-bottom:.4rem;color:var(--txt)}
    .form-input,.form-select,.form-textarea{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;background:#fff;color:var(--txt);outline:none;transition:border .2s}
    .form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--accent)}
    .form-textarea{resize:vertical;min-height:80px}
    .upload-area{border:2px dashed var(--border);border-radius:12px;padding:2rem;text-align:center;cursor:pointer;transition:all .2s}
    .upload-area:hover{border-color:var(--accent);background:#f0f0ff}
    .upload-icon{font-size:2rem;margin-bottom:.5rem}
    .upload-txt{color:var(--txt2);font-size:.85rem}
    .submit-btn{width:100%;background:var(--accent);color:#fff;border:none;padding:14px;border-radius:12px;cursor:pointer;font-family:'Syne',sans-serif;font-weight:600;font-size:1rem;margin-top:.5rem;transition:opacity .2s}
    .submit-btn:hover{opacity:.9}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:.75rem}
    .toast{display:none;position:fixed;bottom:80px;left:50%;transform:translateX(-50%);background:#1a1a2e;color:#fff;padding:10px 20px;border-radius:20px;font-size:.85rem;z-index:300;white-space:nowrap}
    .toast.show{display:block;animation:fadeInToast .3s}
    @keyframes fadeInToast{from{opacity:0;transform:translateX(-50%) translateY(10px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}
    .empty-cat{color:var(--txt2);text-align:center;padding:2rem;font-size:.9rem}
    .section-desc{font-size:.8rem;color:var(--txt2);margin-bottom:1rem}
    .divider{height:1px;background:var(--border);margin:0 1.5rem}
</style>
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="logo">PCNexus</a>
  <div class="search-box">
    <input type="text" placeholder="Cari produk...">
    <button type="button"><i class="fas fa-search"></i></button>
  </div>
  <div class="nav-right">
    <button class="cart-btn" onclick="showCart()">🛒 <span class="cart-badge" id="cart-count">0</span></button>
    <a href="profil_page/profil.php" class="profile-nav">
      <img src="uploads/profile/<?= htmlspecialchars($foto) ?>" alt="Profil" onerror="this.src='uploads/profile/default.png'">
    </a>
  </div>
</nav>

<div id="page-buyer" class="page active">
  <div class="hero">
    <div class="hero-txt">
      <h1>Hardware<br><span>Terbaik</span><br>Harga Fair</h1>
      <p>Laptop, PC, sparepart & aksesoris dari penjual terpercaya se-Indonesia</p>
      <button class="hero-cta">Belanja Sekarang ↓</button>
    </div>
    <div class="hero-img">🖥️</div>
  </div>

  <div class="section">
    <div class="section-head"><h2>Kategori</h2></div>
    <div class="cats" id="cat-list"></div>
  </div>

  <div class="divider"></div>

  <div class="section">
    <div class="section-head">
      <h2 id="section-title">Semua Produk</h2>
      <span class="see-all" id="product-count"></span>
    </div>
    <div class="section-desc" id="section-desc"></div>
    <div class="grid" id="product-grid"></div>
  </div>
</div>

<div id="page-seller" class="page">
  <div class="seller-page">
    <div class="seller-hero">
      <div style="font-size:2.5rem;margin-bottom:.5rem">📦</div>
      <h2>Portal Penjual</h2>
      <p>Upload produk kamu dan mulai berjualan di PCNexus</p>
    </div>

    <div class="form-group">
      <label class="form-label">Kategori Produk</label>
      <select class="form-select" id="s-cat">
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
      <label class="form-label">Merek</label>
      <input class="form-input" id="s-brand" placeholder="Contoh: Lenovo, ASUS, MSI...">
    </div>

    <div class="form-group">
      <label class="form-label">Nama Produk</label>
      <input class="form-input" id="s-name" placeholder="Contoh: ThinkPad T495 Ryzen 7">
    </div>

    <div class="form-group">
      <label class="form-label">Harga (Rp)</label>
      <input class="form-input" id="s-price" type="number" placeholder="3000000">
    </div>

    <div class="form-row">
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Kondisi</label>
        <select class="form-select" id="s-cond">
          <option value="Baru">Baru</option>
          <option value="Bekas">Bekas</option>
          <option value="Refurbished">Refurbished</option>
        </select>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label class="form-label">Stok</label>
        <input class="form-input" id="s-stock" type="number" placeholder="1" value="1">
      </div>
    </div>

    <div class="form-group" style="margin-top:1.25rem">
      <label class="form-label">Spesifikasi / Deskripsi</label>
      <textarea class="form-textarea" id="s-desc" placeholder="Contoh: Processor i5-2450M, RAM 8GB DDR3, SSD 128GB, VGA Nvidia GT520..."></textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Foto Produk</label>
      <div class="upload-area" onclick="handleUpload()">
        <div class="upload-icon">📸</div>
        <div class="upload-txt" id="upload-txt">Klik untuk upload foto produk</div>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Nama Toko</label>
      <input class="form-input" id="s-shop" placeholder="Nama toko kamu">
    </div>

    <button class="submit-btn" onclick="submitProduct()">🚀 Upload Produk</button>
  </div>
</div>

<div class="modal-overlay" id="modal-overlay" onclick="closeModal(event)">
  <div class="modal" id="modal">
    <button class="modal-close" onclick="closeModal(null, true)">✕</button>
    <div id="modal-content"></div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
// ================================================================
// PCNEXUS - troli.php (front-end)
// Keranjang sekarang terhubung ke backend PHP via AJAX
// Handler: fungsi/keranjang_handler.php
// ================================================================

const CART_URL    = 'fungsi/keranjang_handler.php';
const PRODUK_URL  = 'fitur_unggulan/merekomendasi.php'; // halaman produk

// ── Kategori & produk (data publik dari DB — akan di-fetch) ──────
const cats = [
  {id:'all',      label:'Semua',      icon:'🏪'},
  {id:'laptop',   label:'Laptop',     icon:'💻'},
  {id:'pc',       label:'PC Desktop', icon:'🖥️'},
  {id:'sparepart',label:'Sparepart',  icon:'🔧'},
  {id:'vga',      label:'VGA / GPU',  icon:'🎮'},
  {id:'monitor',  label:'Monitor',    icon:'🖥'},
  {id:'aksesoris',label:'Aksesoris',  icon:'🖱️'},
  {id:'storage',  label:'Storage',    icon:'💾'},
  {id:'networking',label:'Networking',icon:'📡'}
];

const catIcons = {laptop:'💻',pc:'🖥️',sparepart:'🔧',vga:'🎮',monitor:'🖥',aksesoris:'🖱️',storage:'💾',networking:'📡'};

let products   = [];   // diisi dari DB
let activeCat  = 'all';
let cartCount  = 0;

// ── Format rupiah ────────────────────────────────────────────────
function fmt(n) { return 'Rp' + parseInt(n).toLocaleString('id-ID'); }

// ── Load produk dari DB via PHP (tabel produk) ───────────────────
async function loadProducts() {
  try {
    const res  = await fetch('fungsi/get_produk_publik.php');
    const data = await res.json();
    if (data.success) {
      products = data.produk;
    }
  } catch(e) {
    // Jika endpoint belum ada / gagal, fallback ke data demo
    products = [];
  }
  buildCats();
  renderProducts();
}

// ── Kategori chips ───────────────────────────────────────────────
function buildCats() {
  document.getElementById('cat-list').innerHTML = cats.map(c =>
    `<div class="cat-chip ${c.id === activeCat ? 'active' : ''}" onclick="filterCat('${c.id}')">
      <span class="cat-icon">${c.icon}</span>${c.label}
    </div>`
  ).join('');
}

function filterCat(id) {
  activeCat = id;
  buildCats();
  renderProducts();
}

// ── Render grid produk ───────────────────────────────────────────
function renderProducts() {
  const filtered = activeCat === 'all' ? products : products.filter(p => p.kategori === activeCat);
  const cat      = cats.find(c => c.id === activeCat);
  document.getElementById('section-title').textContent   = cat ? cat.label + ' ' + cat.icon : 'Semua Produk';
  document.getElementById('product-count').textContent   = filtered.length + ' produk';
  const grid = document.getElementById('product-grid');

  if (!filtered.length) {
    grid.innerHTML = '<div class="empty-cat">Belum ada produk di kategori ini</div>';
    return;
  }

  grid.innerHTML = filtered.map(p => `
    <div class="card" onclick="openProduct(${p.id})">
      <div class="card-img">
        ${p.foto ? `<img src="uploads/produk/${p.foto}" style="width:100%;height:100%;object-fit:cover;">` : (catIcons[p.kategori] || '📦')}
        <div class="${p.kondisi === 'Baru' ? 'badge-new' : 'badge-used'}">${p.kondisi}</div>
      </div>
      <div class="card-body">
        <div class="card-brand">${escHtml(p.merek)}</div>
        <div class="card-name">${escHtml(p.nama_produk)}</div>
        <div class="card-price">${fmt(p.harga)}</div>
        <div class="card-bottom">
          <div class="rating"><span class="star">★</span>${parseFloat(p.rating||5).toFixed(1)} · ${p.sold||0} terjual</div>
          <button class="add-btn" onclick="event.stopPropagation(); addToCart(${p.id})">+</button>
        </div>
      </div>
    </div>`).join('');
}

// ── Buka modal detail produk ──────────────────────────────────────
function openProduct(id) {
  const p = products.find(x => x.id == id);
  if (!p) return;
  document.getElementById('modal-content').innerHTML = `
    <div class="modal-img">${p.foto ? `<img src="uploads/produk/${p.foto}" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">` : (catIcons[p.kategori]||'📦')}</div>
    <div class="modal-brand">${escHtml(p.merek)}</div>
    <div class="modal-name">${escHtml(p.nama_produk)}</div>
    <div class="modal-price">${fmt(p.harga)}</div>
    <div class="spec-row">
      <span class="spec-tag">${escHtml(p.kondisi)}</span>
      <span class="spec-tag">Stok: ${p.stok}</span>
      <span class="spec-tag">${p.kategori}</span>
    </div>
    <p style="font-size:.85rem;color:var(--txt2);margin-bottom:1rem;line-height:1.5">${escHtml(p.deskripsi||'')}</p>
    <div class="seller-box">
      <div class="seller-avatar">${(p.nama_toko||'??').slice(0,2).toUpperCase()}</div>
      <div class="seller-info">
        <b>${escHtml(p.nama_toko||'Penjual')}</b>
        <small>★ ${parseFloat(p.rating||5).toFixed(1)} · ${p.sold||0} terjual</small>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn-sec" onclick="addToCart(${p.id}); closeModal(null,true)">+ Keranjang</button>
      <button class="btn-primary" onclick="buyNow(${p.id})">Beli Langsung</button>
    </div>`;
  document.getElementById('modal-overlay').classList.add('open');
}

function closeModal(e, force) {
  if (force || (e && e.target === document.getElementById('modal-overlay')))
    document.getElementById('modal-overlay').classList.remove('open');
}

// ── Tambah ke keranjang (AJAX ke PHP) ────────────────────────────
async function addToCart(produkId) {
  const form = new FormData();
  form.append('action', 'add');
  form.append('produk_id', produkId);
  form.append('qty', 1);

  try {
    const res  = await fetch(CART_URL, { method:'POST', body: form });
    const data = await res.json();

    if (data.need_login) {
      showToast('⚠️ Silakan login terlebih dahulu');
      setTimeout(() => window.location.href = 'login.php', 1500);
      return;
    }

    if (data.success) {
      cartCount = data.cart_count || cartCount + 1;
      document.getElementById('cart-count').textContent = cartCount;
      const p = products.find(x => x.id == produkId);
      showToast('✅ ' + (p ? p.nama_produk.slice(0,22) : 'Produk') + ' ditambahkan!');
    } else {
      showToast('❌ ' + (data.message || 'Gagal menambahkan'));
    }
  } catch(e) {
    showToast('❌ Gagal terhubung ke server');
  }
}

// ── Beli langsung ────────────────────────────────────────────────
async function buyNow(produkId) {
  await addToCart(produkId);
  closeModal(null, true);
  showCart();
}

// ── Tampilkan isi keranjang (AJAX) ────────────────────────────────
async function showCart() {
  const res  = await fetch(CART_URL + '?action=get');
  const data = await res.json();

  if (!data.success || !data.items || !data.items.length) {
    showToast('🛒 Keranjang masih kosong');
    return;
  }

  const total = data.total;
  document.getElementById('modal-content').innerHTML = `
    <h3 style="font-family:Syne,sans-serif;font-size:1.1rem;margin-bottom:1rem">🛒 Keranjang Belanja</h3>
    ${data.items.map(c => `
      <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem 0;border-bottom:1px solid var(--border)">
        <div style="font-size:1.5rem">${catIcons[c.kategori]||'📦'}</div>
        <div style="flex:1">
          <div style="font-size:.85rem;font-weight:500">${escHtml(c.nama_produk)}</div>
          <div style="font-size:.75rem;color:var(--txt2)">${fmt(c.harga)} × 
            <button onclick="updateQty(${c.cart_id}, ${c.qty-1})" style="border:none;background:#eee;border-radius:4px;padding:0 6px;cursor:pointer">−</button>
            <b>${c.qty}</b>
            <button onclick="updateQty(${c.cart_id}, ${c.qty+1})" style="border:none;background:#eee;border-radius:4px;padding:0 6px;cursor:pointer">+</button>
          </div>
        </div>
        <div style="font-weight:500;font-size:.85rem">${fmt(c.harga * c.qty)}</div>
        <button onclick="removeItem(${c.cart_id})" style="border:none;background:none;color:#ef4444;cursor:pointer;font-size:1.1rem">🗑</button>
      </div>`).join('')}
    <div style="display:flex;justify-content:space-between;margin-top:1rem;font-family:Syne,sans-serif;font-weight:600;font-size:1rem">
      <span>Total</span>
      <span style="color:var(--accent)" id="cart-total">${fmt(total)}</span>
    </div>
    <button class="btn-primary" style="width:100%;margin-top:1rem" onclick="goCheckout()">Bayar Sekarang →</button>`;
  document.getElementById('modal-overlay').classList.add('open');
}

// ── Update qty item keranjang ─────────────────────────────────────
async function updateQty(cartId, newQty) {
  const form = new FormData();
  form.append('action', 'update');
  form.append('cart_id', cartId);
  form.append('qty', newQty);
  const res  = await fetch(CART_URL, { method:'POST', body: form });
  const data = await res.json();
  if (data.success) {
    cartCount = data.cart_count;
    document.getElementById('cart-count').textContent = cartCount;
    closeModal(null, true);
    showCart(); // refresh tampilan
  }
}

// ── Hapus satu item dari keranjang ────────────────────────────────
async function removeItem(cartId) {
  const form = new FormData();
  form.append('action', 'remove');
  form.append('cart_id', cartId);
  const res  = await fetch(CART_URL, { method:'POST', body: form });
  const data = await res.json();
  if (data.success) {
    cartCount = data.cart_count;
    document.getElementById('cart-count').textContent = cartCount;
    closeModal(null, true);
    if (cartCount > 0) showCart(); else showToast('🛒 Keranjang kosong');
  }
}

// ── Lanjut ke checkout ────────────────────────────────────────────
function goCheckout() {
  window.location.href = 'checkout.php';
}

// ── Load jumlah cart saat halaman load (badge nav) ────────────────
async function loadCartCount() {
  try {
    const res  = await fetch(CART_URL + '?action=count');
    const data = await res.json();
    cartCount  = data.count || 0;
    document.getElementById('cart-count').textContent = cartCount;
  } catch(e) {}
}

// ── Tab buyer / seller ────────────────────────────────────────────
function showPage(p, btn) {
  document.querySelectorAll('.page').forEach(x => x.classList.remove('active'));
  document.querySelectorAll('.nav-tab').forEach(x => x.classList.remove('active'));
  document.getElementById('page-' + p).classList.add('active');
  if (btn) btn.classList.add('active');
}

// ── Upload produk (seller tab) — redirect ke penjual.php ──────────
function submitProduct() {
  // Halaman seller diarahkan ke dashboard penjual
  window.location.href = 'penjual/penjual.php';
}

// ── Toast notifikasi ──────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

// ── Escape HTML buat keamanan ─────────────────────────────────────
function escHtml(str) {
  return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Init ──────────────────────────────────────────────────────────
loadCartCount();
loadProducts();
</script>
</body>
</html>
