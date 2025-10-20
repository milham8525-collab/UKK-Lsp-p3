<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: ../auth/login.php');
  exit;
}
include '../db/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

if (isset($_GET['batal'])) {
    $pid = (int)$_GET['batal'];

    $cek = $conn->query("SELECT * FROM pesanan WHERE pesanan_id=$pid AND user_id=$user_id AND status='Belum Dibayar'");
    if ($cek->num_rows > 0) {

        $detail = $conn->query("SELECT * FROM detail_pesanan WHERE pesanan_id=$pid");
        while ($d = $detail->fetch_assoc()) {
            $buku_id = $d['buku_id'];
            $jumlah  = $d['jumlah'];
            $conn->query("UPDATE buku SET stok = stok + $jumlah WHERE buku_id=$buku_id");
        }

        $conn->query("UPDATE pesanan SET status='Dibatalkan' WHERE pesanan_id=$pid");
    }
    header("Location: detail_pesanan.php");
    exit;
}

$expired = $conn->query("
    SELECT * FROM pesanan 
    WHERE status='Belum Dibayar' 
      AND user_id=$user_id 
      AND TIMESTAMPDIFF(HOUR, tanggal_pesanan, NOW()) >= 24
");
while ($ex = $expired->fetch_assoc()) {
    $pid = $ex['pesanan_id'];
    $detail = $conn->query("SELECT * FROM detail_pesanan WHERE pesanan_id=$pid");
    while ($d = $detail->fetch_assoc()) {
        $buku_id = $d['buku_id'];
        $jumlah  = $d['jumlah'];
        $conn->query("UPDATE buku SET stok = stok + $jumlah WHERE buku_id=$buku_id");
    }
    $conn->query("UPDATE pesanan SET status='Dibatalkan' WHERE pesanan_id=$pid");
}

$pesanan = $conn->query("SELECT * FROM pesanan 
                         WHERE user_id=$user_id 
                         ORDER BY pesanan_id DESC");

function badgeStatus($status)
{
  switch ($status) {
    case 'Belum Dibayar':
      return '<span class="badge bg-danger">Belum Dibayar</span>';
    case 'Sudah Dibayar':
      return '<span class="badge bg-success">Sudah Dibayar</span>';
    case 'Dibatalkan':
      return '<span class="badge bg-secondary">Dibatalkan</span>';
    default:
      return '<span class="badge bg-dark">' . htmlspecialchars($status) . '</span>';
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Riwayat Pesanan Saya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-4">
  <div class="mt-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
  </div>

  <h4>Riwayat Pesanan Saya</h4>

  <?php if ($pesanan->num_rows > 0): ?>
    <table class="table table-bordered table-striped mt-3 align-middle">
      <thead class="table-secondary">
        <tr>
          <th>ID Pesanan</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Total Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($p = $pesanan->fetch_assoc()): ?>
          <tr>
            <td>#<?= $p['pesanan_id'] ?></td>
            <td><?= $p['tanggal_pesanan'] ?></td>
            <td><?= badgeStatus($p['status']) ?></td>
            <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
            <td>
              <a href="detail.php?id=<?= $p['pesanan_id'] ?>" class="btn btn-sm btn-info">Detail</a>
              <?php if ($p['status'] == 'Belum Dibayar'): ?>
                <a href="konfirmasi.php?id=<?= $p['pesanan_id'] ?>" class="btn btn-sm btn-success">Konfirmasi</a>
                <a href="detail_pesanan.php?batal=<?= $p['pesanan_id'] ?>"
                   onclick="return confirm('Yakin ingin membatalkan pesanan ini?')"
                   class="btn btn-sm btn-danger">Batalkan</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="alert alert-info mt-3">Anda belum memiliki pesanan.</div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>