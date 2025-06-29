<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = mysqli_real_escape_string($koneksi, $_POST['username']);
  $password = $_POST['password'];

  $query = "SELECT * FROM users WHERE username = '$username'";
  $result = mysqli_query($koneksi, $query);

  if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

      header("Location: ../dashboard/index.php");
      exit();
    }
  }

  $_SESSION['error'] = "Username atau password salah!";
  header("Location: ../index.php");
  exit();
}

header("Location: ../index.php");
exit();
