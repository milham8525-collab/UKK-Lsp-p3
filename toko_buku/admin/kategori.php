<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
include '../db/koneksi.php';

// Tambah
if (isset($_POST['tambah'])) {
    $nama = $conn->real_escape_string($_POST['nama_kategori']);
    $conn->query("INSERT INTO kategori(nama_kategori) VALUES('$nama')");
    header('Location: kategori.php');
    exit;
}

// Edit
if (isset($_POST['edit'])) {
    $id = (int)$_POST['kategori_id'];
    $nama = $conn->real_escape_string($_POST['nama_kategori']);
    $conn->query("UPDATE kategori SET nama_kategori='$nama' WHERE kategori_id=$id");
    header('Location: kategori.php');
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM kategori WHERE kategori_id=$id");
    header('Location: kategori.php');
    exit;
}

$data = $conn->query("SELECT * FROM kategori ORDER BY kategori_id DESC");
$all = $data->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Kategori</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-4">

  <div class="mt-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
  </div>

  <h4>Kategori Buku</h4>
  <button class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">+ Tambah</button>

  <table class="table table-bordered">
    <thead>
      <tr><th>ID</th><th>Nama</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      <?php foreach($all as $r): ?>
      <tr>
        <td><?= $r['kategori_id'] ?></td>
        <td><?= htmlspecialchars($r['nama_kategori']) ?></td>
        <td>
          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $r['kategori_id'] ?>">Edit</button>
          <a href="?hapus=<?= $r['kategori_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus?')">Hapus</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- ========== MODAL TAMBAH ========== -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header"><h5>Tambah Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="nama_kategori" class="form-control" placeholder="Nama kategori" required>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" name="tambah">Tambah</button>
      </div>
    </form>
  </div>
</div>

<!-- ========== MODAL EDIT (dipisah dari table) ========== -->
<?php foreach($all as $r): ?>
<div class="modal fade" id="editModal<?= $r['kategori_id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header"><h5>Edit Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="kategori_id" value="<?= $r['kategori_id'] ?>">
        <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($r['nama_kategori']) ?>" required>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" name="edit">Simpan</button>
      </div>
    </form>
  </div>
</div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>