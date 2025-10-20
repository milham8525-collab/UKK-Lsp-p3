<?php
session_start();
include '../db/koneksi.php';

// Ambil data buku + pencarian
$keyword = '';
if (isset($_GET['q'])) {
    $keyword = $conn->real_escape_string($_GET['q']);
    $books = $conn->query("SELECT b.*, k.nama_kategori 
                           FROM buku b 
                           LEFT JOIN kategori k ON b.kategori_id=k.kategori_id 
                           WHERE b.judul LIKE '%$keyword%'
                           ORDER BY b.buku_id DESC");
} else {
    $books = $conn->query("SELECT b.*, k.nama_kategori 
                           FROM buku b 
                           LEFT JOIN kategori k ON b.kategori_id=k.kategori_id 
                           ORDER BY b.buku_id DESC");
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .book-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    }
    .card-img-top {
      height: 150px;
      object-fit: cover;
    }
    .card-body h6 {
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Daftar Buku</h4>
    <form class="d-flex" method="get">
      <input type="text" name="q" class="form-control form-control-sm me-2" 
             placeholder="Cari judul..." value="<?= htmlspecialchars($keyword) ?>">
      <button class="btn btn-sm btn-primary">Cari</button>
    </form>
  </div>

  <div class="row g-3">
    <?php if($books->num_rows>0): ?>
      <?php while($b=$books->fetch_assoc()): ?>
      <div class="col-6 col-sm-4 col-md-3 col-lg-2">
        <div class="card book-card h-100 shadow-sm border-0 rounded-3">
          <?php if($b['gambar']): ?>
            <img src="../<?= $b['gambar'] ?>" class="card-img-top rounded-top">
          <?php else: ?>
            <div class="bg-light d-flex align-items-center justify-content-center rounded-top" 
                 style="height:150px;">
              <small class="text-muted">Tidak ada gambar</small>
            </div>
          <?php endif; ?>
          <div class="card-body p-2 d-flex flex-column">
            <h6 class="fw-bold text-truncate mb-1"><?= htmlspecialchars($b['judul']) ?></h6>
            <p class="text-muted small mb-1">
              <?= htmlspecialchars($b['nama_kategori']) ?> - <?= htmlspecialchars($b['penulis']) ?>
            </p>
            <p class="small mb-2">
              Stok: <span class="fw-semibold"><?= $b['stok']>0 ? $b['stok'] : 'Habis' ?></span>
            </p>

            <div class="mt-auto">
              <?php if($b['stok']>0): ?>
                <a href="keranjang.php?add=<?= $b['buku_id'] ?>" 
                   class="btn btn-sm btn-primary w-100 fw-semibold">
                  Rp <?= number_format($b['harga'],0,',','.') ?>
                </a>
              <?php else: ?>
                <button class="btn btn-sm btn-secondary w-100" disabled>Stok Habis</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info">Tidak ada buku ditemukan.</div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>