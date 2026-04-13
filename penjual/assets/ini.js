let cart = [];
let activeCat = 'all';
let selUpload = false;

function fmt(n) { return 'Rp' + n.toLocaleString('id-ID'); }

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

function renderProducts() {
  const filtered = activeCat === 'all' ? products : products.filter(p => p.cat === activeCat);
  const cat = cats.find(c => c.id === activeCat);
  document.getElementById('section-title').textContent = cat ? cat.label + ' ' + cat.icon : 'Semua Produk';
  document.getElementById('product-count').textContent = filtered.length + ' produk';
  const grid = document.getElementById('product-grid');
  if (!filtered.length) {
    grid.innerHTML = '<div class="empty-cat">Belum ada produk di kategori ini</div>';
    return;
  }
  grid.innerHTML = filtered.map(p => `
    <div class="card" onclick="openProduct(${p.id})">
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
          <button class="add-btn" onclick="event.stopPropagation(); addCart(${p.id})">+</button>
        </div>
      </div>
    </div>`).join('');
}

function openProduct(id) {
  const p = products.find(x => x.id === id);
  if (!p) return;
  const opts = ['Ram 4 HDD 250', 'Ram 8 SSD 128', 'Ram 16 SSD 512'].filter((_, i) => i < (p.cat === 'laptop' ? 3 : 1));
  document.getElementById('modal-content').innerHTML = `
    <div class="modal-img">${p.icon}</div>
    <div class="modal-brand">${p.brand}</div>
    <div class="modal-name">${p.name}</div>
    <div class="modal-price">${fmt(p.price)}</div>
    ${p.cat === 'laptop' ? `
      <div style="margin-bottom:.75rem;font-size:.82rem;font-weight:500;color:var(--txt2)">Pilih konfigurasi:</div>
      <div class="options-row">${opts.map((o, i) =>
        `<div class="opt-btn ${i === 0 ? 'selected' : ''}" onclick="selectOpt(this)">${o}</div>`
      ).join('')}</div>` : ''}
    <div class="spec-row">${p.specs.map(s => `<span class="spec-tag">${s}</span>`).join('')}</div>
    <p style="font-size:.85rem;color:var(--txt2);margin-bottom:1rem;line-height:1.5">${p.desc || ''}</p>
    <div class="seller-box">
      <div class="seller-avatar">${p.seller.slice(0, 2).toUpperCase()}</div>
      <div class="seller-info">
        <b>${p.seller}</b>
        <small>★ ${p.rating} · ${p.sold} terjual</small>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn-sec" onclick="addCart(${p.id}); closeModal(null, true)">+ Keranjang</button>
      <button class="btn-primary" onclick="buyNow(${p.id})">Beli Langsung</button>
    </div>`;
  document.getElementById('modal-overlay').classList.add('open');
}

function selectOpt(el) {
  el.closest('.options-row').querySelectorAll('.opt-btn').forEach(b => b.classList.remove('selected'));
  el.classList.add('selected');
}

function closeModal(e, force) {
  if (force || (e && e.target === document.getElementById('modal-overlay')))
    document.getElementById('modal-overlay').classList.remove('open');
}

function addCart(id) {
  const p = products.find(x => x.id === id);
  if (!p) return;
  const ex = cart.find(x => x.id === id);
  if (ex) ex.qty++; else cart.push({ ...p, qty: 1 });
  document.getElementById('cart-count').textContent = cart.reduce((a, b) => a + b.qty, 0);
  showToast('✅ ' + p.name.slice(0, 22) + '... ditambahkan!');
}

function buyNow(id) {
  addCart(id);
  closeModal(null, true);
  showToast('🎉 Lanjut ke pembayaran!');
}

function showCart() {
  if (!cart.length) { showToast('🛒 Keranjang masih kosong'); return; }
  const total = cart.reduce((a, b) => a + b.price * b.qty, 0);
  document.getElementById('modal-content').innerHTML = `
    <h3 style="font-family:Syne,sans-serif;font-size:1.1rem;margin-bottom:1rem">🛒 Keranjang Belanja</h3>
    ${cart.map(c => `
      <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem 0;border-bottom:1px solid var(--border)">
        <div style="font-size:1.5rem">${c.icon}</div>
        <div style="flex:1">
          <div style="font-size:.85rem;font-weight:500">${c.name}</div>
          <div style="font-size:.75rem;color:var(--txt2)">${fmt(c.price)} × ${c.qty}</div>
        </div>
        <div style="font-weight:500;font-size:.85rem">${fmt(c.price * c.qty)}</div>
      </div>`).join('')}
    <div style="display:flex;justify-content:space-between;margin-top:1rem;font-family:Syne,sans-serif;font-weight:600;font-size:1rem">
      <span>Total</span>
      <span style="color:var(--accent)">${fmt(total)}</span>
    </div>
    <button class="btn-primary" style="width:100%;margin-top:1rem" onclick="checkout()">Bayar Sekarang</button>`;
  document.getElementById('modal-overlay').classList.add('open');
}

function checkout() {
  showToast('🎉 Terima kasih! Pesanan diproses.');
  closeModal(null, true);
  cart = [];
  document.getElementById('cart-count').textContent = 0;
}

function showPage(p, btn) {
  document.querySelectorAll('.page').forEach(x => x.classList.remove('active'));
  document.querySelectorAll('.nav-tab').forEach(x => x.classList.remove('active'));
  document.getElementById('page-' + p).classList.add('active');
  if (btn) btn.classList.add('active');
}

function handleUpload() {
  selUpload = !selUpload;
  document.getElementById('upload-txt').textContent = selUpload
    ? '📷 foto_produk.jpg (siap diupload)'
    : 'Klik untuk upload foto produk';
}

function submitProduct() {
  const cat   = document.getElementById('s-cat').value;
  const brand = document.getElementById('s-brand').value;
  const name  = document.getElementById('s-name').value;
  const price = parseInt(document.getElementById('s-price').value);
  const cond  = document.getElementById('s-cond').value;
  const desc  = document.getElementById('s-desc').value;
  const shop  = document.getElementById('s-shop').value;
  if (!cat || !name || !price || !shop) { showToast('⚠️ Lengkapi semua field wajib!'); return; }
  const icons = {laptop:'💻',pc:'🖥️',sparepart:'🔧',vga:'🎮',monitor:'🖥',aksesoris:'🖱️',storage:'💾',networking:'📡'};
  const newP = {
    id: products.length + 1, cat, brand: brand || 'Generic', name, price, cond,
    specs: desc.split(',').slice(0, 4).map(s => s.trim()).filter(Boolean),
    seller: shop, rating: 5.0, sold: 0, icon: icons[cat] || '📦', desc
  };
  products.unshift(newP);
  showToast('🚀 Produk berhasil diupload!');
  document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(el => el.value = '');
  selUpload = false;
  document.getElementById('upload-txt').textContent = 'Klik untuk upload foto produk';
  setTimeout(() => {
    activeCat = 'all';
    buildCats();
    renderProducts();
    showPage('buyer', document.querySelector('.nav-tab'));
  }, 1500);
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

buildCats();
renderProducts();