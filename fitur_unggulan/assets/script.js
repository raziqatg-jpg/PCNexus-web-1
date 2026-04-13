// ════════════════════════════════════════════════════════
//  DATA LAPTOP — tambah/edit di sini sesuai kebutuhan
// ════════════════════════════════════════════════════════
const PLACEHOLDER = "https://placehold.co/400x225/e2e8f0/94a3b8?text=Laptop";

/*
  Format setiap item:
  {
    img      : path gambar (ganti dengan imageR/nama.jpg, dll)
    badge    : label kategori
    title    : judul video / laptop
    channel  : nama channel & jumlah views
    price    : harga laptop
    videoUrl : link YouTube
    parts    : array sparepart — { name, spec, harga, status }
               status: "Wajib" | "Opsional" | "Bonus"
    total    : total estimasi harga upgrade/aksesoris
  }
*/

const data = {

  /* ══════════════ MURAH ══════════════ */
  murah: [
    {
      img: PLACEHOLDER,
      badge: "Murah",
      title: "Acer Aspire 3 A314 — Best Buy Pelajar 2024",
      channel: "Tech Anak Kos • 1.2jt views",
      price: "Rp 3.999.000",
      videoUrl: "https://www.youtube.com/results?search_query=acer+aspire+3+review",
      parts: [
        { name: "RAM",           spec: "8GB DDR4 2666MHz SO-DIMM",       harga: "Rp 150.000",  status: "Wajib"    },
        { name: "SSD Upgrade",   spec: "256GB SATA M.2 2280",            harga: "Rp 280.000",  status: "Wajib"    },
        { name: "Thermal Paste", spec: "Noctua NT-H1 3.5g",              harga: "Rp 80.000",   status: "Wajib"    },
        { name: "Cooling Pad",   spec: "Havit HV-F2056 USB",             harga: "Rp 120.000",  status: "Opsional" },
        { name: "Mouse",         spec: "Logitech M100 Wired",            harga: "Rp 95.000",   status: "Opsional" },
        { name: "Sleeve Bag",    spec: "14 inch Neoprene",               harga: "Rp 75.000",   status: "Opsional" },
        { name: "USB Hub",       spec: "3-Port USB 2.0",                 harga: "Rp 45.000",   status: "Bonus"    },
      ],
      total: "Rp 845.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Murah",
      title: "Lenovo IdeaPad 1 — Laptop Rp 3 Jutaan Worth It?",
      channel: "Budget Gadget ID • 890k views",
      price: "Rp 3.299.000",
      videoUrl: "https://www.youtube.com/results?search_query=lenovo+ideapad+1+review",
      parts: [
        { name: "RAM Upgrade",   spec: "16GB DDR4 3200MHz (2x8GB)",      harga: "Rp 250.000",  status: "Wajib"    },
        { name: "SSD",           spec: "512GB SATA 2.5 inch",            harga: "Rp 390.000",  status: "Wajib"    },
        { name: "Thermal Paste", spec: "Arctic MX-4",                    harga: "Rp 65.000",   status: "Wajib"    },
        { name: "Keyboard Cover",spec: "Silikon Anti Debu 14\"",         harga: "Rp 35.000",   status: "Opsional" },
        { name: "Screen Protector",spec: "Anti Glare 14\"",              harga: "Rp 50.000",   status: "Opsional" },
        { name: "Mouse Wireless",spec: "Fantech W4 2.4GHz",              harga: "Rp 85.000",   status: "Opsional" },
        { name: "HDMI Cable",    spec: "1.5m HDMI 2.0",                  harga: "Rp 40.000",   status: "Bonus"    },
      ],
      total: "Rp 915.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Murah",
      title: "HP 14s — Si Irit Buat Mahasiswa Kurang Uang",
      channel: "Laptop Review ID • 2.1jt views",
      price: "Rp 4.499.000",
      videoUrl: "https://www.youtube.com/results?search_query=hp+14s+review",
      parts: [
        { name: "RAM Upgrade",   spec: "4GB → 8GB DDR4 2666MHz",        harga: "Rp 130.000",  status: "Wajib"    },
        { name: "SSD NVMe",      spec: "128GB M.2 PCIe",                 harga: "Rp 190.000",  status: "Wajib"    },
        { name: "Thermal Paste", spec: "Noctua NT-H2",                   harga: "Rp 90.000",   status: "Wajib"    },
        { name: "Cooling Pad",   spec: "DeepCool N65 RGB",               harga: "Rp 150.000",  status: "Opsional" },
        { name: "USB-C Hub",     spec: "4-in-1 USB-C + HDMI",            harga: "Rp 110.000",  status: "Opsional" },
        { name: "Laptop Stand",  spec: "Aluminium Adjustable",           harga: "Rp 120.000",  status: "Opsional" },
        { name: "Sticker Pack",  spec: "Waterproof Aesthetic",           harga: "Rp 15.000",   status: "Bonus"    },
      ],
      total: "Rp 805.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Murah",
      title: "Asus VivoBook Go 14 — Murah Tapi Gak Murahan",
      channel: "GadgetIn • 3.4jt views",
      price: "Rp 4.999.000",
      videoUrl: "https://www.youtube.com/results?search_query=asus+vivobook+go+14",
      parts: [
        { name: "RAM Upgrade",   spec: "8GB LPDDR5 Onboard (max)",       harga: "Rp 180.000",  status: "Wajib"    },
        { name: "SSD Upgrade",   spec: "512GB NVMe Gen3",                 harga: "Rp 320.000",  status: "Wajib"    },
        { name: "Cooling Pad",   spec: "Generic USB RGB",                 harga: "Rp 75.000",   status: "Opsional" },
        { name: "USB Hub",       spec: "5-in-1 USB-A + USB-C",           harga: "Rp 90.000",   status: "Opsional" },
        { name: "Mouse",         spec: "Rexus M716 Wireless",             harga: "Rp 130.000",  status: "Opsional" },
        { name: "Tas Laptop",    spec: "Casual Backpack 14\"",            harga: "Rp 180.000",  status: "Opsional" },
        { name: "Screen Cleaner",spec: "Microfiber + Spray",              harga: "Rp 25.000",   status: "Bonus"    },
      ],
      total: "Rp 1.000.000"
    },
  ],

  /* ══════════════ BUDGET ══════════════ */
  budget: [
    {
      img: PLACEHOLDER,
      badge: "Budget",
      title: "ASUS ROG Zephyrus G14 2023 — Raja Mid-Range Gaming",
      channel: "Dave2D • 5.1jt views",
      price: "Rp 11.999.000",
      videoUrl: "https://www.youtube.com/results?search_query=asus+rog+zephyrus+g14+review",
      parts: [
        { name: "RAM Upgrade",   spec: "32GB DDR5 4800MHz (2x16GB)",     harga: "Rp 850.000",  status: "Wajib"    },
        { name: "SSD Upgrade",   spec: "1TB NVMe PCIe Gen4",             harga: "Rp 620.000",  status: "Wajib"    },
        { name: "Thermal Paste", spec: "Thermal Grizzly Kryonaut",       harga: "Rp 120.000",  status: "Wajib"    },
        { name: "Laptop Stand",  spec: "Aluminium Foldable Adjustable",  harga: "Rp 200.000",  status: "Opsional" },
        { name: "Keyboard",      spec: "Keychron K3 Wireless Mechanical",harga: "Rp 650.000",  status: "Opsional" },
        { name: "Mouse Gaming",  spec: "Logitech G305 Lightspeed",       harga: "Rp 380.000",  status: "Opsional" },
        { name: "USB-C Hub",     spec: "Anker 7-in-1 100W PD",           harga: "Rp 350.000",  status: "Opsional" },
        { name: "Monitor Ext.",  spec: "24\" FHD 144Hz IPS",             harga: "Rp 1.800.000",status: "Bonus"    },
      ],
      total: "Rp 4.970.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Budget",
      title: "Lenovo LOQ 15 — Gaming Budget Killer 2024",
      channel: "Jarrod's Tech • 4.7jt views",
      price: "Rp 9.499.000",
      videoUrl: "https://www.youtube.com/results?search_query=lenovo+loq+15+review",
      parts: [
        { name: "SSD Upgrade",   spec: "1TB Samsung 970 EVO NVMe Gen4",  harga: "Rp 650.000",  status: "Wajib"    },
        { name: "RAM Upgrade",   spec: "16GB → 32GB DDR5 4800MHz",       harga: "Rp 750.000",  status: "Wajib"    },
        { name: "Thermal Repaste",spec: "Noctua NT-H2 + Pad Tembaga",    harga: "Rp 160.000",  status: "Wajib"    },
        { name: "Cooling Pad",   spec: "Cooler Master Notepal X3",       harga: "Rp 280.000",  status: "Opsional" },
        { name: "Gaming Headset",spec: "HyperX Cloud Core Wired",        harga: "Rp 620.000",  status: "Opsional" },
        { name: "Mousepad XL",   spec: "Fantech Sven MP905 90x40cm",     harga: "Rp 220.000",  status: "Opsional" },
        { name: "Gamepad",       spec: "Xbox Wireless Controller",       harga: "Rp 780.000",  status: "Bonus"    },
      ],
      total: "Rp 3.460.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Budget",
      title: "Acer Nitro V 15 — RTX 4060 Paling Murah?",
      channel: "Notebookcheck • 2.8jt views",
      price: "Rp 10.499.000",
      videoUrl: "https://www.youtube.com/results?search_query=acer+nitro+v+15+rtx+4060",
      parts: [
        { name: "SSD NVMe",      spec: "512GB WD Black SN770",           harga: "Rp 480.000",  status: "Wajib"    },
        { name: "RAM",           spec: "16GB DDR5 SO-DIMM",              harga: "Rp 420.000",  status: "Wajib"    },
        { name: "Thermal Paste", spec: "Thermal Grizzly Aeronaut",       harga: "Rp 150.000",  status: "Wajib"    },
        { name: "Cooler Pad",    spec: "RGB Cooler Master SF19",         harga: "Rp 250.000",  status: "Opsional" },
        { name: "Gaming Mouse",  spec: "Razer DeathAdder Essential",     harga: "Rp 450.000",  status: "Opsional" },
        { name: "Keyboard Cover",spec: "Silikon Nitro 15 Custom Fit",    harga: "Rp 55.000",   status: "Opsional" },
        { name: "Laptop Bag",    spec: "Targus 15.6\" Gaming",           harga: "Rp 380.000",  status: "Opsional" },
        { name: "HDMI 2.1",      spec: "2m 4K 120Hz HDR",               harga: "Rp 85.000",   status: "Bonus"    },
      ],
      total: "Rp 2.270.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Budget",
      title: "MSI Modern 15 — Produktivitas Nonstop Tanpa Drama",
      channel: "Linus Tech Tips • 3.2jt views",
      price: "Rp 8.799.000",
      videoUrl: "https://www.youtube.com/results?search_query=msi+modern+15+review",
      parts: [
        { name: "RAM",           spec: "16GB DDR4 3200MHz SO-DIMM",      harga: "Rp 300.000",  status: "Wajib"    },
        { name: "SSD",           spec: "1TB Crucial MX500 SATA",         harga: "Rp 550.000",  status: "Wajib"    },
        { name: "Thermal Paste", spec: "Arctic Silver 5",                harga: "Rp 70.000",   status: "Wajib"    },
        { name: "Laptop Stand",  spec: "Nexstand K7 Portable",           harga: "Rp 320.000",  status: "Opsional" },
        { name: "USB-C Hub",     spec: "Baseus 8-in-1 (4K HDMI + PD)",  harga: "Rp 280.000",  status: "Opsional" },
        { name: "Mouse Silent",  spec: "Logitech M331 Wireless",         harga: "Rp 250.000",  status: "Opsional" },
        { name: "Privacy Filter",spec: "3M 15.6\" Anti-Spy",             harga: "Rp 195.000",  status: "Bonus"    },
      ],
      total: "Rp 1.965.000"
    },
  ],

  /* ══════════════ SULTAN ══════════════ */
  sultan: [
    {
      img: PLACEHOLDER,
      badge: "Sultan",
      title: "ASUS ROG Zephyrus Duo 16 — Dua Layar Satu Laptop Gila",
      channel: "MrMobile • 6.2jt views",
      price: "Rp 34.999.000",
      videoUrl: "https://www.youtube.com/results?search_query=asus+rog+zephyrus+duo+16",
      parts: [
        { name: "RAM",           spec: "64GB DDR5 4800MHz (2x32GB)",     harga: "Rp 2.400.000",status: "Wajib"    },
        { name: "SSD Slot 1",    spec: "2TB NVMe Gen5 Samsung 990 Pro",  harga: "Rp 1.600.000",status: "Wajib"    },
        { name: "SSD Slot 2",    spec: "2TB NVMe Gen5 WD Black SN850X",  harga: "Rp 1.550.000",status: "Wajib"    },
        { name: "Thermal Repaste",spec: "Liquid Metal Conductonaut",     harga: "Rp 320.000",  status: "Wajib"    },
        { name: "eGPU Dock",     spec: "ASUS ROG XG Mobile (RTX 4090)", harga: "Rp 15.000.000",status: "Opsional"},
        { name: "Thunderbolt Dock",spec: "Caldigit TS4 11-in-1",        harga: "Rp 2.800.000",status: "Opsional" },
        { name: "Cooling Base",  spec: "Cooler Master X3 Vacuum",       harga: "Rp 700.000",  status: "Opsional" },
        { name: "Monitor 4K",    spec: "LG 27\" UltraFine 4K OLED",     harga: "Rp 8.500.000",status: "Bonus"    },
        { name: "Keyboard",      spec: "Keychron Q1 Pro QMK Wireless",  harga: "Rp 2.200.000",status: "Bonus"    },
      ],
      total: "Rp 35.070.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Sultan",
      title: "Apple MacBook Pro 16 M3 Max — The Untouchable Beast",
      channel: "MKBHD • 12.4jt views",
      price: "Rp 45.000.000",
      videoUrl: "https://www.youtube.com/results?search_query=macbook+pro+16+m3+max+review",
      parts: [
        { name: "USB-C Hub",     spec: "Satechi 12-in-1 Thunderbolt 4", harga: "Rp 1.500.000",status: "Wajib"    },
        { name: "MagSafe Cable", spec: "2m Braided USB-C 140W",         harga: "Rp 450.000",  status: "Wajib"    },
        { name: "Apple Care+",   spec: "3 Tahun Garansi Extended",      harga: "Rp 3.500.000",status: "Wajib"    },
        { name: "Studio Display",spec: "Apple Studio Display 27\" 5K",  harga: "Rp 22.000.000",status: "Opsional"},
        { name: "Magic Keyboard",spec: "Touch ID + Num Pad Space Gray", harga: "Rp 2.100.000",status: "Opsional" },
        { name: "Magic Mouse",   spec: "Apple Magic Mouse 3",           harga: "Rp 1.200.000",status: "Opsional" },
        { name: "Sleeve",        spec: "Moshi Slim 16\" Vegan Leather", harga: "Rp 850.000",  status: "Opsional" },
        { name: "NVMe Enclosure",spec: "OWC Envoy Pro FX Thunderbolt",  harga: "Rp 1.800.000",status: "Bonus"    },
      ],
      total: "Rp 33.400.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Sultan",
      title: "Razer Blade 18 RTX 4090 — PC Gaming dalam Laptop",
      channel: "Hardware Unboxed • 4.5jt views",
      price: "Rp 55.000.000",
      videoUrl: "https://www.youtube.com/results?search_query=razer+blade+18+rtx+4090+review",
      parts: [
        { name: "RAM",           spec: "64GB DDR5 6400MHz (2x32GB)",     harga: "Rp 3.200.000",status: "Wajib"    },
        { name: "SSD Slot 1",    spec: "4TB NVMe RAID PCIe Gen5",        harga: "Rp 3.100.000",status: "Wajib"    },
        { name: "SSD Slot 2",    spec: "4TB NVMe RAID PCIe Gen5",        harga: "Rp 3.100.000",status: "Wajib"    },
        { name: "Liquid Metal",  spec: "Thermal Grizzly Conductonaut",   harga: "Rp 380.000",  status: "Wajib"    },
        { name: "Thunderbolt 4", spec: "Caldigit TS4 Dock 98W PD",      harga: "Rp 2.800.000",status: "Opsional" },
        { name: "Monitor",       spec: "Samsung Odyssey Neo G8 32\" 4K", harga: "Rp 12.000.000",status:"Opsional" },
        { name: "Gaming Chair",  spec: "Secretlab Titan Evo 2022",      harga: "Rp 8.500.000",status: "Bonus"    },
        { name: "DAC/Amp",       spec: "Schiit Fulla 4 USB",            harga: "Rp 1.200.000",status: "Bonus"    },
      ],
      total: "Rp 34.280.000"
    },
    {
      img: PLACEHOLDER,
      badge: "Sultan",
      title: "Lenovo ThinkPad X1 Extreme Gen 6 — Workstation Premium",
      channel: "Notebookcheck • 1.9jt views",
      price: "Rp 38.000.000",
      videoUrl: "https://www.youtube.com/results?search_query=thinkpad+x1+extreme+gen+6+review",
      parts: [
        { name: "RAM",           spec: "64GB ECC DDR5 4800MHz",          harga: "Rp 2.800.000",status: "Wajib"    },
        { name: "SSD Primary",   spec: "2TB Samsung 990 Pro NVMe",       harga: "Rp 1.800.000",status: "Wajib"    },
        { name: "SSD Secondary", spec: "2TB WD Black SN850X NVMe",       harga: "Rp 1.650.000",status: "Wajib"    },
        { name: "Thermal Paste", spec: "Thermal Grizzly Kryonaut Xtreme",harga: "Rp 280.000",  status: "Wajib"    },
        { name: "Dock",          spec: "Lenovo ThinkPad TB4 Dock 300W",  harga: "Rp 3.500.000",status: "Opsional" },
        { name: "Monitor",       spec: "LG 32UN880 32\" 4K USB-C Ergo",  harga: "Rp 9.000.000",status: "Opsional" },
        { name: "Keyboard",      spec: "Lenovo TrackPoint Keyboard II",  harga: "Rp 1.100.000",status: "Opsional" },
        { name: "UPS",           spec: "APC Back-UPS BX1000 1000VA",     harga: "Rp 1.500.000",status: "Bonus"    },
        { name: "Laptop Bag",    spec: "Thule Subterra 15.6\" Premium",  harga: "Rp 1.200.000",status: "Bonus"    },
      ],
      total: "Rp 22.830.000"
    },
  ],
};

// ════════════════════════════════════════════════════════
//  BADGE STYLE PER KATEGORI
// ════════════════════════════════════════════════════════
const badgeStyle = {
  murah:  { bg: "#dcfce7", color: "#15803d", btnBg: "#22c55e" },
  budget: { bg: "#dbeafe", color: "#1d4ed8", btnBg: "#3b82f6" },
  sultan: { bg: "#fef3c7", color: "#b45309", btnBg: "#f59e0b" },
};

// ════════════════════════════════════════════════════════
//  BUILD SLIDES
// ════════════════════════════════════════════════════════
function buildSlides(category) {
  const container = document.getElementById(`slides-${category}`);
  const items = data[category];
  const bs = badgeStyle[category];

  items.forEach((item, idx) => {
    const li = document.createElement("li");
    li.className = "swiper-slide";
    li.style.cssText = "list-style:none;";
    li.innerHTML = `
      <a class="card-link" href="javascript:void(0)" data-cat="${category}" data-idx="${idx}">
        <img class="card-image" src="${item.img}" alt="${item.title}">
        <div class="card-body">
          <span class="badge">${item.badge}</span>
          <h2 class="card-title">${item.title}</h2>
          <p class="card-price">Harga: <strong>${item.price}</strong></p>
          <button class="card-cta" style="background:${bs.bg};color:${bs.color};">
            Lihat Detail ↗
          </button>
        </div>
      </a>
    `;
    container.appendChild(li);
  });
}

["murah", "budget", "sultan"].forEach(buildSlides);

// ════════════════════════════════════════════════════════
//  INIT SWIPERS
// ════════════════════════════════════════════════════════
function initSwiper(cat) {
  new Swiper(`.swiper-${cat}`, {
    loop: true,
    spaceBetween: 20,
    slidesPerView: 1,
    breakpoints: {
      480:  { slidesPerView: 1, spaceBetween: 14 },
      640:  { slidesPerView: 2, spaceBetween: 18 },
      1024: { slidesPerView: 3, spaceBetween: 20 },
    },
    pagination: {
      el: `.swiper-pag-${cat}`,
      clickable: true,
    },
    navigation: {
      nextEl: `.swiper-next-${cat}`,
      prevEl: `.swiper-prev-${cat}`,
    },
  });
}

["murah", "budget", "sultan"].forEach(initSwiper);

// ════════════════════════════════════════════════════════
//  MODAL LOGIC
// ════════════════════════════════════════════════════════
const overlay          = document.getElementById("modalOverlay");
const modalTitle       = document.getElementById("modalTitle");
const modalBadge       = document.getElementById("modalBadge");
const modalChannel     = document.getElementById("modalChannel");
const modalThumb       = document.getElementById("modalThumb");
const modalParts       = document.getElementById("modalParts");
const modalTotal       = document.getElementById("modalTotal");
const modalWatchLink   = document.getElementById("modalWatchLink");
const modalWatchBtn    = document.getElementById("modalWatchBtn");

// Map status ke class CSS
const statusClass = {
  "Wajib":    "status-wajib",
  "Opsional": "status-opsional",
  "Bonus":    "status-bonus",
};

function openModal(cat, idx) {
  const item = data[cat][idx];
  const bs   = badgeStyle[cat];

  // Badge
  modalBadge.textContent = item.badge.toUpperCase();
  modalBadge.style.cssText = `background:${bs.bg};color:${bs.color};display:inline-block;padding:4px 12px;border-radius:999px;font-size:0.72rem;font-weight:700;letter-spacing:0.04em;margin-bottom:10px;`;

  // Info atas
  modalTitle.textContent   = item.title;
  modalChannel.textContent = item.channel;
  modalThumb.src           = item.img;

  // Tabel sparepart
  modalParts.innerHTML = item.parts.map((p, i) => `
    <tr>
      <td style="color:#94a3b8;font-size:0.75rem;">${i + 1}</td>
      <td><strong>${p.name}</strong></td>
      <td style="color:#64748b">${p.spec}</td>
      <td><span class="status-badge ${statusClass[p.status] || ''}">${p.status}</span></td>
      <td class="price-cell">${p.harga}</td>
    </tr>
  `).join("");

  // Total & tombol
  modalTotal.textContent = item.total;
  modalWatchLink.href     = item.videoUrl;
  modalWatchBtn.style.cssText = `background:${bs.btnBg};color:#fff;padding:10px 22px;border-radius:10px;border:none;font-size:0.85rem;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;display:inline-flex;align-items:center;gap:7px;text-decoration:none;`;

  overlay.classList.add("active");
  document.body.style.overflow = "hidden";
}

function closeModal() {
  overlay.classList.remove("active");
  document.body.style.overflow = "";
}

// Klik card buka modal
document.addEventListener("click", (e) => {
  const link = e.target.closest(".card-link[data-cat]");
  if (link) {
    e.preventDefault();
    openModal(link.dataset.cat, parseInt(link.dataset.idx));
  }
});

// Tutup modal
document.getElementById("modalCloseX").addEventListener("click", closeModal);
document.getElementById("modalCloseBtn").addEventListener("click", closeModal);
overlay.addEventListener("click", (e) => { if (e.target === overlay) closeModal(); });
document.addEventListener("keydown", (e) => { if (e.key === "Escape") closeModal(); });