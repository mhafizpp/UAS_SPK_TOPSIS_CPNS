-- Membuat database
CREATE DATABASE IF NOT EXISTS db_cpns;
USE db_cpns;

-- Tabel users untuk admin
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel kriteria
CREATE TABLE IF NOT EXISTS kriteria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kriteria VARCHAR(100) NOT NULL,
    bobot FLOAT NOT NULL,
    jenis ENUM('benefit', 'cost') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pelamar
CREATE TABLE IF NOT EXISTS pelamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nik VARCHAR(16) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    tempat_lahir VARCHAR(100) NOT NULL,
    tanggal_lahir DATE NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    alamat TEXT NOT NULL,
    email VARCHAR(100) NOT NULL,
    no_telp VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel penilaian
CREATE TABLE IF NOT EXISTS penilaian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pelamar INT NOT NULL,
    id_kriteria INT NOT NULL,
    nilai FLOAT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pelamar) REFERENCES pelamar(id) ON DELETE CASCADE,
    FOREIGN KEY (id_kriteria) REFERENCES kriteria(id) ON DELETE CASCADE
);

-- Insert data default untuk admin
INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator'); 