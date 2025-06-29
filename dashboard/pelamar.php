<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit();
}

// Proses tambah pelamar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
  $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
  $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
  $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
  $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
  $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
  $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
  $email = mysqli_real_escape_string($koneksi, $_POST['email']);
  $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);

  $query = "INSERT INTO pelamar (nik, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, email, no_telp) 
              VALUES ('$nik', '$nama_lengkap', '$tempat_lahir', '$tanggal_lahir', '$jenis_kelamin', '$alamat', '$email', '$no_telp')";
  mysqli_query($koneksi, $query);
}

// Proses edit pelamar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
  $id = intval($_POST['id']);
  $nik = mysqli_real_escape_string($koneksi, $_POST['nik']);
  $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
  $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
  $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
  $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
  $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
  $email = mysqli_real_escape_string($koneksi, $_POST['email']);
  $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);

  $query = "UPDATE pelamar SET 
              nik = '$nik',
              nama_lengkap = '$nama_lengkap',
              tempat_lahir = '$tempat_lahir',
              tanggal_lahir = '$tanggal_lahir',
              jenis_kelamin = '$jenis_kelamin',
              alamat = '$alamat',
              email = '$email',
              no_telp = '$no_telp'
            WHERE id = $id";
  mysqli_query($koneksi, $query);
}

// Proses hapus pelamar
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $query = "DELETE FROM pelamar WHERE id = $id";
  mysqli_query($koneksi, $query);
  header("Location: pelamar.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Pelamar - SPK Penerimaan CPNS</title>
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
            <a class="nav-link active" href="pelamar.php">Data Pelamar</a>
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
            <h5 class="card-title mb-0">Data Pelamar</h5>
          </div>
          <div class="card-body">
            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
              Tambah Pelamar
            </button>

            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>Tempat, Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>No. Telp</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $query = "SELECT * FROM pelamar ORDER BY id ASC";
                  $result = mysqli_query($koneksi, $query);
                  $no = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>A" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nik']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_lengkap']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['tempat_lahir']) . ", " . date('d/m/Y', strtotime($row['tanggal_lahir'])) . "</td>";
                    echo "<td>" . ($row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan') . "</td>";
                    echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['no_telp']) . "</td>";
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
  </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Pelamar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">NIK</label>
              <input type="text" class="form-control" name="nik" required maxlength="16">
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" name="nama_lengkap" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" class="form-control" name="tempat_lahir" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" class="form-control" name="tanggal_lahir" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis Kelamin</label>
              <select class="form-select" name="jenis_kelamin" required>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <textarea class="form-control" name="alamat" required rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">No. Telp</label>
              <input type="text" class="form-control" name="no_telp" required>
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
// Tambah modal edit untuk setiap pelamar
mysqli_data_seek($result, 0); // Reset pointer
while ($row = mysqli_fetch_assoc($result)) {
?>
  <div class="modal fade" id="modalEdit<?php echo $row['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
      <form method="POST" action="">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Pelamar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <div class="mb-3">
              <label class="form-label">NIK</label>
              <input type="text" class="form-control" name="nik" value="<?php echo htmlspecialchars($row['nik']); ?>" required maxlength="16">
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($row['nama_lengkap']); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" class="form-control" name="tempat_lahir" value="<?php echo htmlspecialchars($row['tempat_lahir']); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" class="form-control" name="tanggal_lahir" value="<?php echo $row['tanggal_lahir']; ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Jenis Kelamin</label>
              <select class="form-select" name="jenis_kelamin" required>
                <option value="L" <?php if ($row['jenis_kelamin'] == 'L') echo 'selected'; ?>>Laki-laki</option>
                <option value="P" <?php if ($row['jenis_kelamin'] == 'P') echo 'selected'; ?>>Perempuan</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <textarea class="form-control" name="alamat" required rows="3"><?php echo htmlspecialchars($row['alamat']); ?></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">No. Telp</label>
              <input type="text" class="form-control" name="no_telp" value="<?php echo htmlspecialchars($row['no_telp']); ?>" required>
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