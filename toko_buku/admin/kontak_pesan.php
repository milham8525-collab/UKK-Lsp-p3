<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){ header('Location: ../auth/login.php'); exit; }
include '../db/koneksi.php';

$pesan = $conn->query("SELECT p.*, u.username, u.nama 
                       FROM pesan_kontak p 
                       LEFT JOIN users u ON p.user_id=u.user_id
                       ORDER BY p.tanggal_kirim DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Pesan User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
  <div class="mt-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
  </div>
  <h4>Pesan dari User</h4>
  <table class="table table-bordered">
    <tr><th>User</th><th>Subjek</th><th>Isi</th><th>Tanggal</th></tr>
    <?php while($p=$pesan->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($p['nama']) ?> (<?= htmlspecialchars($p['username']) ?>)</td>
        <td><?= htmlspecialchars($p['subjek']) ?></td>
        <td><?= nl2br(htmlspecialchars($p['isi_pesan'])) ?></td>
        <td><?= $p['tanggal_kirim'] ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>