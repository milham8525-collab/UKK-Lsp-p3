<?php
session_start();
include '../db/koneksi.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$pesanan_id = (int)$_GET['id'];

// cek apakah pesanan milik user dan masih Belum Dibayar
$cek = $conn->query("SELECT * FROM pesanan 
                     WHERE pesanan_id=$pesanan_id 
                       AND user_id=$user_id 
                       AND status='Belum Dibayar'");
if($cek->num_rows == 0){
    die("<script>alert('Pesanan tidak ditemukan atau sudah dikonfirmasi/dibatalkan.'); window.location='detail_pesanan.php';</script>");
}
$p = $cek->fetch_assoc();

$message = '';
$alertClass = 'info';

// jika user klik konfirmasi
if(isset($_POST['konfirmasi'])){
    $metode  = $conn->real_escape_string($_POST['metode']);

    // upload bukti transfer
    $bukti = '';
    if(isset($_FILES['bukti']) && $_FILES['bukti']['error']==0){
        $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
        $bukti = "uploads/bukti_pembayaran/bukti_" . time() . ".$ext";

        // buat folder jika belum ada
        if(!is_dir("../uploads/bukti_pembayaran")){
            mkdir("../uploads/bukti_pembayaran", 0777, true);
        }

        move_uploaded_file($_FILES['bukti']['tmp_name'], "../$bukti");
    }

    // simpan data konfirmasi
    $conn->query("UPDATE pesanan 
                  SET status='Menunggu Konfirmasi Admin',
                      metode_pembayaran='$metode',
                      bukti_transfer='$bukti'
                  WHERE pesanan_id=$pesanan_id");

    $message = "✅ Konfirmasi berhasil! Menunggu verifikasi admin.";
    $alertClass = "success";
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Konfirmasi Pembayaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container mt-4">
  <h4>Konfirmasi Pembayaran</h4>
  <div class="card p-3 mb-3">
    <p><strong>ID Pesanan:</strong> #<?= $p['pesanan_id'] ?></p>
    <p><strong>Total Bayar:</strong> Rp <?= number_format($p['total_harga'],0,',','.') ?></p>
    <p><strong>Status Saat Ini:</strong> <?= htmlspecialchars($p['status']) ?></p>
  </div>

  <form method="post" enctype="multipart/form-data">
    <!-- kotak rekening muncul di sini -->
    <div id="rekeningBox" class="alert alert-info d-none mb-3"></div>

    <div class="mb-3">
      <label for="metode" class="form-label">Metode Pembayaran</label>
      <select name="metode" id="metode" class="form-select" required onchange="showRekening()">
        <option value="">-- Pilih Metode --</option>
        <option value="Transfer Bank">Transfer Bank</option>
        <option value="E-Wallet">E-Wallet</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="bukti" class="form-label">Upload Bukti Transfer</label>
      <input type="file" name="bukti" id="bukti" class="form-control" accept="image/*" required>
    </div>

    <?php if($message): ?>
      <div class="alert alert-<?= $alertClass ?>"><?= $message ?></div>
    <?php endif; ?>

    <button type="submit" name="konfirmasi" class="btn btn-success">Kirim Konfirmasi</button>
    <a href="detail_pesanan.php" class="btn btn-outline-secondary">Kembali</a>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// tampilkan nomor rekening sesuai pilihan
function showRekening(){
  let metode = document.getElementById("metode").value;
  let box = document.getElementById("rekeningBox");
  box.classList.add("d-none");

  if(metode === "Transfer Bank"){
    box.innerHTML = "<strong>Transfer ke:</strong><br>Bank BCA - 1234567890 a.n PT Toko Buku Ajaib";
    box.classList.remove("d-none");
  } else if(metode === "E-Wallet"){
    box.innerHTML = "<strong>Transfer ke:</strong><br>OVO / DANA / Gopay - 085818283848 a.n PT Toko Buku Ajaib";
    box.classList.remove("d-none");
  }
}
</script>
</body>
</html>