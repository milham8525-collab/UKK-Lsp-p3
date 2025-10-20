<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){ 
    header('Location: ../auth/login.php'); 
    exit; 
}
include '../db/koneksi.php';

/* =========================
   AUTO CANCEL >24 JAM
========================= */
$expired = $conn->query("
    SELECT * FROM pesanan 
    WHERE status='Belum Dibayar' 
      AND TIMESTAMPDIFF(HOUR, tanggal_pesanan, NOW()) >= 24
");
while($ex = $expired->fetch_assoc()){
    $pid = $ex['pesanan_id'];
    $detail = $conn->query("SELECT * FROM detail_pesanan WHERE pesanan_id=$pid");
    while($d = $detail->fetch_assoc()){
        $buku_id = $d['buku_id'];
        $jumlah  = $d['jumlah'];
        $conn->query("UPDATE buku SET stok = stok + $jumlah WHERE buku_id=$buku_id");
    }
    $conn->query("UPDATE pesanan SET status='Dibatalkan' WHERE pesanan_id=$pid");
}

/* =========================
   AMBIL DATA PESANAN
========================= */
$pesanan = $conn->query("SELECT p.*, u.username, u.nama 
                         FROM pesanan p 
                         LEFT JOIN users u ON p.user_id=u.user_id
                         ORDER BY p.pesanan_id DESC");

function badgeStatus($status){
    switch($status){
        case 'Belum Dibayar': return '<span class="badge bg-danger">Belum Dibayar</span>';
        case 'Menunggu Konfirmasi Admin': return '<span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>';
        case 'Sudah Dibayar': return '<span class="badge bg-success">Sudah Dibayar</span>';
        case 'Dibatalkan': return '<span class="badge bg-secondary">Dibatalkan</span>';
        default: return '<span class="badge bg-dark">'.htmlspecialchars($status).'</span>';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pesanan User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
  <div class="mt-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
  </div>
  <h4>Daftar Pesanan User</h4>

  <?php while($p=$pesanan->fetch_assoc()): ?>
    <div class="card mb-3">
      <div class="card-header">
        <strong><?= htmlspecialchars($p['nama']) ?> (<?= htmlspecialchars($p['username']) ?>)</strong> |
        <?= $p['tanggal_pesanan'] ?> |
        Total: Rp <?= number_format($p['total_harga'],0,',','.') ?> |
        Status: <?= badgeStatus($p['status']) ?>
      </div>
      <div class="card-body">
        <!-- Detail Pesanan -->
        <table class="table table-sm mb-3">
          <tr><th>Judul</th><th>Jumlah</th><th>Harga Satuan</th></tr>
          <?php 
            $detail = $conn->query("SELECT d.*, b.judul 
                                    FROM detail_pesanan d 
                                    JOIN buku b ON d.buku_id=b.buku_id 
                                    WHERE d.pesanan_id=".$p['pesanan_id']);
            while($d=$detail->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($d['judul']) ?></td>
              <td><?= $d['jumlah'] ?></td>
              <td>Rp <?= number_format($d['harga_satuan'],0,',','.') ?></td>
            </tr>
          <?php endwhile; ?>
        </table>

        <!-- Metode Pembayaran -->
        <p><strong>Metode Pembayaran:</strong> <?= $p['metode_pembayaran'] ?: '-' ?></p>

        <!-- Bukti Pembayaran -->
        <?php if(!empty($p['bukti_transfer'])): ?>
          <div class="mb-3">
            <p><strong>Bukti Transfer:</strong></p>
            <img src="../<?= htmlspecialchars($p['bukti_transfer']) ?>" 
                 alt="Bukti Transfer" 
                 class="img-fluid rounded border" 
                 style="max-width:300px">

            <div class="mt-2">
              <a href="../<?= htmlspecialchars($p['bukti_transfer']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                Lihat Asli
              </a>
              <a href="../<?= htmlspecialchars($p['bukti_transfer']) ?>" download class="btn btn-sm btn-outline-success">
                Download
              </a>
            </div>
          </div>
        <?php endif; ?>

        <!-- Verifikasi -->
        <?php if($p['status']=='Menunggu Konfirmasi Admin'): ?>
          <a href="verifikasi.php?id=<?= $p['pesanan_id'] ?>&aksi=accept" 
             class="btn btn-sm btn-success"
             onclick="return confirm('Terima pesanan ini?')">Terima</a>
          <a href="verifikasi.php?id=<?= $p['pesanan_id'] ?>&aksi=tolak" 
             class="btn btn-sm btn-danger"
             onclick="return confirm('Tolak pesanan ini?')">Tolak</a>
        <?php endif; ?>
      </div>
    </div>
  <?php endwhile; ?>
</div>
</body>
</html>