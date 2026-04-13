<?php
// asumsi koneksi sudah ada
// contoh: $conn

$keyword = "";
if (isset($_GET['search'])) {
    $keyword = trim($_GET['search']);
}

if ($keyword != "") {
    $stmt = $conn->prepare(
        "SELECT * FROM products 
         WHERE name LIKE ? 
            OR category LIKE ?"
    );

    $search = "%" . $keyword . "%";
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // kalau search kosong, tampilkan semua produk
    $result = $conn->query("SELECT * FROM products");
}
?>