<?php
session_start();
if(!isset($_SESSION['role'])||$_SESSION['role']!='admin'){ header('Location: ../auth/login.php'); exit; }
include '../db/koneksi.php';
$users = $conn->query("SELECT user_id,username,nama,email,role,created_at FROM users ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Users</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
  <div class="mt-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
  </div>
  <h4>List User</h4>
  <table class="table table-bordered">
    <tr><th>ID</th><th>Username</th><th>Nama</th><th>Email</th><th>Role</th><th>Mendaftar</th></tr>
    <?php while($u=$users->fetch_assoc()): ?>
    <tr>
      <td><?= $u['user_id'] ?></td>
      <td><?= htmlspecialchars($u['username']) ?></td>
      <td><?= htmlspecialchars($u['nama']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= $u['role'] ?></td>
      <td><?= $u['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>