<?php
session_start();
include '../db/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}
$user_id = (int)$_SESSION['user_id'];

if (isset($_GET['add'])) {
    $buku_id = (int)$_GET['add'];
    $cek = $conn->query("SELECT * FROM keranjang WHERE user_id=$user_id AND buku_id=$buku_id");
    if ($cek->num_rows > 0) {
        $conn->query("UPDATE keranjang SET jumlah=jumlah+1 WHERE user_id=$user_id AND buku_id=$buku_id");
    } else {
        $conn->query("INSERT INTO keranjang(user_id,buku_id,jumlah) VALUES($user_id,$buku_id,1)");
    }
    header("Location: keranjang.php");
    exit;
}

if (isset($_POST['update_qty'])) {
    $id = (int)$_POST['id'];
    $jumlah = (int)$_POST['jumlah'];

    $stokQ = $conn->query("SELECT b.stok 
                           FROM keranjang k 
                           JOIN buku b ON k.buku_id=b.buku_id 
                           WHERE k.keranjang_id=$id AND k.user_id=$user_id");
    $stokData = $stokQ->fetch_assoc();
    $stok = (int)$stokData['stok'];

    if ($jumlah > $stok) $jumlah = $stok;
    if ($jumlah < 1) $jumlah = 1;

    $conn->query("UPDATE keranjang SET jumlah=$jumlah WHERE keranjang_id=$id AND user_id=$user_id");

    echo "ok";
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM keranjang WHERE keranjang_id=$id AND user_id=$user_id");
    header('Location: keranjang.php');
    exit;
}

if (isset($_POST['checkout'])) {
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $metode = $conn->real_escape_string($_POST['metode_pembayaran']);

    $items = $conn->query("SELECT k.*, b.judul, b.harga, b.stok 
                           FROM keranjang k 
                           JOIN buku b ON k.buku_id=b.buku_id 
                           WHERE k.user_id=$user_id");

    if ($items->num_rows == 0) {
        echo "<script>alert('Keranjang kosong!'); window.location='keranjang.php';</script>";
        exit;
    }

    while ($it = $items->fetch_assoc()) {
        if ($it['jumlah'] > $it['stok']) {
            $judul = htmlspecialchars($it['judul']);
            echo "<script>alert('Stok buku \"$judul\" tidak mencukupi!'); window.location='keranjang.php';</script>";
            exit;
        }
    }

    $conn->query("INSERT INTO pesanan(user_id,total_harga,status,tanggal_pesanan,alamat,metode_pembayaran) 
                  VALUES($user_id,0,'Belum Dibayar',NOW(),'$alamat','$metode')");
    $pesanan_id = $conn->insert_id;

    $items->data_seek(0);
    $total = 0;
    while ($it = $items->fetch_assoc()) {
        $buku_id = $it['buku_id'];
        $jumlah  = $it['jumlah'];
        $harga   = $it['harga'];

        $conn->query("INSERT INTO detail_pesanan(pesanan_id,buku_id,jumlah,harga_satuan)
                      VALUES($pesanan_id,$buku_id,$jumlah,$harga)");

        $conn->query("UPDATE buku SET stok = stok - $jumlah WHERE buku_id=$buku_id");

        $total += $jumlah * $harga;
    }

    $conn->query("UPDATE pesanan SET total_harga=$total WHERE pesanan_id=$pesanan_id");
    $conn->query("DELETE FROM keranjang WHERE user_id=$user_id");

    echo "<script>alert('Pesanan berhasil dibuat!'); window.location='detail_pesanan.php';</script>";
    exit;
}

$data = $conn->query("SELECT k.*, b.judul, b.harga, b.stok 
                      FROM keranjang k 
                      JOIN buku b ON k.buku_id=b.buku_id 
                      WHERE k.user_id=$user_id");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Keranjang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4">
    <h4>Keranjang</h4>
  <div class="mt-3 mb-4">
    <a href="index.php" class="btn btn-outline-secondary">Kembali</a>
    <a href="detail_pesanan.php" class="btn btn-outline-primary">Riwayat Pesanan</a>
  </div>

  
  <form method="post">
    <table class="table table-bordered align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Judul</th>
          <th>Harga</th>
          <th>Jumlah</th>
          <th>Subtotal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=1; $total=0; while($r=$data->fetch_assoc()): ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($r['judul']) ?></td>
          <td>Rp <?= number_format($r['harga'],0,',','.') ?></td>
          <td>
            <input type="number" class="form-control form-control-sm qty-input" 
                   data-id="<?= $r['keranjang_id'] ?>" 
                   value="<?= $r['jumlah'] ?>" min="1" max="<?= $r['stok'] ?>" 
                   style="width:80px">
          </td>
          <td>Rp <?= number_format($r['harga']*$r['jumlah'],0,',','.') ?></td>
          <td>
            <a href="?hapus=<?= $r['keranjang_id'] ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('Hapus item ini?')">Hapus</a>
          </td>
        </tr>
        <?php $total += $r['harga']*$r['jumlah']; endwhile; ?>
      </tbody>
    </table>

    <p class="fw-bold">Total: Rp <?= number_format($total,0,',','.') ?></p>

    <div class="mb-3">
      <label for="alamat" class="form-label">Alamat Pengiriman:</label>
      <textarea name="alamat" id="alamat" class="form-control" required></textarea>
    </div>

    <!-- Pilih metode pembayaran -->
    <!--<div class="mb-3">
      <label for="metode" class="form-label">Metode Pembayaran:</label>
      <select name="metode_pembayaran" id="metode" class="form-select" required>
        <option value="Transfer BCA">BCA</option>
        <option value="Transfer BRI">BRI</option>
        <option value="Transfer Mandiri">Mandiri</option>
        <option value="Transfer Dana">Dana</option>
        <option value="Transfer Gopay">Gopay</option>
        <option value="Transfer ShopeePay">ShopeePay</option>
      </select>
    </div>-->

    <button class="btn btn-success" name="checkout" <?= ($total==0?'disabled':'') ?>>
      Checkout
    </button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

document.querySelectorAll('.qty-input').forEach(input => {
  input.addEventListener('change', function() {
    let id = this.dataset.id;
    let jumlah = this.value;

    fetch('keranjang.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'update_qty=1&id='+id+'&jumlah='+jumlah
    }).then(res => res.text())
      .then(() => location.reload());
  });
});
</script>
</body>
</html>