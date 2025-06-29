<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit();
}

// Proses simpan penilaian
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
  $id_pelamar = intval($_POST['id_pelamar']);
  $nilai = $_POST['nilai'];

  // Hapus penilaian lama
  $query = "DELETE FROM penilaian WHERE id_pelamar = $id_pelamar";
  mysqli_query($koneksi, $query);

  // Simpan penilaian baru
  foreach ($nilai as $id_kriteria => $value) {
    $value = floatval($value);
    $query = "INSERT INTO penilaian (id_pelamar, id_kriteria, nilai) VALUES ($id_pelamar, $id_kriteria, $value)";
    mysqli_query($koneksi, $query);
  }

  header("Location: penilaian.php");
  exit();
}

// Ambil data pelamar yang dipilih
$id_pelamar = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pelamar = null;
if ($id_pelamar > 0) {
  $query = "SELECT * FROM pelamar WHERE id = $id_pelamar";
  $result = mysqli_query($koneksi, $query);
  $pelamar = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penilaian - SPK Penerimaan CPNS</title>
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
            <a class="nav-link" href="index.php">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="kriteria.php">Kriteria</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="pelamar.php">Data Pelamar</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="penilaian.php">Penilaian</a>
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
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Penilaian Pelamar</h5>
          </div>
          <div class="card-body">
            <?php if (!$pelamar): ?>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>NIK</th>
                      <th>Nama Lengkap</th>
                      <th>Status Penilaian</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $query = "SELECT p.*, 
                                                (SELECT COUNT(*) FROM penilaian pn WHERE pn.id_pelamar = p.id) as jml_nilai,
                                                (SELECT COUNT(*) FROM kriteria) as jml_kriteria
                                                FROM pelamar p ORDER BY p.id ASC";
                    $result = mysqli_query($koneksi, $query);
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . $no++ . "</td>";
                      echo "<td>" . htmlspecialchars($row['nik']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                      echo "<td>" . ($row['jml_nilai'] == $row['jml_kriteria'] ?
                        '<span class="badge bg-success">Sudah Dinilai</span>' :
                        '<span class="badge bg-danger">Belum Dinilai</span>') . "</td>";
                      echo "<td>
                                                    <a href='?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Nilai</a>
                                                  </td>";
                      echo "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <h6>Penilaian untuk: <?php echo htmlspecialchars($pelamar['nama_lengkap']); ?></h6>
              <form action="" method="POST">
                <input type="hidden" name="id_pelamar" value="<?php echo $pelamar['id']; ?>">
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Kriteria</th>
                        <th>Bobot</th>
                        <th>Jenis</th>
                        <th>Nilai</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $query = "SELECT k.*, 
                                                    (SELECT nilai FROM penilaian p WHERE p.id_kriteria = k.id AND p.id_pelamar = {$pelamar['id']}) as nilai
                                                    FROM kriteria k ORDER BY k.id ASC";
                      $result = mysqli_query($koneksi, $query);
                      $no = 1;
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_kriteria']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['bobot']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['jenis']) . "</td>";
                        echo "<td>
                                                        <input type='number' class='form-control' name='nilai[{$row['id']}]' 
                                                            value='" . ($row['nilai'] ?? '') . "' required step='0.01'>
                                                      </td>";
                        echo "</tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <a href="penilaian.php" class="btn btn-secondary">Kembali</a>
                  <button type="submit" name="simpan" class="btn btn-primary">Simpan Penilaian</button>
                </div>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>