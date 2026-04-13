<?php
session_start();
$foto = $_SESSION['foto_profil'] ?? 'default.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - PCNexus</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f5f5f5; color: #1a1a2e; }
    .navbar { background: #fff; padding: 12px 40px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
    .logo { font-size: 22px; font-weight: 800; color: #7c3aed; text-decoration: none; letter-spacing: -0.5px; }
    .search-box { display: flex; align-items: center; background: #f1f5f9; border-radius: 40px; padding: 0 16px; width: 100%; max-width: 360px; }
    .search-box input { flex: 1; border: none; background: transparent; padding: 10px 0; font-size: 14px; outline: none; }
    .search-box button { background: none; border: none; color: #94a3b8; font-size: 16px; cursor: pointer; }
    .nav-right { display: flex; align-items: center; gap: 16px; }
    .profile-nav img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #e2e8f0; }
    .checkout-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 380px; gap: 24px; }
    .checkout-form, .order-summary { background: #fff; border-radius: 20px; padding: 28px; box-shadow: 0 2px 12px rgba(0,0,0,.05); }
    .section-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; padding-bottom: 12px; border-bottom: 2px solid #f0f0f0; }
    .section-title span { background: #7b3fe4; color: #fff; width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; }
    .form-group { margin-bottom: 18px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #333; }
    input, select, textarea { width: 100%; padding: 12px 14px; border: 1.5px solid #e5e5e5; border-radius: 12px; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; transition: all 0.2s; }
    input:focus, select:focus, textarea:focus { outline: none; border-color: #7b3fe4; box-shadow: 0 0 0 3px rgba(123,63,228,.1); }
    .order-items { max-height: 350px; overflow-y: auto; margin-bottom: 20px; }
    .order-item { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
    .order-item-icon { font-size: 32px; }
    .order-item-info { flex: 1; }
    .order-item-name { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
    .order-item-price { font-size: 12px; color: #888; }
    .order-item-qty { font-size: 13px; color: #7b3fe4; font-weight: 500; }
    .order-item-total { font-weight: 700; font-size: 14px; }
    .summary-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 14px; }
    .summary-total { display: flex; justify-content: space-between; padding: 15px 0; border-top: 2px solid #f0f0f0; margin-top: 10px; font-size: 18px; font-weight: 800; color: #7b3fe4; }
    .payment-methods { margin: 20px 0; }
    .payment-option { display: flex; align-items: center; gap: 12px; padding: 12px; border: 1.5px solid #e5e5e5; border-radius: 12px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; }
    .payment-option:hover, .payment-option.selected { border-color: #7b3fe4; background: #f3eeff; }
    .payment-option input { width: 18px; height: 18px; accent-color: #7b3fe4; }
    .payment-option label { flex: 1; margin: 0; cursor: pointer; font-weight: 500; }
    .btn-checkout { width: 100%; background: #7b3fe4; color: #fff; border: none; padding: 14px; border-radius: 14px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s; margin-top: 10px; }
    .btn-checkout:hover { background: #6a34c9; transform: translateY(-1px); }
    .back-link { display: inline-flex; align-items: center; gap: 6px; color: #7b3fe4; text-decoration: none; font-size: 14px; font-weight: 500; margin-bottom: 20px; }
    .modal-confirm { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    .modal-confirm.open { display: flex; }
    .modal-content { background: #fff; border-radius: 24px; padding: 32px; max-width: 400px; text-align: center; animation: slideUp 0.3s ease; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .modal-icon { font-size: 64px; margin-bottom: 16px; }
    .modal-title { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
    .modal-text { color: #666; margin-bottom: 24px; }
    .modal-btn { background: #7b3fe4; color: #fff; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 600; cursor: pointer; }
    .empty-cart { text-align: center; padding: 40px; color: #888; }
    .empty-cart a { color: #7b3fe4; text-decoration: none; font-weight: 600; }
    .address-book { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; margin-bottom: 16px; }
    .address-book-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; font-size: 13px; font-weight: 600; }
    .address-book-note { font-size: 12px; color: #64748b; margin-top: 8px; }
    @media (max-width: 768px) { .checkout-container { grid-template-columns: 1fr; } .navbar { padding: 12px 20px; flex-wrap: wrap; gap: 12px; } .search-box { order: 3; width: 100%; max-width: 100%; } .form-row { grid-template-columns: 1fr; } }
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
    <a href="profil_page/profil.php" class="profile-nav">
      <img src="uploads/profile/<?= htmlspecialchars($foto) ?>" alt="Profil" onerror="this.src='uploads/profile/default.png'">
    </a>
  </div>
</nav>

<div class="checkout-container">
  <div class="checkout-form">
    <a href="javascript:history.back()" class="back-link">← Kembali ke Keranjang</a>
    <div class="section-title"><span>1</span> Informasi Pengiriman</div>
    <div class="address-book">
      <div class="address-book-head">
        <span>Pilih Alamat dari Profil</span>
        <a href="profil_page/profil.php" style="font-size:12px;color:#7b3fe4;text-decoration:none;">Kelola di Profil</a>
      </div>
      <select id="savedAddressSelect">
        <option value="">-- Pilih alamat tersimpan --</option>
      </select>
      <div class="address-book-note">Alamat di profil otomatis muncul di sini, tapi tetap bisa kamu edit manual sebelum checkout.</div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Nama Lengkap</label><input type="text" id="fullname" placeholder="Masukkan nama lengkap"></div>
      <div class="form-group"><label>No. Telepon</label><input type="tel" id="phone" placeholder="08xxxxxxxxxx"></div>
    </div>
    <div class="form-group"><label>Alamat Email</label><input type="email" id="email" placeholder="email@example.com"></div>
    <div class="form-group"><label>Alamat Lengkap</label><textarea id="address" rows="3" placeholder="Jl. Contoh No. 123, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos"></textarea></div>
    <div class="form-row">
      <div class="form-group"><label>Kota/Kabupaten</label><input type="text" id="city" placeholder="Kota" value="Jakarta"></div>
      <div class="form-group"><label>Kode Pos</label><input type="text" id="postcode" placeholder="Kode Pos"></div>
    </div>
    <div class="section-title" style="margin-top:24px"><span>2</span> Metode Pembayaran</div>
    <div class="payment-methods">
      <div class="payment-option selected" data-method="bank"><input type="radio" name="payment" value="bank_transfer" id="bank_transfer" checked><label for="bank_transfer">🏦 Transfer Bank (BCA/Mandiri/BNI/BRI)</label></div>
      <div class="payment-option" data-method="qris"><input type="radio" name="payment" value="qris" id="qris"><label for="qris">📱 QRIS (Scan QR Code)</label></div>
      <div class="payment-option" data-method="cod"><input type="radio" name="payment" value="cod" id="cod"><label for="cod">🚚 COD (Bayar di Tempat)</label></div>
      <div class="payment-option" data-method="card"><input type="radio" name="payment" value="credit_card" id="credit_card"><label for="credit_card">💳 Kartu Kredit/Debit</label></div>
    </div>
  </div>

  <div class="order-summary">
    <div class="section-title" style="border-bottom: none; padding-bottom: 0; margin-bottom: 16px;">🛍️ Ringkasan Belanja</div>
    <div class="order-items" id="order-items"></div>
    <div class="summary-row"><span>Subtotal</span><span id="subtotal">Rp0</span></div>
    <div class="summary-row"><span>Ongkos Kirim</span><span id="shipping">Rp0</span></div>
    <div class="summary-row"><span>Pajak (11%)</span><span id="tax">Rp0</span></div>
    <div class="summary-total"><span>Total Tagihan</span><span id="total">Rp0</span></div>
    <button class="btn-checkout" onclick="processCheckout()">✅ Konfirmasi & Bayar</button>
    <p style="font-size:11px;color:#aaa;text-align:center;margin-top:12px">Dengan melanjutkan, Anda menyetujui Syarat & Ketentuan PCNexus</p>
  </div>
</div>

<div class="modal-confirm" id="modalConfirm">
  <div class="modal-content">
    <div class="modal-icon">🎉</div>
    <div class="modal-title">Pesanan Berhasil!</div>
    <div class="modal-text" id="modalMessage"></div>
    <button class="modal-btn" onclick="closeModalAndRedirect()">Kembali ke Beranda</button>
  </div>
</div>

<script>
function fmt(n) { return 'Rp' + parseInt(n).toLocaleString('id-ID'); }

let checkoutItems = [];

// Ambil dari localStorage dengan proteksi ketat
function getCartFromLocalStorage() {
  const cartData = localStorage.getItem('cart');
  if (!cartData) return [];
  
  try {
    let cart = JSON.parse(cartData);
    if (!Array.isArray(cart) || cart.length === 0) return [];
    
    // Pastikan ulang semua tipe data harga dan qty adalah angka yang valid
    return cart.map(item => {
      let rawPrice = item.price ? item.price.toString() : '0';
      return {
        ...item,
        price: parseInt(rawPrice.replace(/[^0-9]/g, '')) || 0,
        qty: parseInt(item.qty) || 1
      };
    });
  } catch(e) {
    console.error('Error parsing cart:', e);
    return [];
  }
}

async function loadCheckoutData() {
  const checkoutCartRaw = localStorage.getItem('checkoutCart');
  if (checkoutCartRaw) {
    try {
      const parsed = JSON.parse(checkoutCartRaw);
      if (Array.isArray(parsed) && parsed.length > 0) {
        checkoutItems = parsed.map(item => ({
          id: parseInt(item.id || item.produk_id || 0),
          name: item.name || item.nama_produk || 'Produk',
          price: parseInt(item.price || item.harga || 0),
          qty: Math.max(1, parseInt(item.qty || 1)),
          icon: item.icon || '📦'
        })).filter(item => item.id > 0);
        renderOrderSummary();
        return;
      }
    } catch (e) {
      console.error('Error parsing checkoutCart:', e);
    }
  }

  const urlParams = new URLSearchParams(window.location.search);
  const productId = urlParams.get('id');
  const productQty = parseInt(urlParams.get('qty')) || 1;
  const productName = urlParams.get('name');
  const productPrice = urlParams.get('price');
  const productIcon = urlParams.get('icon') || '📦';
  
  if (productId) {
    // 1. Beli Satuan: bawa parameter nama & harga dari URL
    if (productName && productPrice) {
      let cleanPrice = parseInt(productPrice.replace(/[^0-9]/g, '')) || 0;
      checkoutItems = [{
        id: productId,
        name: decodeURIComponent(productName),
        price: cleanPrice,
        qty: productQty,
        icon: decodeURIComponent(productIcon)
      }];
    } else {
      // 2. Fallback: ambil dari server berdasarkan produk_id
      try {
        const res = await fetch('fungsi/keranjang_handler.php?action=get');
        const data = await res.json();
        if (data.success && data.items && data.items.length > 0) {
          const found = data.items.find(x => x.produk_id == productId);
          if (found) {
            checkoutItems = [{
              id: found.produk_id,
              name: found.nama_produk,
              price: parseInt(found.harga),
              qty: parseInt(found.qty),
              icon: '📦'
            }];
          } else {
            checkoutItems = data.items.map(item => ({
              id: item.produk_id,
              name: item.nama_produk,
              price: parseInt(item.harga),
              qty: parseInt(item.qty),
              icon: '📦'
            }));
          }
        }
      } catch(e) {
        console.error('Gagal load cart dari server:', e);
      }
    }
  } else {
    // Beli dari keranjang: fetch dari server (session PHP)
    try {
      const res = await fetch('fungsi/keranjang_handler.php?action=get');
      const data = await res.json();
      if (data.success && data.items && data.items.length > 0) {
        checkoutItems = data.items.map(item => ({
          id: item.produk_id,
          name: item.nama_produk,
          price: parseInt(item.harga),
          qty: parseInt(item.qty),
          icon: '📦'
        }));
      }
    } catch(e) {
      console.error('Gagal load cart dari server:', e);
    }
  }
  
  renderOrderSummary();
}

function calculateShipping(city) {
  if (!city) return 0;
  const c = city.toLowerCase();
  if (c.includes('jakarta') || c.includes('bekasi') || c.includes('depok') || 
      c.includes('tangerang') || c.includes('bogor')) return 20000;
  if (c.includes('bandung') || c.includes('surabaya') || c.includes('semarang')) return 30000;
  return 50000;
}

function calculateTotals() {
  const subtotal = checkoutItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
  const city = document.getElementById('city')?.value || '';
  const shippingCost = calculateShipping(city);
  const tax = Math.round(subtotal * 0.11);
  const total = subtotal + shippingCost + tax;
  return { subtotal, shippingCost, tax, total };
}

function updatePriceDisplay() {
  const { subtotal, shippingCost, tax, total } = calculateTotals();
  document.getElementById('subtotal').innerHTML = fmt(subtotal);
  document.getElementById('shipping').innerHTML = fmt(shippingCost);
  document.getElementById('tax').innerHTML = fmt(tax);
  document.getElementById('total').innerHTML = fmt(total);
}

function renderOrderSummary() {
  const container = document.getElementById('order-items');
  if (!checkoutItems.length) {
    container.innerHTML = '<div class="empty-cart">🛒 Keranjang kosong!<br><br><a href="index.php">← Belanja Sekarang</a></div>';
    updatePriceDisplay();
    return;
  }
  
  container.innerHTML = checkoutItems.map(item => `
    <div class="order-item">
      <div class="order-item-icon">${item.icon || '📦'}</div>
      <div class="order-item-info">
        <div class="order-item-name">${item.name}</div>
        <div class="order-item-price">${fmt(item.price)}</div>
      </div>
      <div class="order-item-qty">x${item.qty}</div>
      <div class="order-item-total">${fmt(item.price * item.qty)}</div>
    </div>
  `).join('');
  updatePriceDisplay();
}

document.getElementById('city')?.addEventListener('input', updatePriceDisplay);

document.querySelectorAll('.payment-option').forEach(opt => {
  opt.addEventListener('click', function() {
    document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
    this.classList.add('selected');
    const radio = this.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
  });
});

function fillShippingForm(data) {
  document.getElementById('fullname').value = data.nama || '';
  document.getElementById('phone').value = data.hp || '';
  document.getElementById('email').value = data.email || '';
  document.getElementById('address').value = data.detail || '';
  document.getElementById('city').value = data.kota || '';
  document.getElementById('postcode').value = data.kode_pos || '';
  updatePriceDisplay();
}

async function loadProfileAddressBook() {
  try {
    const res = await fetch('profil_page/update/address_handler.php');
    const data = await res.json();
    if (!data.success) return;

    if (data.profile) {
      document.getElementById('fullname').value = data.profile.nama || '';
      document.getElementById('email').value = data.profile.email || '';
    }

    const select = document.getElementById('savedAddressSelect');
    const addresses = Array.isArray(data.addresses) ? data.addresses : [];
    addresses.forEach((addr, idx) => {
      const opt = document.createElement('option');
      opt.value = String(idx);
      opt.textContent = `${addr.label || 'Alamat'} - ${addr.kota || '-'}`;
      select.appendChild(opt);
    });

    const utamaIdx = addresses.findIndex(a => a.utama);
    if (utamaIdx >= 0) {
      select.value = String(utamaIdx);
      const a = addresses[utamaIdx];
      fillShippingForm({
        nama: a.nama || data.profile?.nama,
        hp: a.hp,
        email: data.profile?.email,
        detail: a.detail,
        kota: a.kota,
        kode_pos: a.kode_pos
      });
    }

    select.addEventListener('change', function() {
      const idx = parseInt(this.value, 10);
      if (Number.isNaN(idx) || !addresses[idx]) return;
      const a = addresses[idx];
      fillShippingForm({
        nama: a.nama || data.profile?.nama,
        hp: a.hp,
        email: data.profile?.email,
        detail: a.detail,
        kota: a.kota,
        kode_pos: a.kode_pos
      });
    });
  } catch (e) {
    console.error('Gagal load alamat profil:', e);
  }
}

function processCheckout() {
  const nama = document.getElementById('fullname').value.trim();
  const hp = document.getElementById('phone').value.trim();
  const email = document.getElementById('email').value.trim();
  const alamat = document.getElementById('address').value.trim();
  const kota = document.getElementById('city').value.trim();
  const kodePos = document.getElementById('postcode').value.trim();
  
  if (!nama) { alert('❌ Isi nama lengkap'); return; }
  if (!hp) { alert('❌ Isi nomor telepon'); return; }
  if (!alamat) { alert('❌ Isi alamat'); return; }
  if (!checkoutItems.length) { alert('❌ Keranjang kosong'); return; }
  
  const payment = document.querySelector('input[name="payment"]:checked');
  const metode = payment ? payment.value : 'bank_transfer';
  
  const formData = new FormData();
  formData.append('action', 'checkout');
  formData.append('nama_lengkap', nama);
  formData.append('no_hp', hp);
  formData.append('email', email);
  formData.append('alamat', alamat);
  formData.append('kota', kota);
  formData.append('kode_pos', kodePos);
  formData.append('metode_bayar', metode);
  formData.append('items', JSON.stringify(checkoutItems));
  
  const btn = document.querySelector('.btn-checkout');
  btn.disabled = true;
  btn.textContent = '⏳ Memproses...';
  
  fetch('fungsi/checkout_handler.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const { total } = calculateTotals();
      const payText = { bank_transfer:'Transfer Bank', qris:'QRIS', cod:'COD', credit_card:'Kartu Kredit' }[metode] || metode;
      document.getElementById('modalMessage').innerHTML = 
        `<strong>${data.order_code}</strong><br>Total: ${fmt(total)}<br>Metode: ${payText}`;
      document.getElementById('modalConfirm').classList.add('open');
      // Hapus cart dari server (session PHP)
      fetch('fungsi/keranjang_handler.php', { method: 'POST', body: (() => { const f = new FormData(); f.append('action', 'clear'); return f; })() });
      localStorage.removeItem('cart');
      localStorage.removeItem('checkoutCart');
    } else {
      alert('❌ ' + (data.message || 'Terjadi kesalahan'));
      btn.disabled = false;
      btn.textContent = '✅ Konfirmasi & Bayar';
    }
  })
  .catch(err => {
    alert('❌ Gagal terhubung ke server: ' + err.message);
    btn.disabled = false;
    btn.textContent = '✅ Konfirmasi & Bayar';
  });
}

function closeModalAndRedirect() {
  document.getElementById('modalConfirm').classList.remove('open');
  window.location.href = 'index.php';
}

// Jalankan fungsi saat halaman dimuat
loadCheckoutData();
loadProfileAddressBook();
</script>
</body>
</html>