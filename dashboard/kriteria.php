<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit();
}

// Proses tambah kriteria
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
  $nama_kriteria = mysqli_real_escape_string($koneksi, $_POST['nama_kriteria']);
  $bobot = floatval($_POST['bobot']);
  $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);

  $query = "INSERT INTO kriteria (nama_kriteria, bobot, jenis) VALUES ('$nama_kriteria', $bobot, '$jenis')";
  mysqli_query($koneksi, $query);
}

// Proses edit kriteria
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
  $id = intval($_POST['id']);
  $nama_kriteria = mysqli_real_escape_string($koneksi, $_POST['nama_kriteria']);
  $bobot = floatval($_POST['bobot']);
  $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);

  $query = "UPDATE kriteria SET nama_kriteria='$nama_kriteria', bobot=$bobot, jenis='$jenis' WHERE id=$id";
  mysqli_query($koneksi, $query);
}

// Proses hapus kriteria
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $query = "DELETE FROM kriteria WHERE id = $id";
  mysqli_query($koneksi, $query);
  header("Location: kriteria.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manajemen Kriteria - SPK Penerimaan CPNS</title>
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
            <a class="nav-link active" href="kriteria.php">Kriteria</a>
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
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Data Kriteria</h5>
          </div>
          <div class="card-body">
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
              Tambah Kriteria
            </button>

            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Kriteria</th>
                  <th>Bobot</th>
                  <th>Jenis</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query = "SELECT * FROM kriteria ORDER BY id ASC";
                $result = mysqli_query($koneksi, $query);
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>C" . $no++ . "</td>"; // Kolom No ditampilkan sebagai A1, A2, ...
                  echo "<td>" . htmlspecialchars($row['nama_kriteria']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['bobot']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['jenis']) . "</td>";
                  echo "<td>
                    <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#modalEdit$row[id]'>Edit</button>
                    <a href='?hapus=$row[id]' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>
                  </td>";
                  echo "</tr>";
                }

                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal Tambah -->
  <div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Kriteria</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama Kriteria</label>
              <input type="text" class="form-control" name="nama_kriteria" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Bobot</label>
              <input type="number" class="form-control" name="bobot" step="0.01" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis</label>
              <select class="form-select" name="jenis" required>
                <option value="benefit">Benefit</option>
                <option value="cost">Cost</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php
mysqli_data_seek($result, 0); // Reset result pointer
while ($row = mysqli_fetch_assoc($result)) {
?>
  <div class="modal fade" id="modalEdit<?php echo $row['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" action="">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Kriteria</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <div class="mb-3">
              <label class="form-label">Nama Kriteria</label>
              <input type="text" class="form-control" name="nama_kriteria" value="<?php echo htmlspecialchars($row['nama_kriteria']); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Bobot</label>
              <input type="number" class="form-control" name="bobot" step="0.01" value="<?php echo $row['bobot']; ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis</label>
              <select class="form-select" name="jenis" required>
                <option value="benefit" <?php echo $row['jenis'] == 'benefit' ? 'selected' : ''; ?>>Benefit</option>
                <option value="cost" <?php echo $row['jenis'] == 'cost' ? 'selected' : ''; ?>>Cost</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php } ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>