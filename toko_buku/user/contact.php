<?php
session_start();
include '../db/koneksi.php';
if(!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit; }

if(isset($_POST['kirim'])){
    $uid = $_SESSION['user_id'];
    $subjek = $conn->real_escape_string($_POST['subjek']);
    $isi = $conn->real_escape_string($_POST['isi']);
    $conn->query("INSERT INTO pesan_kontak(user_id,subjek,isi_pesan) 
                  VALUES($uid,'$subjek','$isi')");
    $sukses = "Pesan berhasil dikirim ke admin.";
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Contact Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-4" style="max-width:600px">
  <h4>Hubungi Admin</h4>
  <?php if(!empty($sukses)): ?><div class="alert alert-success"><?= $sukses ?></div><?php endif; ?>
  <form method="post">
    <input type="text" name="subjek" class="form-control mb-2" placeholder="Subjek" required>
    <textarea name="isi" class="form-control mb-2" placeholder="Tulis pesan..." rows="5" required></textarea>
    <button class="btn btn-primary" name="kirim">Kirim Pesan</button>
  </form>
</div>
</body>
</html>