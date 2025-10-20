<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
include '../db/koneksi.php';

// Buat folder uploads jika belum ada
if (!is_dir('../uploads')) {
    mkdir('../uploads', 0777, true);
}

// Tambah Buku
if (isset($_POST['tambah'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $harga = (int)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $kategori_id = (int)$_POST['kategori_id'];

    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $namaFile = time().'_'.basename($_FILES['gambar']['name']);
        $target = '../uploads/'.$namaFile;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
        $gambar = 'uploads/'.$namaFile;
    }

    $conn->query("INSERT INTO buku(judul,penulis,harga,stok,kategori_id,gambar)
                  VALUES('$judul','$penulis',$harga,$stok,$kategori_id,'$gambar')");
    header('Location: buku.php');
    exit;
}

// Edit Buku
if (isset($_POST['edit'])) {
    $buku_id = (int)$_POST['buku_id'];
    $judul = $conn->real_escape_string($_POST['judul']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $harga = (int)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $kategori_id = (int)$_POST['kategori_id'];

    $gambar_sql = '';
    if (!empty($_FILES['gambar']['name'])) {
        $namaFile = time().'_'.basename($_FILES['gambar']['name']);
        $target = '../uploads/'.$namaFile;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target);
        $gambar_sql = ", gambar='uploads/$namaFile'";
    }

    $conn->query("UPDATE buku 
                  SET judul='$judul', penulis='$penulis', harga=$harga, stok=$stok, kategori_id=$kategori_id $gambar_sql
                  WHERE buku_id=$buku_id");
    header('Location: buku.php');
    exit;
}

// Hapus Buku
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM buku WHERE buku_id=$id");
    header('Location: buku.php');
    exit;
}

$books = $conn->query("SELECT b.*, k.nama_kategori 
                       FROM buku b LEFT JOIN kategori k ON b.kategori_id=k.kategori_id 
                       ORDER BY b.buku_id DESC");
$kategori = $conn->query("SELECT * FROM kategori");
$kategori_all = $kategori->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Kelola Buku</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-4">

  <div class="mt-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
  </div>

  <h4>Data Buku</h4>
  <button class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">+ Tambah</button>

  <table class="table table-bordered align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Judul</th>
        <th>Penulis</th>
        <th>Kategori</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Gambar</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php $modal_data=[]; while($b=$books->fetch_assoc()): $modal_data[]=$b; ?>
      <tr>
        <td><?= $b['buku_id'] ?></td>
        <td><?= htmlspecialchars($b['judul']) ?></td>
        <td><?= htmlspecialchars($b['penulis']) ?></td>
        <td><?= htmlspecialchars($b['nama_kategori']) ?></td>
        <td>Rp <?= number_format($b['harga'],0,',','.') ?></td>
        <td><?= $b['stok'] ?></td>
        <td><?php if($b['gambar']): ?><img src="../<?= $b['gambar'] ?>" style="height:50px"><?php endif; ?></td>
        <td>
          <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $b['buku_id'] ?>">Edit</button>
          <a href="?hapus=<?= $b['buku_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus buku ini?')">Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5>Tambah Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="judul" class="form-control mb-2" placeholder="Judul" required>
        <input type="text" name="penulis" class="form-control mb-2" placeholder="Penulis" required>
        <input type="number" name="harga" class="form-control mb-2" placeholder="Harga" required>
        <input type="number" name="stok" class="form-control mb-2" placeholder="Stok" required>
        <select name="kategori_id" class="form-control mb-2" required>
          <option value="">-- Pilih Kategori --</option>
          <?php foreach($kategori_all as $k): ?>
            <option value="<?= $k['kategori_id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="file" name="gambar" class="form-control">
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" name="tambah">Tambah</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit -->
<?php foreach($modal_data as $b): ?>
<div class="modal fade" id="editModal<?= $b['buku_id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form method="post" enctype="multipart/form-data" class="modal-content">
      <div class="modal-header">
        <h5>Edit Buku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="buku_id" value="<?= $b['buku_id'] ?>">
        <input type="text" name="judul" class="form-control mb-2" value="<?= htmlspecialchars($b['judul']) ?>" required>
        <input type="text" name="penulis" class="form-control mb-2" value="<?= htmlspecialchars($b['penulis']) ?>" required>
        <input type="number" name="harga" class="form-control mb-2" value="<?= $b['harga'] ?>" required>
        <input type="number" name="stok" class="form-control mb-2" value="<?= $b['stok'] ?>" required>
        <select name="kategori_id" class="form-control mb-2" required>
          <?php foreach($kategori_all as $k): ?>
            <option value="<?= $k['kategori_id'] ?>" <?= ($k['kategori_id']==$b['kategori_id']?'selected':'') ?>>
              <?= htmlspecialchars($k['nama_kategori']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <input type="file" name="gambar" class="form-control">
        <?php if($b['gambar']): ?><img src="../<?= $b['gambar'] ?>" style="height:50px" class="mt-2"><?php endif; ?>
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