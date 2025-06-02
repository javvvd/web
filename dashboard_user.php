<?php
session_start();

// Tampilkan semua error untuk debug (hapus di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cek otentikasi
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
  // Arahkan ke login2.php karena Anda sudah mengganti nama file login
  header("Location: login2.php");
  exit;
}

// --- KONEKSI DATABASE ---
$host = 'sql201.infinityfree.com';
$db = 'if0_39101194_kampus';
$user = 'if0_39101194';
$pass = 'kelompokweb123'; // Pastikan ini password yang benar

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$nama_lengkap = $_SESSION['username']; // Default ke NIM/NIP jika nama tidak ditemukan
$nim_nip = $_SESSION['username']; // NIM/NIP tetap diambil dari session

// Ambil nama lengkap dari database berdasarkan NIM/NIP yang login
if (isset($_SESSION['nim_nip'])) { // Pastikan nim_nip sudah diset di session dari login2.php
    $nim_nip_login = $_SESSION['nim_nip'];

    $stmt = $conn->prepare("SELECT nama FROM data_kampus WHERE nim_nip = ?");
    $stmt->bind_param("s", $nim_nip_login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nama_lengkap = $row['nama']; // Ambil nama lengkap dari database
    }
    $stmt->close();
}

$conn->close(); // Tutup koneksi setelah selesai
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pengguna</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav class="navbar">
    <a href="index.php">Home</a>
    <a href="dashboard_user.php">Dashboard User</a>
    <a href="profile.php">Profil Saya</a>
    <a href="free_content.php">Konten Bebas</a>
    <a href="logout.php">Logout</a>
  </nav>

  <div class="container">
    <h1>Halo, <strong><?= htmlspecialchars($nama_lengkap) ?></strong>!</h1>
    <p>Selamat datang di dashboard pribadi Anda. Di sini Anda bisa melihat dan mengelola informasi profil Anda.</p>

    <h2>Informasi Akun</h2>
    <div class="profile-details">
        <p><strong>NIM / NIP:</strong> <?= htmlspecialchars($nim_nip) ?></p>
        <p><strong>Peran:</strong> Mahasiswa/Pegawai</p>
        <p><strong>Status:</strong> Aktif</p>
    </div>

    <div style="margin-top: var(--spacing-lg);">
        <a href="profile.php" class="btn">Lihat Profil Saya</a>
    </div>

    <h2 style="margin-top: var(--spacing-xl);">Tentang Sistem</h2>
    <p>Sistem Informasi Data Kampus ini dirancang untuk mempermudah akses informasi bagi mahasiswa dan pegawai.</p>
    <p>Anda dapat melihat data profil pribadi Anda serta informasi umum lainnya yang relevan dengan peran Anda.</p>
  </div>
</body>
</html>