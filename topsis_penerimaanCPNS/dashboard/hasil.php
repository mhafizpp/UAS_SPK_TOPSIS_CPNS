<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../index.php");
  exit();
}

// Fungsi untuk menghitung TOPSIS
function hitungTOPSIS($koneksi)
{
  // 1. Ambil semua data yang diperlukan
  $query_kriteria = "SELECT * FROM kriteria";
  $result_kriteria = mysqli_query($koneksi, $query_kriteria);
  $kriteria = [];
  while ($row = mysqli_fetch_assoc($result_kriteria)) {
    $kriteria[] = $row;
  }

  $query_pelamar = "SELECT * FROM pelamar";
  $result_pelamar = mysqli_query($koneksi, $query_pelamar);
  $pelamar = [];
  while ($row = mysqli_fetch_assoc($result_pelamar)) {
    $pelamar[] = $row;
  }

  $matriks = [];
  foreach ($pelamar as $p) {
    $nilai_pelamar = [];
    foreach ($kriteria as $k) {
      $query_nilai = "SELECT nilai FROM penilaian WHERE id_pelamar = {$p['id']} AND id_kriteria = {$k['id']}";
      $result_nilai = mysqli_query($koneksi, $query_nilai);
      $nilai = mysqli_fetch_assoc($result_nilai);
      $nilai_pelamar[] = $nilai ? $nilai['nilai'] : 0;
    }
    $matriks[] = $nilai_pelamar;
  }

  if (empty($matriks)) {
    return [];
  }

  // 2. Normalisasi matriks
  $matriks_normalisasi = [];
  for ($i = 0; $i < count($matriks); $i++) {
    $matriks_normalisasi[$i] = [];
    for ($j = 0; $j < count($matriks[0]); $j++) {
      $pembagi = sqrt(array_sum(array_map(function ($row) use ($j) {
        return pow($row[$j], 2);
      }, $matriks)));
      $matriks_normalisasi[$i][$j] = $matriks[$i][$j] / ($pembagi ?: 1);
    }
  }

  // 3. Normalisasi terbobot
  $matriks_terbobot = [];
  for ($i = 0; $i < count($matriks_normalisasi); $i++) {
    $matriks_terbobot[$i] = [];
    for ($j = 0; $j < count($matriks_normalisasi[0]); $j++) {
      $matriks_terbobot[$i][$j] = $matriks_normalisasi[$i][$j] * $kriteria[$j]['bobot'];
    }
  }

  // 4. Solusi ideal positif dan negatif
  $positif = [];
  $negatif = [];
  for ($j = 0; $j < count($matriks_terbobot[0]); $j++) {
    $kolom = array_column($matriks_terbobot, $j);
    if ($kriteria[$j]['jenis'] == 'benefit') {
      $positif[$j] = max($kolom);
      $negatif[$j] = min($kolom);
    } else {
      $positif[$j] = min($kolom);
      $negatif[$j] = max($kolom);
    }
  }

  // 5. Jarak solusi ideal
  $jarak_positif = [];
  $jarak_negatif = [];
  for ($i = 0; $i < count($matriks_terbobot); $i++) {
    $jarak_positif[$i] = sqrt(array_sum(array_map(function ($j) use ($matriks_terbobot, $positif, $i) {
      return pow($matriks_terbobot[$i][$j] - $positif[$j], 2);
    }, array_keys($positif))));

    $jarak_negatif[$i] = sqrt(array_sum(array_map(function ($j) use ($matriks_terbobot, $negatif, $i) {
      return pow($matriks_terbobot[$i][$j] - $negatif[$j], 2);
    }, array_keys($negatif))));
  }

  // 6. Nilai preferensi
  $preferensi = [];
  for ($i = 0; $i < count($jarak_positif); $i++) {
    $preferensi[$i] = [
      'id_pelamar' => $pelamar[$i]['id'],
      'nama' => $pelamar[$i]['nama_lengkap'],
      'nilai' => $jarak_negatif[$i] / ($jarak_negatif[$i] + $jarak_positif[$i])
    ];
  }

  // Urutkan berdasarkan nilai preferensi
  usort($preferensi, function ($a, $b) {
    return $b['nilai'] <=> $a['nilai'];
  });

  return $preferensi;
}

$hasil_topsis = hitungTOPSIS($koneksi);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil TOPSIS - SPK Penerimaan CPNS</title>
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
            <a class="nav-link" href="penilaian.php">Penilaian</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="hasil.php">Hasil TOPSIS</a>
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
            <h5 class="card-title mb-0">Hasil Perhitungan TOPSIS</h5>
          </div>
          <div class="card-body">
            <?php if (empty($hasil_topsis)): ?>
              <div class="alert alert-info">
                Belum ada data yang dapat dihitung. Pastikan sudah ada data pelamar, kriteria, dan penilaian.
              </div>
            <?php else: ?>
              
<h5 class="mb-3">Tabel Penilaian Alternatif</h5>
<div class="table-responsive mb-4">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Pelamar</th>
        <?php
        $query_kriteria = "SELECT * FROM kriteria ORDER BY id ASC";
        $result_kriteria = mysqli_query($koneksi, $query_kriteria);
        $kriteria_list = [];
        while ($k = mysqli_fetch_assoc($result_kriteria)) {
          $kriteria_list[] = $k;
          echo "<th>" . htmlspecialchars($k['nama_kriteria']) . "</th>";
        }
        ?>
      </tr>
    </thead>
    <tbody>
      <?php
      $query_pelamar = "SELECT * FROM pelamar ORDER BY id ASC";
      $result_pelamar = mysqli_query($koneksi, $query_pelamar);
      $no = 1;
      while ($p = mysqli_fetch_assoc($result_pelamar)) {
        echo "<tr>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($p['nama_lengkap']) . "</td>";
        foreach ($kriteria_list as $k) {
          $q = "SELECT nilai FROM penilaian WHERE id_pelamar = {$p['id']} AND id_kriteria = {$k['id']}";
          $r = mysqli_query($koneksi, $q);
          $n = mysqli_fetch_assoc($r);
          echo "<td>" . ($n ? $n['nilai'] : '-') . "</td>";
        }
        echo "</tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Peringkat</th>
                      <th>Nama Pelamar</th>
                      <th>Nilai Preferensi</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    foreach ($hasil_topsis as $rank => $hasil) {
                      echo "<tr>";
                      echo "<td>" . ($rank + 1) . "</td>";
                      echo "<td>" . htmlspecialchars($hasil['nama']) . "</td>";
                      echo "<td>" . number_format($hasil['nilai'], 4) . "</td>";
                      echo "<td>" . ($rank < 3 ? '<span class="badge bg-success">Lulus</span>' :
                        '<span class="badge bg-danger">Tidak Lulus</span>') . "</td>";
                      echo "</tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>