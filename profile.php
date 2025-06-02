<?php
// Tampilkan semua error untuk debug (hapus di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autentikasi pengguna
session_start();
// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
  header("Location: login.php"); // Arahkan ke halaman login jika belum login
  exit;
}

// --- KONEKSI DATABASE ---
// Pastikan detail ini sesuai dengan yang ada di panel InfinityFree Anda
$host = 'sql201.infinityfree.com';
$user = 'if0_39101194';
$pass = 'kelompokweb123'; // Pastikan ini adalah password yang benar untuk user ini
$dbname = 'if0_39101194_kampus';

$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$userData = null; // Variabel untuk menyimpan data profil pengguna
// Pastikan Anda sudah login, dan NIM/NIP disimpan di session
if (!isset($_SESSION['nim_nip'])) {
    // Jika NIM/NIP tidak ada di session (misal admin login atau ada masalah)
    // Redirect ke dashboard atau tampilkan pesan error
    header("Location: dashboard_" . $_SESSION['role'] . ".php"); // Atau ke dashboard_user.php
    exit;
}
$loggedInNimNip = $_SESSION['nim_nip']; // Gunakan session nim_nip

// --- AMBIL DATA PROFIL PENGGUNA DARI DATABASE ---
// Gunakan Prepared Statement untuk keamanan
$sql = "SELECT * FROM data_kampus WHERE nim_nip = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("s", $loggedInNimNip); // 's' menandakan string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc(); // Ambil satu baris data
    } else {
        // Data tidak ditemukan untuk NIM/NIP ini
        // Ini bisa terjadi jika data pengguna belum ada di tabel data_kampus
        $pesan_error_profil = "Data profil Anda tidak ditemukan di sistem. Silakan hubungi administrator.";
    }
    $stmt->close();
} else {
    die("Error saat menyiapkan query profil: " . $conn->error);
}

// Tutup koneksi database
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya</title>
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
    <h1>Profil Saya</h1>
    <p>Berikut adalah informasi profil Anda yang terdaftar di sistem.</p>

    <?php if (isset($pesan_error_profil)): ?>
        <p class="message error"><?= $pesan_error_profil ?></p>
    <?php elseif ($userData): ?>
        <div class="profile-details">
            <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($userData['nama']) ?></p>
            <p><strong>NIM / NIP:</strong> <?= htmlspecialchars($userData['nim_nip']) ?></p>
            <p><strong>Program Studi / Jabatan:</strong> <?= htmlspecialchars($userData['prodi_jabatan']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']) ?></p>
            <!-- Kolom 'telepon' dan 'alamat' tidak ada di skema data_kampus yang kita buat sebelumnya.
                 Jika Anda ingin menampilkannya, pastikan kolom ini ada di tabel Anda.
                 Saya menggunakan isset() untuk menghindari error jika kolom tidak ada. -->
            <p><strong>Telepon:</strong> <?= isset($userData['telepon']) ? htmlspecialchars($userData['telepon']) : 'N/A' ?></p>
            <p><strong>Alamat:</strong> <?= isset($userData['alamat']) ? nl2br(htmlspecialchars($userData['alamat'])) : 'N/A' ?></p>
            <p><strong>Tanggal Bergabung:</strong> <?= htmlspecialchars($userData['created_at']) ?></p>
        </div>
    <?php else: ?>
        <p class="message info">Tidak dapat memuat data profil. Silakan coba lagi atau hubungi administrator.</p>
    <?php endif; ?>

    <div style="margin-top: var(--spacing-lg);">
        <a href="dashboard_user.php" class="btn secondary">Kembali ke Dashboard</a>
    </div>
  </div>
</body>
</html>
