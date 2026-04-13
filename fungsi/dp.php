<?php
$host = 'localhost';
$username = 'root'; 
$password = ''; 
$database = 'db nexus'; 
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// jangan tampilkan pesan pada setiap include agar tidak mengganggu header atau output lain
// echo "Koneksi berhasil!";?>              