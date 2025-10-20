<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/toko_buku/index.php">BookStore</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navmenu">
      <ul class="navbar-nav ms-auto align-items-center">

        <?php if(isset($_SESSION['user_id'])): ?>
          <?php if(isset($_SESSION['role']) && $_SESSION['role']=='admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/admin/index.php">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/admin/kategori.php">Kategori</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/admin/buku.php">Buku</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/admin/users.php">User</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/admin/detail_pesanan.php">Pesanan</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/admin/kontak_pesan.php">Kontak Pesan</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/user/index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/user/about.php">About</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/user/contact.php">Contact</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/user/cart.php">Keranjang</a></li>
            <li class="nav-item"><a class="nav-link" href="/toko_buku/user/detail_pesanan.php">Riwayat</a></li>
          <?php endif; ?>

          <li class="nav-item dropdown ms-3">
            <a class="nav-link dropdown-toggle text-primary fw-semibold" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <!--<li><span class="dropdown-item-text text-muted"><?= htmlspecialchars($_SESSION['nama'] ?? 'Wahidin') ?></span></li>-->
              <li><span class="dropdown-item-text text-muted">Role: <?= htmlspecialchars($_SESSION['role'] ?? '-') ?></span></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="/toko_buku/auth/logout.php">Logout</a></li>
            </ul>
          </li>

        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/toko_buku/auth/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="/toko_buku/auth/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
