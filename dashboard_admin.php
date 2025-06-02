<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav class="navbar">
    <a href="index.php">Home</a>
    <a href="dashboard_admin.php">Dashboard Admin</a>
    <a href="list_data.php">Data Kampus</a>
    <a href="add_data.php">Tambah Data</a>
    <a href="free_content.php">Konten Bebas</a>
    <a href="logout.php">Logout</a>
  </nav>

  <div class="container">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! Anda memiliki akses penuh ke manajemen data kampus.</p>

    <h2>Quick Actions</h2>
    <div class="card-grid">
        <div class="card">
            <h3>Lihat Data</h3>
            <p>Lihat semua data mahasiswa dan pegawai.</p>
            <a href="list_data.php" class="btn">Lihat Data</a>
        </div>
        <div class="card">
            <h3>Tambah Data</h3>
            <p>Tambahkan data mahasiswa atau pegawai baru.</p>
            <a href="add_datas.php" class="btn">Tambah Data</a>
        </div>
        <div class="card">
            <h3>Manajemen Pengguna</h3>
            <p>Kelola akun pengguna (fitur ini belum tersedia).</p>
            <a href="#" class="btn secondary">Kelola User</a>
        </div>
    </div>

    <h2 style="margin-top: var(--spacing-xl);">Sistem Informasi Data Kampus</h2>
    <p>Sistem ini dirancang untuk memudahkan pengelolaan data mahasiswa dan pegawai di lingkungan kampus.</p>
    <ul>
        <li><strong>Manajemen Data:</strong> Tambah, edit, dan hapus data mahasiswa/pegawai dengan antarmuka yang intuitif.</li>
        <li><strong>Akses Berbasis Peran:</strong> Memisahkan akses antara administrator dan pengguna biasa untuk keamanan data.</li>
        <li><strong>Tampilan Detail:</strong> Lihat informasi lengkap setiap entri data.</li>
    </ul>
  </div>
</body>
</html>
