<?php
session_start();
require_once __DIR__ . '/../fungsi/dp.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil user data dari tabel pcnexus
$stmt_user = mysqli_prepare($conn, "SELECT nama_lengkap, foto_profil FROM pcnexus WHERE id = ?");
mysqli_stmt_bind_param($stmt_user, 'i', $user_id);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$user = mysqli_fetch_assoc($result_user);
mysqli_stmt_close($stmt_user);

// Jika foto_profil NULL atau kosong, set default
if (empty($user['foto_profil'])) {
    $user['foto_profil'] = 'default.png';
}

// Ambil semua video yang sudah diupload
$query_videos = "SELECT v.*, u.nama_lengkap as uploader_name 
                 FROM videos v 
                 JOIN pcnexus u ON v.user_id = u.id 
                 ORDER BY v.created_at DESC";
$result_videos = mysqli_query($conn, $query_videos);
$videos = [];
while ($row = mysqli_fetch_assoc($result_videos)) {
    $videos[] = $row;
}

// Kategori video
$categories = [
    'tutorial' => ['name' => 'Tutorial', 'icon' => '🎓', 'color' => '#3b82f6'],
    'review'   => ['name' => 'Review',   'icon' => '⭐', 'color' => '#f59e0b'],
    'unboxing' => ['name' => 'Unboxing', 'icon' => '📦', 'color' => '#10b981'],
    'modding'  => ['name' => 'Modding',  'icon' => '🔧', 'color' => '#8b5cf6'],
    'gaming'   => ['name' => 'Gaming',   'icon' => '🎮', 'color' => '#ef4444'],
    'editing'  => ['name' => 'Editing',  'icon' => '✂️', 'color' => '#06b6d4'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekomendasi Video - PCNexus</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#f7f9fc; color:#1e293b; }
        .navbar { background:#fff; padding:12px 40px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 3px rgba(0,0,0,0.05); position:sticky; top:0; z-index:100; gap:20px; flex-wrap:wrap; }
        .logo { font-size:22px; font-weight:800; color:#7c3aed; text-decoration:none; letter-spacing:-0.5px; white-space:nowrap; }
        .search-box { display:flex; align-items:center; background:#f1f5f9; border-radius:40px; padding:0 16px; flex:1; max-width:400px; min-width:200px; }
        .search-box input { flex:1; border:none; background:transparent; padding:10px 0; font-size:14px; outline:none; }
        .search-box button { background:none; border:none; cursor:pointer; color:#94a3b8; font-size:16px; padding:8px; }
        .nav-right { display:flex; align-items:center; gap:16px; flex-shrink:0; }
        .profile-nav img { width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0; transition:all 0.2s; }
        .profile-nav img:hover { border-color:#7c3aed; transform:scale(1.05); }
        .btn-upload { background:linear-gradient(135deg,#7c3aed,#a855f7); color:#fff; border:none; padding:8px 18px; border-radius:30px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; transition:all 0.2s; }
        .btn-upload:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(124,58,237,0.3); }
        .breadcrumb { padding:16px 40px; background:#fff; border-bottom:1px solid #e2e8f0; font-size:13px; }
        .breadcrumb a { color:#7c3aed; text-decoration:none; }
        .breadcrumb span { color:#94a3b8; }
        .page-header { text-align:center; padding:40px 20px 20px; }
        .page-title { font-size:32px; font-weight:800; background:linear-gradient(135deg,#7c3aed,#a855f7); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; margin-bottom:8px; }
        .page-subtitle { color:#64748b; font-size:14px; }
        .video-container { max-width:1200px; margin:0 auto; padding:20px; }
        .video-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:24px; }
        .video-card { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:all 0.3s; cursor:pointer; }
        .video-card:hover { transform:translateY(-4px); box-shadow:0 12px 24px rgba(0,0,0,0.1); }
        .video-thumbnail { position:relative; height:180px; background:#1e293b; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .video-thumbnail img { width:100%; height:100%; object-fit:cover; }
        .play-icon { position:absolute; width:50px; height:50px; background:rgba(0,0,0,0.6); border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:20px; }
        .video-category { position:absolute; top:12px; left:12px; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:600; color:#fff; }
        .video-info { padding:16px; }
        .video-title { font-size:16px; font-weight:700; margin-bottom:6px; line-height:1.4; }
        .video-channel { font-size:12px; color:#94a3b8; margin-bottom:8px; }
        .video-price { font-size:14px; font-weight:700; color:#7c3aed; margin-top:8px; }
        .video-actions { display:flex; gap:10px; margin-top:12px; }
        .btn-buy { flex:1; background:#7c3aed; color:#fff; border:none; padding:8px; border-radius:10px; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.2s; }
        .btn-buy:hover { background:#6d28d9; }
        .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center; }
        .modal.open { display:flex; }
        .modal-content { background:#fff; border-radius:24px; max-width:600px; width:90%; max-height:85vh; overflow-y:auto; }
        .modal-header { padding:20px 24px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; }
        .modal-header h3 { font-size:18px; }
        .modal-close { background:none; border:none; font-size:20px; cursor:pointer; color:#94a3b8; }
        .modal-body { padding:24px; }
        .modal-footer { padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end; gap:12px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:13px; font-weight:600; margin-bottom:6px; color:#334155; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:10px 14px; border:1px solid #e2e8f0; border-radius:12px; font-family:inherit; font-size:14px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,0.1); }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .sparepart-item { background:#f8fafc; padding:12px; border-radius:12px; margin-bottom:12px; }
        .btn-add-part { background:#f1f5f9; border:1px dashed #cbd5e1; padding:10px; border-radius:12px; width:100%; cursor:pointer; color:#7c3aed; font-weight:500; }
        .toast { position:fixed; bottom:30px; left:50%; transform:translateX(-50%); background:#1e293b; color:#fff; padding:12px 24px; border-radius:40px; font-size:13px; z-index:1100; display:none; }
        .toast.show { display:block; animation:fadeIn 0.3s; }
        @keyframes fadeIn { from { opacity:0; transform:translateX(-50%) translateY(10px); } to { opacity:1; transform:translateX(-50%) translateY(0); } }
        @media (max-width:768px) { .navbar { padding:12px 20px; gap:12px; } .search-box { order:3; width:100%; max-width:100%; } .breadcrumb { padding:12px 20px; } .video-grid { grid-template-columns:1fr; } .page-title { font-size:24px; } }
    </style>
</head>
<body>

<nav class="navbar">
    <a href="../index.php" class="logo">PCNexus</a>
    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Cari video rekomendasi...">
        <button onclick="searchVideo()"><i class="fas fa-search"></i></button>
    </div>
    <div class="nav-right">
        <button class="btn-upload" onclick="openUploadModal()"><i class="fas fa-plus"></i> Upload Video</button>
        <a href="../profil_page/profil.php" class="profile-nav">
            <img src="../uploads/profile/<?= htmlspecialchars($user['foto_profil']) ?>" alt="Profil" onerror="this.src='../uploads/profile/default.png'">
        </a>
    </div>
</nav>

<div class="breadcrumb"><a href="../index.php">Home</a> <span>/</span> <span>Rekomendasi Video</span></div>

<div class="page-header">
    <h1 class="page-title">🎬 Rekomendasi Video Hardware</h1>
    <p class="page-subtitle">Temukan tutorial, review, dan unboxing dari para ahli</p>
</div>

<div class="video-container">
    <div class="video-grid" id="videoGrid">
        <?php foreach ($videos as $video): ?>
        <div class="video-card" data-id="<?= $video['id'] ?>" onclick="openVideoModal(<?= $video['id'] ?>, '<?= htmlspecialchars(addslashes($video['video_url'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($video['video_file'] ?? '')) ?>', '<?= htmlspecialchars(addslashes($video['title'] ?? '')) ?>')" style="cursor:pointer">
            <div class="video-thumbnail">
                <?php if ($video['thumbnail']): ?>
                    <img src="../uploads/thumbnails/<?= htmlspecialchars($video['thumbnail']) ?>" alt="Thumbnail">
                <?php else: ?>
                    <div style="width:100%; height:100%; background:linear-gradient(135deg,#667eea,#764ba2); display:flex; align-items:center; justify-content:center; font-size:40px;">🎬</div>
                <?php endif; ?>
                <span class="video-category" style="background:<?= $categories[$video['category']]['color'] ?? '#7c3aed' ?>"><?= $categories[$video['category']]['icon'] ?? '📹' ?> <?= $categories[$video['category']]['name'] ?? 'Video' ?></span>
                <div class="play-icon"><i class="fas fa-play"></i></div>
            </div>
            <div class="video-info">
                <h3 class="video-title"><?= htmlspecialchars($video['title']) ?></h3>
                <p class="video-channel"><i class="fas fa-user"></i> <?= htmlspecialchars($video['uploader_name']) ?></p>
                <p class="video-price">💰 <?= number_format($video['total_price'], 0, ',', '.') ?></p>
                <div class="video-actions">
                    <button class="btn-buy" onclick="event.stopPropagation(); buyVideoSparepart(<?= $video['id'] ?>)">Beli Sparepart</button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- MODAL UPLOAD -->
<div class="modal" id="uploadModal">
    <div class="modal-content">
        <div class="modal-header"><h3><i class="fas fa-upload"></i> Upload Video Rekomendasi</h3><button class="modal-close" onclick="closeModal('uploadModal')">&times;</button></div>
        <form id="uploadForm" enctype="multipart/form-data">
            <div class="modal-body">
                <div class="form-group"><label>Judul Video *</label><input type="text" id="video_title" name="title" required placeholder="Contoh: Review RTX 5090 Terbaru"></div>
                <div class="form-group"><label>Kategori *</label><select id="video_category" name="category"><?php foreach ($categories as $key => $cat): ?><option value="<?= $key ?>"><?= $cat['icon'] ?> <?= $cat['name'] ?></option><?php endforeach; ?></select></div>
                <div class="form-group"><label>URL YouTube / Video</label><input type="url" id="video_url" name="video_url" placeholder="https://youtube.com/watch?v=..."><small style="color:#94a3b8">Opsional, bisa kosong jika upload file</small></div>
                <div class="form-group"><label>Upload Video (MP4, max 100MB)</label><input type="file" id="video_file" name="video_file" accept="video/mp4,video/webm"></div>
                <div class="form-group"><label>Thumbnail (Opsional)</label><input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp"></div>
                <div class="form-group"><label>Deskripsi</label><textarea id="video_desc" name="description" rows="3" placeholder="Jelaskan tentang video ini..."></textarea></div>
                <h4 style="margin:16px 0 12px">🔧 Sparepart yang Direkomendasikan</h4>
                <div id="sparepartList">
                    <div class="sparepart-item"><div class="form-row"><div class="form-group"><label>Nama Komponen</label><input type="text" class="part-name" placeholder="Contoh: RAM DDR4"></div><div class="form-group"><label>Spesifikasi</label><input type="text" class="part-spec" placeholder="16GB 3200MHz"></div><div class="form-group"><label>Harga</label><input type="number" class="part-price" placeholder="350000"></div></div></div>
                </div>
                <button type="button" class="btn-add-part" onclick="addSparepartField()"><i class="fas fa-plus"></i> Tambah Sparepart</button>
            </div>
            <div class="modal-footer"><button type="button" onclick="closeModal('uploadModal')">Batal</button><button type="submit">Upload Video</button></div>
        </form>
    </div>
</div>

<!-- MODAL VIDEO PLAYER -->
<div class="modal" id="videoModal">
    <div class="modal-content" style="max-width:720px; width:95%">
        <div class="modal-header">
            <h3 id="videoModalTitle" style="font-size:16px">▶ Putar Video</h3>
            <button class="modal-close" onclick="closeVideoModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding:16px">
            <div id="videoPlayerWrapper" style="width:100%; background:#000; border-radius:12px; overflow:hidden; min-height:200px; display:flex; align-items:center; justify-content:center;">
                <!-- video atau iframe akan dirender di sini oleh JS -->
            </div>
            <div style="margin-top:12px; display:flex; gap:10px; justify-content:flex-end">
                <button class="btn-buy" id="videoSparepartBtn" onclick="">Lihat Sparepart</button>
            </div>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
let sparepartCount = 1;

function showToast(msg) { 
    const toast = document.getElementById('toast'); 
    toast.textContent = msg; 
    toast.classList.add('show'); 
    setTimeout(() => toast.classList.remove('show'), 2500); 
}

function addSparepartField() { 
    sparepartCount++; 
    const html = `<div class="sparepart-item" id="part-${sparepartCount}"><div class="form-row"><div class="form-group"><label>Nama Komponen</label><input type="text" class="part-name" placeholder="Contoh: RAM DDR4"></div><div class="form-group"><label>Spesifikasi</label><input type="text" class="part-spec" placeholder="16GB 3200MHz"></div><div class="form-group"><label>Harga</label><input type="number" class="part-price" placeholder="350000"></div></div><button type="button" onclick="removeSparepartField(${sparepartCount})" style="color:#ef4444; background:none; border:none; cursor:pointer; margin-top:8px">Hapus</button></div>`; 
    document.getElementById('sparepartList').insertAdjacentHTML('beforeend', html); 
}

function removeSparepartField(id) { 
    document.getElementById(`part-${id}`).remove(); 
}

function openUploadModal() { 
    document.getElementById('uploadModal').classList.add('open'); 
}

function closeModal(id) { 
    document.getElementById(id).classList.remove('open'); 
}

// Bug 3 Fix: Fungsi buka video player
function openVideoModal(id, url, file, title) {
    document.getElementById('videoModalTitle').textContent = '▶ ' + (title || 'Putar Video');
    const wrapper = document.getElementById('videoPlayerWrapper');
    
    // Cek apakah YouTube/URL eksternal atau file lokal
    if (url && (url.includes('youtube.com') || url.includes('youtu.be'))) {
        // Convert YouTube URL ke embed
        let embedUrl = url.replace('watch?v=', 'embed/').replace('youtu.be/', 'www.youtube.com/embed/');
        // Hapus query params berlebih
        embedUrl = embedUrl.split('&')[0];
        wrapper.innerHTML = `<iframe src="${embedUrl}" width="100%" height="380" frameborder="0" allowfullscreen allow="autoplay; encrypted-media" style="border-radius:12px; display:block;"></iframe>`;
    } else if (file) {
        // File lokal yang diupload
        wrapper.innerHTML = `<video controls autoplay style="width:100%; border-radius:12px; max-height:380px; background:#000;">
            <source src="../uploads/videos/${file}" type="video/mp4">
            <source src="../uploads/videos/${file}" type="video/webm">
            Browser Anda tidak mendukung video HTML5.
        </video>`;
    } else if (url) {
        // URL video lain (bukan YouTube)
        wrapper.innerHTML = `<video controls autoplay style="width:100%; border-radius:12px; max-height:380px; background:#000;">
            <source src="${url}">
            Browser Anda tidak mendukung video HTML5.
        </video>`;
    } else {
        wrapper.innerHTML = `<div style="color:#fff; text-align:center; padding:40px; font-size:14px">⚠️ Tidak ada video untuk ditampilkan</div>`;
    }
    
    // Update tombol sparepart
    document.getElementById('videoSparepartBtn').onclick = () => { closeVideoModal(); buyVideoSparepart(id); };
    document.getElementById('videoModal').classList.add('open');
}

function closeVideoModal() {
    // Stop video/iframe saat modal ditutup
    const wrapper = document.getElementById('videoPlayerWrapper');
    wrapper.innerHTML = '';
    document.getElementById('videoModal').classList.remove('open');
}

document.getElementById('uploadForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const submitBtn = this.querySelector('button[type="submit"]');
    const title = document.getElementById('video_title').value.trim();
    const videoUrl = document.getElementById('video_url').value.trim();
    const videoFile = document.getElementById('video_file').files[0];

    if (!title) {
        showToast('❌ Judul video wajib diisi');
        return;
    }
    if (!videoUrl && !videoFile) {
        showToast('❌ Isi URL video atau upload file video');
        return;
    }

    const formData = new FormData();
    formData.append('title', title);
    formData.append('category', document.getElementById('video_category').value);
    formData.append('video_url', videoUrl);
    formData.append('description', document.getElementById('video_desc').value);
    if (videoFile) formData.append('video_file', videoFile);
    const thumbnail = document.getElementById('thumbnail').files[0];
    if (thumbnail) formData.append('thumbnail', thumbnail);
    const parts = [];
    document.querySelectorAll('.sparepart-item').forEach(item => {
        const name = item.querySelector('.part-name')?.value;
        const spec = item.querySelector('.part-spec')?.value;
        const price = item.querySelector('.part-price')?.value;
        if (name && price) parts.push({ name, spec, price: parseInt(price) });
    });
    formData.append('spareparts', JSON.stringify(parts));
    
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengupload...';
    }

    try {
        const response = await fetch('./upload_video.php', { method: 'POST', body: formData });
        const raw = await response.text();
        let result;
        try {
            result = JSON.parse(raw);
        } catch (jsonErr) {
            throw new Error(raw || 'Response server bukan JSON');
        }

        if (!response.ok) {
            throw new Error(result.message || `HTTP ${response.status}`);
        }

        if (result.success) {
            closeModal('uploadModal');
            showToast('✅ Video berhasil diupload!');
            setTimeout(() => location.reload(), 1200);
        } else {
            showToast('❌ Gagal upload: ' + (result.message || 'Unknown error'));
        }
    } catch (err) {
        showToast('❌ Gagal submit: ' + err.message);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Upload Video';
        }
    }
});

function buyVideoSparepart(videoId) {
    fetch(`get_video_sparepart.php?id=${videoId}`).then(res => res.json()).then(data => {
        if (data.success && data.spareparts.length > 0) {
            let sparepartList = '';
            data.spareparts.forEach(sp => {
                sparepartList += `- ${sp.name} (${sp.spec || '-'}) : Rp ${sp.price.toLocaleString('id-ID')}\n`;
            });
            alert(`🛒 Sparepart yang direkomendasikan:\n\n${sparepartList}\n\nTotal: Rp ${data.total_price?.toLocaleString('id-ID') || 0}\n\nHubungi penjual untuk pembelian.`);
            showToast('✅ Silakan hubungi penjual untuk pembelian sparepart');
        } else { 
            showToast('Tidak ada sparepart untuk video ini'); 
        }
    }).catch(err => showToast('Gagal ambil data sparepart'));
}

function searchVideo() { 
    const keyword = document.getElementById('searchInput').value.toLowerCase(); 
    const cards = document.querySelectorAll('.video-card'); 
    cards.forEach(card => { 
        const title = card.querySelector('.video-title')?.innerText.toLowerCase() || ''; 
        card.style.display = title.includes(keyword) ? '' : 'none'; 
    }); 
}
</script>
</body>
</html>