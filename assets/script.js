// ===== SWIPER HERO =====
document.addEventListener('DOMContentLoaded', function () {

  // Init Swiper
  if (typeof Swiper !== 'undefined' && document.querySelector('.hero-swiper')) {
    new Swiper('.hero-swiper', {
      loop: true,
      speed: 800,
      autoplay: { delay: 3000, disableOnInteraction: false },
      pagination: { el: '.swiper-pagination', clickable: true },
      navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    });
  }

  // ===== DATA KATEGORI =====
  const cats = [
    { id: 'all',        label: 'Semua',      icon: '🏪' },
    { id: 'laptop',     label: 'Laptop',     icon: '💻' },
    { id: 'pc',         label: 'PC Desktop', icon: '🖥️' },
    { id: 'sparepart',  label: 'Sparepart',  icon: '🔧' },
    { id: 'vga',        label: 'VGA / GPU',  icon: '🎮' },
    { id: 'monitor',    label: 'Monitor',    icon: '🖥'  },
    { id: 'aksesoris',  label: 'Aksesoris',  icon: '🖱️' },
    { id: 'storage',    label: 'Storage',    icon: '💾' },
    { id: 'networking', label: 'Networking', icon: '📡' },
  ];

  // ===== DATA PRODUK =====
  let products = [
    { id:1,  cat:'laptop',     brand:'Lenovo',       name:'ThinkPad T495 Ryzen 7 Pro',     price:3000000,  cond:'Bekas', specs:['Ryzen 7','RAM 8GB','SSD 512GB','14" FHD'],        seller:'Gugel Laptop',    rating:5.0, sold:750, icon:'💻', desc:'Laptop bisnis premium bekas dengan performa tinggi.' },
    { id:2,  cat:'laptop',     brand:'ASUS',         name:'Gaming X441U Core i5',          price:2200000,  cond:'Bekas', specs:['Core i5','RAM 8GB','HDD 1TB','GT920MX'],           seller:'YOGS COMP',       rating:5.0, sold:14,  icon:'💻', desc:'Laptop gaming entry level.' },
    { id:3,  cat:'laptop',     brand:'Lenovo',       name:'ThinkPad T430 i5',              price:1550000,  cond:'Bekas', specs:['Core i5','RAM 8GB','SSD 256GB','Intel HD'],        seller:'ASIATech',        rating:4.8, sold:4,   icon:'💻', desc:'ThinkPad T430 klasik.' },
    { id:4,  cat:'laptop',     brand:'MSI',          name:'Modern 14 C5M Ryzen 5',         price:4000000,  cond:'Bekas', specs:['Ryzen 5','RAM 8GB','SSD 512GB','Radeon'],          seller:'wkwk.store',      rating:5.0, sold:100, icon:'💻', desc:'Laptop modern tipis.' },
    { id:5,  cat:'laptop',     brand:'Lenovo',       name:'ThinkPad E14 Core i5',          price:1200000,  cond:'Bekas', specs:['Core i5','RAM 4GB','HDD 500GB','Intel HD'],        seller:'PT Rahayu',       rating:4.5, sold:3,   icon:'💻', desc:'ThinkPad seri bisnis.' },
    { id:6,  cat:'vga',        brand:'ASUS TUF',     name:'GeForce RTX 5090 OC',           price:32000000, cond:'Baru',  specs:['RTX 5090','32GB GDDR7','PCIe 5.0','DLSS 4'],       seller:'PCNexus Official', rating:5.0, sold:12,  icon:'🎮', desc:'Kartu grafis flagship.' },
    { id:7,  cat:'sparepart',  brand:'Kingston',     name:'RAM DDR4 16GB 3200MHz',         price:350000,   cond:'Baru',  specs:['DDR4','16GB','3200MHz','CL22'],                    seller:'RAM Store ID',    rating:4.9, sold:320, icon:'🔧', desc:'RAM upgrade.' },
    { id:8,  cat:'storage',    brand:'Samsung',      name:'SSD 970 EVO 500GB NVMe',        price:750000,   cond:'Baru',  specs:['NVMe','500GB','3500MB/s','M.2'],                   seller:'Storage Hub',     rating:5.0, sold:88,  icon:'💾', desc:'SSD NVMe kencang.' },
    { id:9,  cat:'monitor',    brand:'LG',           name:'Monitor 24" IPS FHD 75Hz',      price:1800000,  cond:'Baru',  specs:['24"','1080p','75Hz','IPS'],                        seller:'Monitor Zone',    rating:4.8, sold:45,  icon:'🖥', desc:'Monitor IPS full HD.' },
    { id:10, cat:'pc',         brand:'Custom Build', name:'PC Gaming Ryzen 5 + GTX 1660',  price:6500000,  cond:'Baru',  specs:['Ryzen 5 5600','16GB DDR4','GTX 1660S','SSD 256GB'], seller:'Build PC Pro',   rating:4.9, sold:22,  icon:'🖥️', desc:'PC gaming rakitan.' },
    { id:11, cat:'aksesoris',  brand:'Logitech',     name:'Mouse Gaming G502 Hero',        price:450000,   cond:'Baru',  specs:['HERO 25K','11 Tombol','RGB','USB'],                seller:'Aksesori Corner', rating:4.9, sold:200, icon:'🖱️', desc:'Mouse gaming ergonomis.' },
    { id:12, cat:'networking', brand:'TP-Link',      name:'Router WiFi 6 AX3000',          price:650000,   cond:'Baru',  specs:['WiFi 6','AX3000','4 Antena','MU-MIMO'],            seller:'Network Store',   rating:4.7, sold:67,  icon:'📡', desc:'Router WiFi 6.' },
  ];

  async function loadProductsFromDB() {
    try {
      const res = await fetch('fungsi/get_produk_publik.php');
      const data = await res.json();
      if (!data.success || !Array.isArray(data.produk)) return;

      products = data.produk.map((p) => {
        const cat = p.kategori || 'sparepart';
        const iconMap = { laptop:'💻', pc:'🖥️', sparepart:'🔧', vga:'🎮', monitor:'🖥', aksesoris:'🖱️', storage:'💾', networking:'📡' };
        return {
          id: parseInt(p.id),
          cat,
          brand: p.merek || 'Brand',
          name: p.nama_produk || 'Produk',
          price: parseInt(p.harga || 0),
          cond: p.kondisi || 'Baru',
          specs: [cat, `Stok ${parseInt(p.stok || 0)}`, p.nama_toko || 'PCNexus'].filter(Boolean),
          seller: p.nama_toko || 'Penjual',
          rating: parseFloat(p.rating || 5),
          sold: parseInt(p.sold || 0),
          icon: iconMap[cat] || '📦',
          desc: p.deskripsi || '',
        };
      });
    } catch (e) {
      // Fallback ke data dummy jika endpoint gagal.
    }
  }

  // ===== VARIABEL GLOBAL =====
  let cart = [];
  let activeCat = 'all';
  let selUpload = false;

  // ===== HELPER FUNCTION =====
  function fmt(n) { return 'Rp' + n.toLocaleString('id-ID'); }

  function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
  }

  // Update badge jumlah item
  function updateCartBadge() {
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = cart.reduce((a, b) => a + b.qty, 0);
  }

  // Hitung total dari item yang dicentang saja
  function getCheckedTotal() {
    return cart.reduce((total, item) => {
      if (item.checked) {
        return total + (item.price * item.qty);
      }
      return total;
    }, 0);
  }

  // Hitung jumlah item yang dicentang
  function getCheckedCount() {
    return cart.reduce((count, item) => {
      if (item.checked) {
        return count + item.qty;
      }
      return count;
    }, 0);
  }

  // ===== KATEGORI CHIP =====
  function buildCats() {
    const el = document.getElementById('cat-list');
    if (!el) return;
    el.innerHTML = cats.map(c =>
      `<div class="cat-chip ${c.id === activeCat ? 'active' : ''}" onclick="window.filterCat('${c.id}')">
        <span class="cat-icon">${c.icon}</span>${c.label}
      </div>`
    ).join('');
  }

  window.filterCat = function (id) {
    activeCat = id;
    buildCats();
    renderProducts();
  };

  // ===== RENDER PRODUK =====
  function renderProducts() {
    const filtered = activeCat === 'all' ? products : products.filter(p => p.cat === activeCat);
    const cat = cats.find(c => c.id === activeCat);
    
    const titleEl = document.getElementById('section-title');
    const countEl = document.getElementById('product-count');
    const grid = document.getElementById('product-grid');
    
    if (!grid) return;
    if (titleEl) titleEl.textContent = cat ? cat.label + ' ' + cat.icon : 'Semua Produk';
    if (countEl) countEl.textContent = filtered.length + ' produk';
    
    if (!filtered.length) {
      grid.innerHTML = '<div class="empty-cat">Belum ada produk di kategori ini</div>';
      return;
    }
    
    grid.innerHTML = filtered.map(p => `
      <div class="card" onclick="window.openProduct(${p.id})">
        <div class="card-img">
          ${p.icon}
          <div class="${p.cond === 'Baru' ? 'badge-new' : 'badge-used'}">${p.cond}</div>
        </div>
        <div class="card-body">
          <div class="card-brand">${p.brand}</div>
          <div class="card-name">${p.name}</div>
          <div class="card-price">${fmt(p.price)}</div>
          <div class="card-bottom">
            <div class="rating"><span class="star">★</span>${p.rating} · ${p.sold > 100 ? p.sold + '+' : p.sold} terjual</div>
            <button class="add-btn" onclick="event.stopPropagation(); window.addCart(${p.id})">+</button>
          </div>
        </div>
      </div>
    `).join('');
  }

  // ===== MODAL PRODUK =====
  window.openProduct = function (id) {
    const p = products.find(x => x.id === id);
    if (!p) return;
    const opts = ['Ram 4 HDD 250', 'Ram 8 SSD 128', 'Ram 16 SSD 512'].filter((_, i) => i < (p.cat === 'laptop' ? 3 : 1));
    
    document.getElementById('modal-content').innerHTML = `
      <div class="modal-img">${p.icon}</div>
      <div class="modal-brand">${p.brand}</div>
      <div class="modal-name">${p.name}</div>
      <div class="modal-price">${fmt(p.price)}</div>
      ${p.cat === 'laptop' ? `
        <div style="margin-bottom:.75rem;font-size:.82rem;font-weight:500;color:#6b7280">Pilih konfigurasi:</div>
        <div class="options-row">
          ${opts.map((o, i) => `<div class="opt-btn ${i === 0 ? 'selected' : ''}" onclick="window.selectOpt(this)">${o}</div>`).join('')}
        </div>
      ` : ''}
      <div class="spec-row">${p.specs.map(s => `<span class="spec-tag">${s}</span>`).join('')}</div>
      <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;line-height:1.5">${p.desc || ''}</p>
      <div class="seller-box">
        <div class="seller-avatar">${p.seller.slice(0, 2).toUpperCase()}</div>
        <div class="seller-info">
          <b>${p.seller}</b>
          <small>★ ${p.rating} · ${p.sold} terjual</small>
        </div>
      </div>
      <div class="modal-actions">
        <button class="btn-sec" onclick="window.addCart(${p.id}); window.closeModal(null, true)">+ Keranjang</button>
        <button class="btn-primary" onclick="window.buyNow(${p.id})">Beli Langsung</button>
      </div>
    `;
    document.getElementById('modal-overlay').classList.add('open');
  };

  window.selectOpt = function (el) {
    const row = el.closest('.options-row');
    if (row) {
      row.querySelectorAll('.opt-btn').forEach(b => b.classList.remove('selected'));
    }
    el.classList.add('selected');
  };

  window.closeModal = function (e, force) {
    const overlay = document.getElementById('modal-overlay');
    if (force || (e && e.target === overlay)) {
      overlay.classList.remove('open');
    }
  };

  // ===== FUNGSI KERANJANG DENGAN CHECKLIST =====
  window.addCart = function (id) {
    const p = products.find(x => x.id === id);
    if (!p) return;
    const ex = cart.find(x => x.id === id);
    if (ex) {
      ex.qty++;
    } else {
      cart.push({ 
        ...p, 
        qty: 1, 
        checked: true  // default dicentang
      });
    }
    updateCartBadge();
    showToast('✅ ' + p.name.slice(0, 22) + '... ditambahkan!');
  };

  window.buyNow = function (id) {
    window.addCart(id);
    window.closeModal(null, true);
    showToast('🎉 Lanjut ke pembayaran!');
  };

  // Toggle checklist item
  window.toggleCheckItem = function (id) {
    const item = cart.find(x => x.id === id);
    if (item) {
      item.checked = !item.checked;
      if (document.getElementById('modal-overlay').classList.contains('open')) {
        window.showCart();
      }
    }
  };

  // Toggle semua item
  window.toggleCheckAll = function () {
    const isAnyUnchecked = cart.some(item => !item.checked);
    cart.forEach(item => {
      item.checked = isAnyUnchecked;
    });
    window.showCart();
  };

  // Update quantity item
  window.updateQty = function (id, change) {
    const item = cart.find(x => x.id === id);
    if (item) {
      const newQty = item.qty + change;
      if (newQty <= 0) {
        const index = cart.findIndex(x => x.id === id);
        if (index !== -1) cart.splice(index, 1);
      } else {
        item.qty = newQty;
      }
      updateCartBadge();
      if (document.getElementById('modal-overlay').classList.contains('open')) {
        window.showCart();
      }
    }
  };

  // Hapus item dari keranjang
  window.removeFromCart = function (id) {
    const index = cart.findIndex(x => x.id === id);
    if (index !== -1) {
      const itemName = cart[index].name;
      cart.splice(index, 1);
      updateCartBadge();
      showToast('🗑️ ' + itemName.slice(0, 22) + ' dihapus');
      if (cart.length === 0) {
        window.closeModal(null, true);
      } else if (document.getElementById('modal-overlay').classList.contains('open')) {
        window.showCart();
      }
    }
  };

  // ===== SHOW CART DENGAN SISTEM CHECKLIST =====
  window.showCart = function () {
    console.log('showCart dipanggil, cart length:', cart.length);
    
    if (!cart.length) {
      showToast('🛒 Keranjang masih kosong');
      return;
    }
    
    const checkedTotal = getCheckedTotal();
    const checkedCount = getCheckedCount();
    const allChecked = cart.length > 0 && cart.every(item => item.checked);
    
    const modalContent = document.getElementById('modal-content');
    if (!modalContent) {
      console.error('modal-content tidak ditemukan');
      return;
    }
    
    modalContent.innerHTML = `
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:10px">
        <h3 style="font-family:Syne,sans-serif;font-size:1.1rem">🛒 Keranjang Belanja</h3>
        <button onclick="window.toggleCheckAll()" style="background:#f3eeff;border:none;padding:6px 12px;border-radius:20px;cursor:pointer;color:#7b3fe4;font-size:12px;font-weight:500">
          ${allChecked ? '☐ Batal Pilih Semua' : '✓ Pilih Semua'}
        </button>
      </div>
      
      <div style="max-height:400px;overflow-y:auto">
        ${cart.map(item => `
          <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem 0;border-bottom:1px solid var(--border)">
            <!-- CHECKBOX -->
            <input type="checkbox" 
                   id="check-${item.id}" 
                   ${item.checked ? 'checked' : ''} 
                   onchange="window.toggleCheckItem(${item.id})"
                   style="width:18px;height:18px;cursor:pointer;accent-color:#7b3fe4">
            
            <!-- ICON PRODUK -->
            <div style="font-size:1.8rem">${item.icon}</div>
            
            <!-- INFO PRODUK -->
            <div style="flex:1">
              <div style="font-size:.85rem;font-weight:500">${item.name}</div>
              <div style="font-size:.7rem;color:var(--txt2)">${item.seller}</div>
              <div style="font-size:.75rem;color:#7b3fe4;font-weight:500">${fmt(item.price)}</div>
            </div>
            
            <!-- QUANTITY CONTROL -->
            <div style="display:flex;align-items:center;gap:6px">
              <button onclick="window.updateQty(${item.id}, -1)" 
                      style="width:26px;height:26px;border-radius:50%;border:1px solid var(--border);background:#fff;cursor:pointer;font-size:16px">-</button>
              <span style="min-width:30px;text-align:center;font-weight:500">${item.qty}</span>
              <button onclick="window.updateQty(${item.id}, 1)" 
                      style="width:26px;height:26px;border-radius:50%;border:1px solid var(--border);background:#fff;cursor:pointer;font-size:16px">+</button>
            </div>
            
            <!-- SUBTOTAL ITEM -->
            <div style="min-width:80px;text-align:right">
              <div style="font-weight:600;font-size:.85rem">${fmt(item.price * item.qty)}</div>
            </div>
            
            <!-- HAPUS BUTTON -->
            <button onclick="window.removeFromCart(${item.id})" 
                    style="background:#fee2e2;border:none;border-radius:50%;width:28px;height:28px;cursor:pointer;color:#dc2626;font-size:14px">✕</button>
          </div>
        `).join('')}
      </div>
      
      <!-- RINGKASAN BELANJA -->
      <div style="margin-top:1.5rem;padding-top:1rem;border-top:2px solid var(--border)">
        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
          <span style="color:var(--txt2)">Item dipilih:</span>
          <span style="font-weight:500">${checkedCount} produk</span>
        </div>
        <div style="display:flex;justify-content:space-between;margin-bottom:12px">
          <span style="font-size:1rem;font-weight:600">Total yang harus dibayar:</span>
          <span style="font-size:1.2rem;font-weight:700;color:#7b3fe4">${fmt(checkedTotal)}</span>
        </div>
        
        <div style="display:flex;gap:10px;margin-top:16px">
          <button class="btn-sec" style="flex:1" onclick="window.closeModal(null, true)">Tutup</button>
          <button class="btn-primary" style="flex:2" 
                  onclick="window.checkoutSelected()"
                  ${checkedCount === 0 ? 'disabled style="opacity:0.5;cursor:not-allowed"' : ''}>
            ${checkedCount === 0 ? '⚠️ Pilih produk dulu' : '✅ Bayar (' + checkedCount + ' item)'}
          </button>
        </div>
      </div>
    `;
    
    const overlay = document.getElementById('modal-overlay');
    if (overlay) {
      overlay.classList.add('open');
    }
  };

  // Checkout hanya untuk item yang dicentang
  window.checkoutSelected = function () {
    const selectedItems = cart.filter(item => item.checked);
    
    if (selectedItems.length === 0) {
      showToast('⚠️ Silakan pilih produk yang ingin dibeli terlebih dahulu');
      return;
    }
    
    const total = getCheckedTotal();
    const itemCount = getCheckedCount();
    
    // Tampilkan ringkasan pesanan
    const itemList = selectedItems.map(item => 
      `  - ${item.name} x${item.qty} = ${fmt(item.price * item.qty)}`
    ).join('\n');
    
    // Konfirmasi pembayaran
    if (confirm(`🛒 Ringkasan Pesanan:\n\n${itemList}\n\n────────────────\nTotal: ${fmt(total)}\n\nLanjutkan ke pembayaran?`)) {
      // Hapus item yang sudah dibayar dari keranjang
      cart = cart.filter(item => !item.checked);
      updateCartBadge();
      window.closeModal(null, true);
      showToast(`🎉 Pesanan berhasil! Total ${fmt(total)} akan diproses.`);
    }
  };

  window.checkout = function () {
    const hasChecked = cart.some(item => item.checked);
    if (!hasChecked && cart.length > 0) {
      showToast('⚠️ Silakan centang produk yang ingin dibeli');
      return;
    }
    window.checkoutSelected();
  };

  // ===== UPLOAD PRODUK =====
  window.handleUpload = function () {
    selUpload = !selUpload;
    const txtEl = document.getElementById('upload-txt');
    if (txtEl) {
      txtEl.textContent = selUpload ? '📷 foto_produk.jpg (siap diupload)' : 'Klik untuk upload foto produk';
    }
  };

  window.submitProduct = function () {
    const catEl = document.getElementById('s-cat');
    const brandEl = document.getElementById('s-brand');
    const nameEl = document.getElementById('s-name');
    const priceEl = document.getElementById('s-price');
    const condEl = document.getElementById('s-cond');
    const descEl = document.getElementById('s-desc');
    const shopEl = document.getElementById('s-shop');
    
    const cat = catEl ? catEl.value : '';
    const brand = brandEl ? brandEl.value : '';
    const name = nameEl ? nameEl.value : '';
    const price = parseInt(priceEl ? priceEl.value : 0);
    const cond = condEl ? condEl.value : 'Baru';
    const desc = descEl ? descEl.value : '';
    const shop = shopEl ? shopEl.value : '';
    
    if (!cat || !name || !price || !shop) {
      showToast('⚠️ Lengkapi semua field wajib!');
      return;
    }
    
    const icons = { laptop:'💻', pc:'🖥️', sparepart:'🔧', vga:'🎮', monitor:'🖥', aksesoris:'🖱️', storage:'💾', networking:'📡' };
    
    const newProduct = {
      id: products.length + 1,
      cat, brand: brand || 'Generic', name, price, cond,
      specs: desc.split(',').slice(0, 4).map(s => s.trim()).filter(Boolean),
      seller: shop, rating: 5.0, sold: 0, icon: icons[cat] || '📦', desc
    };
    
    products.unshift(newProduct);
    showToast('🚀 Produk berhasil diupload!');
    
    // Reset form
    if (brandEl) brandEl.value = '';
    if (nameEl) nameEl.value = '';
    if (priceEl) priceEl.value = '';
    if (descEl) descEl.value = '';
    if (shopEl) shopEl.value = '';
    if (catEl) catEl.value = 'laptop';
    if (condEl) condEl.value = 'Baru';
    
    selUpload = false;
    const utxt = document.getElementById('upload-txt');
    if (utxt) utxt.textContent = 'Klik untuk upload foto produk';
    
    setTimeout(() => {
      activeCat = 'all';
      buildCats();
      renderProducts();
    }, 500);
  };

  // ===== INITIALISASI =====
  (async () => {
    await loadProductsFromDB();
    buildCats();
    renderProducts();
  })();

  // ===== FUNGSI UNTUK CHECKOUT LANGSUNG KE HALAMAN =====

// Fungsi untuk menyimpan data keranjang ke localStorage dan redirect ke checkout
function redirectToCheckout() {
  // Ambil item yang dicentang
  const selectedItems = cart.filter(item => item.checked === true);
  
  if (selectedItems.length === 0) {
    showToast('⚠️ Silakan pilih produk yang ingin dibeli terlebih dahulu');
    return false;
  }
  
  // Simpan data ke localStorage
  localStorage.setItem('checkoutCart', JSON.stringify(selectedItems));
  
  // Redirect ke halaman checkout
  window.location.href = 'checkout.php';
  return true;
}

// Ganti fungsi checkoutSelected yang lama dengan yang baru (redirect ke halaman checkout)
window.checkoutSelected = function () {
  const selectedItems = cart.filter(item => item.checked === true);
  
  if (selectedItems.length === 0) {
    showToast('⚠️ Silakan pilih produk yang ingin dibeli terlebih dahulu');
    return;
  }
  
  // Simpan ke localStorage
  localStorage.setItem('checkoutCart', JSON.stringify(selectedItems));
  
  // Tutup modal keranjang
  window.closeModal(null, true);
  
  // Tampilkan toast dan redirect
  showToast('🔄 Mengarahkan ke halaman checkout...');
  
  setTimeout(() => {
    window.location.href = 'checkout.php';
  }, 500);
};

// Ganti fungsi buyNow (Beli Langsung) yang lama
window.buyNow = function (id) {
  const p = products.find(x => x.id === id);
  if (!p) return;
  
  // Buat array berisi 1 produk yang dibeli langsung
  const directBuyItem = [{
    ...p,
    qty: 1,
    checked: true
  }];
  
  // Simpan ke localStorage
  localStorage.setItem('checkoutCart', JSON.stringify(directBuyItem));
  
  // Tutup modal jika terbuka
  window.closeModal(null, true);
  
  // Tampilkan notifikasi
  showToast('🔄 Mengarahkan ke halaman checkout...');
  
  // Redirect ke checkout
  setTimeout(() => {
    window.location.href = 'checkout.php';
  }, 500);
};

// Ganti fungsi checkout (tombol Bayar Sekarang di keranjang)
window.checkout = function () {
  const hasChecked = cart.some(item => item.checked === true);
  if (!hasChecked && cart.length > 0) {
    showToast('⚠️ Silakan centang produk yang ingin dibeli');
    return;
  }
  if (cart.length === 0) {
    showToast('🛒 Keranjang masih kosong');
    return;
  }
  
  // Panggil fungsi checkoutSelected
  window.checkoutSelected();
};
}); // end DOMContentLoaded