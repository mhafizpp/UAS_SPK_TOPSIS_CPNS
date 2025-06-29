<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - SPK Penerimaan CPNS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="#">SPK Penerimaan CPNS</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link active" href="index.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="kriteria.php">Kriteria</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pelamar.php">Data Pelamar</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="penilaian.php">Penilaian</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="hasil.php">Hasil TOPSIS</a>
          </li>
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <div class="row">
      <div class="col-md-12">
        <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h2>
        <p>Silakan pilih menu di atas untuk mengelola data sistem pendukung keputusan penerimaan CPNS.</p>
      </div>
    </div>

    <div class="row mt-4">
      <?php
      // Hitung jumlah pelamar
      $query_pelamar = "SELECT COUNT(*) as total FROM pelamar";
      $result_pelamar = mysqli_query($koneksi, $query_pelamar);
      $total_pelamar = mysqli_fetch_assoc($result_pelamar)['total'];

      // Hitung jumlah kriteria
      $query_kriteria = "SELECT COUNT(*) as total FROM kriteria";
      $result_kriteria = mysqli_query($koneksi, $query_kriteria);
      $total_kriteria = mysqli_fetch_assoc($result_kriteria)['total'];
      ?>
      <div class="col-md-4">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <h5 class="card-title">Total Pelamar</h5>
            <p class="card-text display-4"><?php echo $total_pelamar; ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-success text-white">
          <div class="card-body">
            <h5 class="card-title">Total Kriteria</h5>
            <p class="card-text display-4"><?php echo $total_kriteria; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>