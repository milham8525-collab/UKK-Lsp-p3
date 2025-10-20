<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin') { 
  header('Location: ../auth/login.php'); exit; 
}
include '../db/koneksi.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-4">
  <h3>Admin Dashboard</h3>
  <div class="row g-3">

    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h5>Kelola Kategori</h5>
        <a href="kategori.php" class="btn btn-primary btn-sm mt-2">Buka</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h5>Kelola Buku</h5>
        <a href="buku.php" class="btn btn-primary btn-sm mt-2">Buka</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h5>User</h5>
        <a href="users.php" class="btn btn-primary btn-sm mt-2">Buka</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h5>Pesanan User</h5>
        <a href="detail_pesanan.php" class="btn btn-primary btn-sm mt-2">Buka</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card p-3 shadow-sm">
        <h5>Pesan dari User</h5>
        <a href="kontak_pesan.php" class="btn btn-primary btn-sm mt-2">Buka</a>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>