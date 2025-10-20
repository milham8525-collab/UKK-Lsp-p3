<?php
session_start();
include '../db/koneksi.php';

if(isset($_POST['register'])){
    $username = $conn->real_escape_string($_POST['username']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // cek username unik
    $cek = $conn->query("SELECT * FROM users WHERE username='$username'");
    if($cek && $cek->num_rows > 0){
        $err = 'Username sudah digunakan';
    } else {
        $conn->query("INSERT INTO users(username,nama,email,password,role)
                      VALUES('$username','$nama','$email','$password','user')");
        header('Location: login.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="container mt-5" style="max-width:500px">
  <div class="card p-3">
    <h4>Register</h4>
    <?php if(isset($err)): ?><div class="alert alert-danger"><?= $err ?></div><?php endif; ?>
    <form method="post">
      <input type="text" name="username" class="form-control mb-2" placeholder="Username" required>
      <input type="text" name="nama" class="form-control mb-2" placeholder="Nama lengkap" required>
      <input type="email" name="email" class="form-control mb-2" placeholder="Email">
      <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
      <button class="btn btn-success w-100" type="submit" name="register">Daftar</button>
    </form>
    <small class="d-block mt-2">sudah punya akun? <a href="login.php">Login</a></small>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>