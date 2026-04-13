# PCNexus — Panduan Setup & Perubahan Backend

## ✅ File yang Diubah / Ditambahkan

### Bug Fix
| File | Perubahan |
|------|-----------|
| `fungsi/Pdaftar.php` | Fix typo `$username1` → `$username` (bug registrasi), auto-buat kolom DB baru |
| `fungsi/masuk.php` | Load `is_seller`, `nama_toko` dari DB ke session saat login |
| `profil_page/update/dftr_penjual.php` | Simpan status penjual ke DB (bukan hanya session) |
| `profil_page/update/update_profil.php` | Simpan edit profil ke DB |
| `profil_page/profil.php` | Tombol "Daftar Jadi Penjual" & "Edit Profil" sekarang kirim ke PHP |

### File Baru (Backend Handler)
| File | Fungsi |
|------|--------|
| `fungsi/keranjang_handler.php` | AJAX handler keranjang: add/get/update/remove/clear/count |
| `fungsi/penjual_handler.php` | AJAX handler penjual: upload produk, stats, grafik real, ulasan |
| `fungsi/checkout_handler.php` | Proses order ke DB, kurangi stok, buat kode pesanan |
| `fungsi/get_produk_publik.php` | Endpoint publik: ambil produk aktif dari DB |

### Frontend Diupdate
| File | Perubahan |
|------|-----------|
| `troli.php` | Produk & keranjang terhubung ke DB via AJAX |
| `checkout.php` | Order dikirim ke DB, tidak lagi pakai localStorage |
| `penjual/penjual.php` | Grafik & statistik real dari DB, upload produk ke DB |

---

## 🗄️ Struktur Database yang Dibutuhkan

Database: `db nexus` (sesuai `dp.php`)

> **Semua tabel dibuat otomatis** saat pertama kali diakses. Tapi bisa juga import SQL di bawah.

```sql
-- Tabel pengguna (sudah ada, ditambah kolom baru)
ALTER TABLE pcnexus
  ADD COLUMN IF NOT EXISTS is_seller TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS nama_toko VARCHAR(150) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS no_hp_toko VARCHAR(20) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS deskripsi_toko TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS seller_since DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS bio TEXT DEFAULT NULL;

-- Tabel produk
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_toko VARCHAR(150) DEFAULT '',
    kategori VARCHAR(50) NOT NULL,
    merek VARCHAR(100) NOT NULL,
    nama_produk VARCHAR(200) NOT NULL,
    harga BIGINT NOT NULL,
    stok INT NOT NULL DEFAULT 1,
    kondisi ENUM('Baru','Bekas','Refurbished') DEFAULT 'Baru',
    berat DECIMAL(5,2) DEFAULT 1.00,
    deskripsi TEXT,
    foto VARCHAR(255) DEFAULT '',
    sold INT NOT NULL DEFAULT 0,
    rating DECIMAL(3,1) DEFAULT 5.0,
    status ENUM('aktif','nonaktif','habis') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel keranjang
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    produk_id INT NOT NULL,
    qty INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY user_produk (user_id, produk_id)
);

-- Tabel pesanan
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_code VARCHAR(50) NOT NULL,
    nama_lengkap VARCHAR(150) NOT NULL,
    no_hp VARCHAR(20),
    alamat TEXT,
    kota VARCHAR(100),
    kode_pos VARCHAR(10),
    metode_bayar VARCHAR(50) DEFAULT 'transfer_bank',
    status ENUM('pending','diproses','dikirim','selesai','dibatalkan') DEFAULT 'pending',
    total_harga BIGINT NOT NULL,
    ongkir INT DEFAULT 0,
    pajak INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel detail pesanan
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    produk_id INT NOT NULL,
    qty INT NOT NULL,
    harga_satuan BIGINT NOT NULL,
    subtotal BIGINT NOT NULL
);

-- Tabel ulasan
CREATE TABLE IF NOT EXISTS ulasan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    produk_id INT NOT NULL,
    rating TINYINT NOT NULL DEFAULT 5,
    komentar TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🔄 Alur Lengkap Sistem

### Alur Pembeli (User)
```
Daftar/Login → Lihat Produk (troli.php) → Klik + Keranjang
→ [AJAX] keranjang_handler.php?action=add → Cek stok → Simpan ke tabel cart
→ Klik "Bayar" → checkout.php → Isi data pengiriman & metode bayar
→ [AJAX] checkout_handler.php → Buat pesanan di DB → Kurangi stok → Hapus cart
→ Tampil kode order
```

### Alur Penjual
```
Login → Profil → "Daftar Jadi Penjual" (isi nama toko)
→ [AJAX] dftr_penjual.php → Simpan is_seller=1, nama_toko ke DB
→ Redirect ke penjual/penjual.php (hanya bisa diakses jika is_seller=true)
→ Upload Produk → [AJAX] penjual_handler.php?action=upload_produk
→ Grafik otomatis update dari data pesanan real di DB
```

---

## 📁 Folder Uploads yang Perlu Ada
```
PCNexus/uploads/
├── profile/    ← foto profil user
├── produk/     ← foto produk penjual
└── banner/     ← banner profil user
```
Buat folder ini di server, pastikan writable (chmod 777 atau 755).

---

## ⚠️ Catatan
- `dp.php` menggunakan database `db nexus` (ada spasi). Pastikan nama DB di phpMyAdmin persis sama.
- Kalau mau ganti nama DB, edit baris `$database = 'db nexus';` di `fungsi/dp.php`.
