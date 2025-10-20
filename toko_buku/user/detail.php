<?php
session_start();
if(!isset($_SESSION['user_id'])){ 
    header('Location: ../auth/login.php'); 
    exit; 
}
include '../db/koneksi.php';

$user_id = (int)$_SESSION['user_id'];
$pesanan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil pesanan berdasarkan user login
$pesanan = $conn->query("SELECT * FROM pesanan 
                         WHERE pesanan_id=$pesanan_id 
                         AND user_id=$user_id");

if($pesanan->num_rows == 0){
    // langsung redirect ke riwayat tanpa alert
    header("Location: detail_pesanan.php");
    exit;
}
$p = $pesanan->fetch_assoc();

// Ambil detail pesanan
$detail = $conn->query("SELECT d.*, b.judul 
                        FROM detail_pesanan d 
                        JOIN buku b ON d.buku_id=b.buku_id 
                        WHERE d.pesanan_id=$pesanan_id");

// fungsi badge status
function badgeStatus($status) {
    switch ($status) {
        case 'Belum Dibayar': return '<span class="badge bg-danger">Belum Dibayar</span>';
        case 'Menunggu Konfirmasi Admin': return '<span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>';
        case 'Sudah Dibayar': return '<span class="badge bg-success">Sudah Dibayar</span>';
        case 'Pending':     return '<span class="badge bg-warning text-dark">Pending</span>';
        case 'Diproses':    return '<span class="badge bg-primary">Diproses</span>';
        case 'Dikirim':     return '<span class="badge bg-info text-dark">Dikirim</span>';
        case 'Selesai':     return '<span class="badge bg-success">Selesai</span>';
        case 'Dibatalkan':  return '<span class="badge bg-secondary">Dibatalkan</span>';
        default:            return '<span class="badge bg-dark">'.htmlspecialchars($status).'</span>';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Detail Pesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-4">

  <div class="mt-3 mb-4">
    <a href="detail_pesanan.php" class="btn btn-outline-secondary">Kembali</a>
  </div>

  <h4>Detail Pesanan #<?= $p['pesanan_id'] ?></h4>

  <div class="card mb-3">
    <div class="card-header">
      <strong>Tanggal:</strong> <?= $p['tanggal_pesanan'] ?> |
      <strong>Status:</strong> <?= badgeStatus($p['status']) ?> |
      <strong>Total:</strong> Rp <?= number_format($p['total_harga'],0,',','.') ?>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Judul Buku</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php while($d = $detail->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($d['judul']) ?></td>
              <td><?= $d['jumlah'] ?></td>
              <td>Rp <?= number_format($d['harga_satuan'],0,',','.') ?></td>
              <td>Rp <?= number_format($d['jumlah']*$d['harga_satuan'],0,',','.') ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <hr>
      <p><strong>Metode Pembayaran:</strong> <?= $p['metode_pembayaran'] ?: '-' ?></p>
      <?php if (!empty($p['bukti_transfer'])): ?>
        <p><strong>Bukti Transfer:</strong></p>
        <img src="../<?= $p['bukti_transfer'] ?>" class="img-fluid" style="max-width:300px">
      <?php endif; ?>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>