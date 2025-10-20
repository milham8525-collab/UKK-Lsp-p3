<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){ 
    header('Location: ../auth/login.php'); 
    exit; 
}
include '../db/koneksi.php';

if(!isset($_GET['id']) || !isset($_GET['aksi'])){
    header("Location: detail_pesanan.php");
    exit;
}

$pesanan_id = (int)$_GET['id'];
$aksi = $_GET['aksi'];

// cek pesanan
$cek = $conn->query("SELECT * FROM pesanan WHERE pesanan_id=$pesanan_id");
if($cek->num_rows == 0){
    die("<script>alert('Pesanan tidak ditemukan'); window.location='detail_pesanan.php';</script>");
}
$p = $cek->fetch_assoc();

if($aksi == "accept"){
    // ubah status jadi Sudah Dibayar
    $conn->query("UPDATE pesanan SET status='Sudah Dibayar' WHERE pesanan_id=$pesanan_id");
    $msg = "Pesanan #$pesanan_id berhasil dibayar.";
}
elseif($aksi == "tolak"){
    // kembalikan stok buku
    $detail = $conn->query("SELECT * FROM detail_pesanan WHERE pesanan_id=$pesanan_id");
    while($d = $detail->fetch_assoc()){
        $buku_id = $d['buku_id'];
        $jumlah  = $d['jumlah'];
        $conn->query("UPDATE buku SET stok = stok + $jumlah WHERE buku_id=$buku_id");
    }

    // ubah status jadi Dibatalkan
    $conn->query("UPDATE pesanan SET status='Dibatalkan' WHERE pesanan_id=$pesanan_id");
    $msg = "Pesanan #$pesanan_id berhasil ditolak dan stok dikembalikan.";
}
else{
    $msg = "Aksi tidak valid.";
}

echo "<script>alert('$msg'); window.location='detail_pesanan.php';</script>";
exit;