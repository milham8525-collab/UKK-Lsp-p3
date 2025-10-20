<?php
session_start();
include '../db/koneksi.php';

if (isset($_POST['login'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: ../admin/index.php');
            } else {
                header('Location: ../user/index.php');
            }
            exit;
        } else {
            $err = "Password salah";
        }
    } else {
        $err = "Username tidak ditemukan";
    }
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include '../navbar.php'; ?>
<div class="container mt-5" style="max-width:420px">
  <div class="card p-3">
    <h4 class="mb-3">Login</h4>
    <?php if(isset($err)): ?><div class="alert alert-danger"><?= $err ?></div><?php endif; ?>
    <form method="post">
      <input type="text" name="username" class="form-control mb-2"
             placeholder="Username"
             value="<?= htmlspecialchars($username) ?>" required>
      <input type="password" name="password" class="form-control mb-2"
             placeholder="Password" required>
      <button class="btn btn-primary w-100" type="submit" name="login">Login</button>
    </form>
    <small class="d-block mt-2">Belum punya akun? <a href="register.php">Daftar</a></small>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>