<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Laptop Terdekat - PCNexus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@400;500;600;700;800&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f8f9fe;
            color: #1a1a2e;
        }
        
        /* NAVBAR */
        .navbar { background:#fff; padding:12px 40px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 3px rgba(0,0,0,0.05); position:sticky; top:0; z-index:100; gap:20px; }
        .logo { font-size:22px; font-weight:800; color:#7c3aed; text-decoration:none; letter-spacing:-0.5px; white-space:nowrap; font-family:'Inter',sans-serif; }
        .search-box { display:flex; align-items:center; background:#f1f5f9; border-radius:40px; padding:0 16px; width:100%; max-width:360px; }
        .search-box input { flex:1; border:none; background:transparent; padding:10px 0; font-size:14px; outline:none; }
        .search-box button { background:none; border:none; cursor:pointer; color:#94a3b8; font-size:16px; }
        .nav-right { display:flex; align-items:center; gap:16px; }
        .profile-btn { padding:8px 14px; border-radius:20px; border:1.5px solid #7c3aed; background:transparent; color:#7c3aed; text-decoration:none; font-size:13px; font-weight:600; transition:all 0.2s; }
        .profile-btn:hover { background:#7c3aed; color:#fff; }
        
        /* BREADCRUMB */
        .breadcrumb {
            max-width: 1400px;
            margin: 20px auto 0;
            padding: 0 24px;
            font-size: 0.8rem;
            color: #6b7280;
        }
        
        .breadcrumb a { color: #6c63ff; text-decoration: none; font-weight: 600; }
        .breadcrumb-current { color: #1a1a2e; font-weight: 600; }
        .breadcrumb-sep { color: #cbd5e1; margin: 0 4px; }
        
        /* MAIN */
        .main-container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 24px;
        }
        
        .service-app {
            display: flex;
            height: 620px;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        /* SIDEBAR */
        .service-sidebar {
            width: 360px;
            min-width: 360px;
            background: #fff;
            overflow-y: auto;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 20px 16px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }
        
        .sidebar-header h2 {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            font-family: 'Syne', sans-serif;
        }
        
        .sidebar-header p {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 4px;
        }
        
        .location-info {
            padding: 10px 16px;
            background: #f0f0ff;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
            color: #6c63ff;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .location-info .dot {
            width: 8px;
            height: 8px;
            background: #4caf50;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 0.5; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1.2); }
        }
        
        .filter-bar {
            padding: 12px;
            background: #f8f9fe;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #6b7280;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .filter-btn.active,
        .filter-btn:hover {
            background: #6c63ff;
            color: #fff;
            border-color: #6c63ff;
        }
        
        .shop-list {
            padding: 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .shop-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        
        .shop-card:hover {
            border-color: #6c63ff;
            box-shadow: 0 4px 12px rgba(108, 99, 255, 0.1);
            transform: translateY(-1px);
        }
        
        .shop-card.active {
            border-color: #6c63ff;
            background: #f8f9fe;
        }
        
        .shop-card.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #6c63ff;
        }
        
        .shop-rank {
            position: absolute;
            top: 12px;
            right: 12px;
            font-size: 10px;
            font-weight: 700;
            color: #6c63ff;
            background: #f0f0ff;
            border-radius: 8px;
            padding: 2px 8px;
        }
        
        .distance-badge {
            position: absolute;
            bottom: 12px;
            right: 12px;
            font-size: 9px;
            font-weight: 600;
            color: #fff;
            background: #6c63ff;
            border-radius: 12px;
            padding: 2px 8px;
        }
        
        .shop-name {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a2e;
            margin-right: 45px;
            margin-bottom: 6px;
        }
        
        .shop-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
            flex-wrap: wrap;
        }
        
        .stars { color: #f59e0b; font-size: 11px; letter-spacing: 1px; }
        .rating-num { font-size: 11px; font-weight: 700; color: #1a1a2e; }
        .review-count { font-size: 10px; color: #9ca3af; }
        .shop-addr { font-size: 11px; color: #6b7280; margin-top: 6px; line-height: 1.4; }
        .shop-hours { font-size: 10px; margin-top: 6px; display: flex; align-items: center; gap: 8px; }
        .open-badge { background: #e8f5e9; color: #2e7d32; border-radius: 6px; padding: 2px 8px; font-size: 10px; font-weight: 600; }
        .shop-tags { display: flex; gap: 6px; margin-top: 8px; flex-wrap: wrap; }
        .tag { font-size: 9px; padding: 3px 8px; border-radius: 12px; background: #f0f0ff; color: #6c63ff; border: 1px solid #e5e7eb; }
        
        /* MAP */
        .map-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        
        .map-topbar {
            padding: 12px 16px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
        }
        
        .map-title { font-size: 12px; color: #6b7280; }
        
        .map-frame { flex: 1; }
        .map-frame iframe { width: 100%; height: 100%; border: none; }
        
        .bottom-tip {
            padding: 8px 12px;
            background: #f8f9fe;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }
        
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f0f0ff; }
        ::-webkit-scrollbar-thumb { background: #c7c7ff; border-radius: 10px; }
        
        @media (max-width: 768px) {
            .navbar { padding:12px 20px; flex-wrap:wrap; gap:12px; }
            .search-box { order:3; max-width:100%; }
            .service-sidebar { width: 300px; min-width: 300px; }
        }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="../index.php" class="logo">PCNexus</a>
    <div class="search-box">
        <input type="text" placeholder="Cari service / lokasi...">
        <button type="button">🔍</button>
    </div>
    <div class="nav-right">
        <a href="../profil_page/profil.php" class="profile-btn">Profil</a>
    </div>
</nav>

<nav class="breadcrumb">
    <a href="#">Home</a>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Service Laptop Terdekat</span>
</nav>

<div class="main-container">
    <div class="service-app">
        <div class="service-sidebar">
            <div class="sidebar-header">
                <h2>📍 Service Laptop Terdekat</h2>
                <p>Berdasarkan lokasi kamu di Bandung</p>
            </div>
            <div class="location-info">
                <span class="dot"></span>
                <span>📍 Lokasi referensi: <strong>Pusat Bandung (Alun-Alun)</strong></span>
            </div>
            <div class="filter-bar">
                <button class="filter-btn active" data-filter="all">Semua</button>
                <button class="filter-btn" data-filter="rating5">⭐ Rating 5.0</button>
                <button class="filter-btn" data-filter="buka">🌅 Buka 24 Jam</button>
            </div>
            <div class="shop-list" id="shopList"></div>
        </div>

        <div class="map-area">
            <div class="map-topbar">
                <span class="map-title">🗺️ Google Maps · Lokasi terdekat dari pusat Bandung</span>
            </div>
            <div class="map-frame">
                <iframe id="googleMap" 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15844!2d107.6186!3d-6.9175!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e7c8a7b6a8b5%3A0x3e5e5e5e5e5e5e5e!2sAlun-Alun%20Bandung!5e0!3m2!1sid!2sid!4v1680000000000!5m2!1sid!2sid"
                    allowfullscreen loading="lazy">
                </iframe>
            </div>
            <div class="bottom-tip">
                💡 Urutan toko diurutkan dari yang TERDEKAT dengan pusat Bandung
            </div>
        </div>
    </div>
</div>

<script>
// Pusat referensi: Alun-Alun Bandung
const CENTER_LAT = -6.9175;
const CENTER_LNG = 107.6186;

// DATA TOKO SERVICE LAPTOP BANDUNG (dengan koordinat real)
const shopsRaw = [
    {
        id: 1, rank: "#1",
        name: "Services Laptop Bandung Timur",
        addr: "D'Pasirwanggi View Blok B No 6, Bandung",
        rating: 5.0, reviews: 5,
        hours: "Buka 24 jam", openEarly: true,
        phone: "0821-3628-2164",
        tags: ["Servis Komputer", "Laptop", "24 Jam"],
        lat: -6.9100, lng: 107.6250
    },
    {
        id: 2, rank: "#2",
        name: "Teguh Jaya Computer",
        addr: "Gg. Cikoang Kaler No.01, RT.04/RW.3, Bandung",
        rating: 4.8, reviews: 28,
        hours: "09.00 - 18.00", openEarly: false,
        phone: "0821-1163-2335",
        tags: ["Servis Komputer", "Service Cepat"],
        lat: -6.9200, lng: 107.6200
    },
    {
        id: 3, rank: "#3",
        name: "Kharisma Servis Komputer",
        addr: "Jalan Grimekar raya no 39A, Cilengkrang, Bandung",
        rating: 5.0, reviews: 3,
        hours: "08.00 - 20.00", openEarly: false,
        phone: "0852-2072-2262",
        tags: ["Jasa Reparasi", "Komputer", "Laptop"],
        lat: -6.9250, lng: 107.6350
    },
    {
        id: 4, rank: "#4",
        name: "Azka Utama Service",
        addr: "Jl. Cibiru Raya No.123, Bandung",
        rating: 4.9, reviews: 12,
        hours: "09.00 - 19.00", openEarly: false,
        phone: "0852-2072-2262",
        tags: ["Service Laptop", "Sparepart"],
        lat: -6.9350, lng: 107.6400
    },
    {
        id: 5, rank: "#5",
        name: "Mantri Computer",
        addr: "Jl. Alun-Alun Ujung Berung, Bandung",
        rating: 4.7, reviews: 45,
        hours: "08.30 - 20.30", openEarly: false,
        phone: "0821-3628-2164",
        tags: ["Service", "Komputer", "Laptop"],
        lat: -6.9050, lng: 107.6100
    },
    {
        id: 6, rank: "#6",
        name: "Neo Aztec IT Solution",
        addr: "Jl. Cibiru Raya, Bandung",
        rating: 4.8, reviews: 8,
        hours: "09.00 - 18.00", openEarly: false,
        phone: "0821-3300-0690",
        tags: ["IT Solution", "Service", "Laptop"],
        lat: -6.9300, lng: 107.6320
    },
    {
        id: 7, rank: "#7",
        name: "Service Laptop Privasi",
        addr: "Jl. Cibiru Hilir, Bandung",
        rating: 4.9, reviews: 15,
        hours: "10.00 - 20.00", openEarly: false,
        phone: "0852-2072-2262",
        tags: ["Privasi", "Service", "Laptop"],
        lat: -6.9280, lng: 107.6280
    }
];

// Fungsi hitung jarak (Haversine formula)
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius bumi dalam km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Hitung jarak dari pusat ke setiap toko
let shops = shopsRaw.map(shop => ({
    ...shop,
    distance: calculateDistance(CENTER_LAT, CENTER_LNG, shop.lat, shop.lng),
    distanceKm: calculateDistance(CENTER_LAT, CENTER_LNG, shop.lat, shop.lng).toFixed(1)
}));

// Urutkan berdasarkan jarak terdekat
shops.sort((a, b) => a.distance - b.distance);

// Update rank berdasarkan urutan jarak
shops.forEach((shop, index) => {
    shop.rank = `#${index + 1}`;
    shop.originalId = shop.id;
    shop.id = index + 1;
});

let activeId = 1;
let currentFilter = 'all';

function starsHtml(rating) {
    const full = Math.floor(rating);
    const half = rating % 1 >= 0.5 ? 1 : 0;
    const empty = 5 - full - half;
    return '★'.repeat(full) + (half ? '½' : '') + '☆'.repeat(empty);
}

function formatDistance(dist) {
    if (dist < 1) return `${(dist * 1000).toFixed(0)} meter`;
    return `${dist.toFixed(1)} km`;
}

function renderCards(list) {
    const container = document.getElementById('shopList');
    container.innerHTML = list.map(shop => `
        <div class="shop-card ${activeId === shop.id ? 'active' : ''}" onclick="selectShop(${shop.id})">
            <span class="shop-rank">${shop.rank}</span>
            <span class="distance-badge">📍 ${formatDistance(shop.distance)}</span>
            <div class="shop-name">${shop.name}</div>
            <div class="shop-meta">
                <span class="stars">${starsHtml(shop.rating)}</span>
                <span class="rating-num">${shop.rating}</span>
                <span class="review-count">(${shop.reviews.toLocaleString()} ulasan)</span>
            </div>
            <div class="shop-addr">${shop.addr}</div>
            <div class="shop-hours">
                <span class="open-badge ${shop.openEarly ? '' : 'siang'}">${shop.openEarly ? '🌅 Buka 24 Jam' : '☀️ Buka Siang'}</span>
                <span style="color:#9ca3af;font-size:10px">${shop.hours}</span>
            </div>
            <div class="shop-tags">${shop.tags.map(t => `<span class="tag">${t}</span>`).join('')}</div>
        </div>
    `).join('');
}

function selectShop(id) {
    activeId = id;
    const selectedShop = shops.find(s => s.id === id);
    if (!selectedShop) return;
    
    // Update Google Maps iframe ke lokasi toko
    const mapFrame = document.getElementById('googleMap');
    mapFrame.src = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1000!2d${selectedShop.lng}!3d${selectedShop.lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z${selectedShop.lat}%2C${selectedShop.lng}!5e0!3m2!1sid!2sid!4v1680000000000!5m2!1sid!2sid`;
    
    let filtered = [...shops];
    if (currentFilter === 'rating5') filtered = shops.filter(s => s.rating === 5.0);
    if (currentFilter === 'buka') filtered = shops.filter(s => s.openEarly === true);
    renderCards(filtered);
    
    setTimeout(() => {
        const activeCard = document.querySelector('.shop-card.active');
        if (activeCard) activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 50);
}

function applyFilter(filterType) {
    currentFilter = filterType;
    
    let filtered = [...shops];
    if (filterType === 'rating5') filtered = shops.filter(s => s.rating === 5.0);
    if (filterType === 'buka') filtered = shops.filter(s => s.openEarly === true);
    
    const stillActive = filtered.some(s => s.id === activeId);
    if (!stillActive && filtered.length > 0) {
        activeId = filtered[0].id;
    } else if (filtered.length === 0) {
        activeId = null;
    }
    
    renderCards(filtered);
    
    if (activeId) {
        const currentShop = shops.find(s => s.id === activeId);
        if (currentShop) {
            const mapFrame = document.getElementById('googleMap');
            mapFrame.src = `https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1000!2d${currentShop.lng}!3d${currentShop.lat}!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2z${currentShop.lat}%2C${currentShop.lng}!5e0!3m2!1sid!2sid!4v1680000000000!5m2!1sid!2sid`;
        }
    }
}

document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        applyFilter(btn.getAttribute('data-filter'));
    });
});

renderCards(shops);
selectShop(1);
</script>
</body>
</html>