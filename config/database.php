<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_cpns';

$koneksi = mysqli_connect($host, $username, $password, $database);

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}
