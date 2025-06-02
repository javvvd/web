<?php
// Tampilkan semua error untuk debug (hapus di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autentikasi admin
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
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

// --- AMBIL ID DARI URL DAN FETCH DATA ---
// Cek apakah 'id' ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT); // Validasi ID sebagai integer

    if ($id === false) {
        die("ID data tidak valid.");
    }

    // Gunakan Prepared Statement untuk keamanan
    $sql = "SELECT * FROM data_kampus WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id); // 'i' menandakan integer
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc(); // Ambil satu baris data
        } else {
            $data = null; // Data tidak ditemukan
        }
        $stmt->close();
    } else {
        die("Error saat menyiapkan query: " . $conn->error);
    }
} else {
    // Jika tidak ada ID di URL, arahkan kembali ke daftar data
    header("Location: list_data.php");
    exit;
}

// Jika data tidak ditemukan, tampilkan pesan error
if ($data === null) {
    die("Data dengan ID tersebut tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Data</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav class="navbar">
    <a href="index.php">Home</a>
    <a href="dashboard_admin.php">Dashboard Admin</a>
    <a href="list_data.php">Data Kampus</a>
    <a href="add_datas.php">Tambah Data</a> <a href="free_content.php">Konten Bebas</a>
    <a href="logout.php">Logout</a>
  </nav>

  <div class="container">
    <h1>Detail Data Mahasiswa/Pegawai</h1>
    <p>Ini adalah tampilan detail untuk data individu.</p>

    <div class="profile-details">
        <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data['nama']) ?></p>
        <p><strong>NIM / NIP:</strong> <?= htmlspecialchars($data['nim_nip']) ?></p>
        <p><strong>Program Studi / Jabatan:</strong> <?= htmlspecialchars($data['prodi_jabatan']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></p>
        <p><strong>Tanggal Ditambahkan:</strong> <?= htmlspecialchars($data['created_at']) ?></p>
    </div>

    <div style="margin-top: var(--spacing-lg);">
        <a href="edit_data.php?id=<?= $data['id'] ?>" class="btn">Edit Data Ini</a>
        <a href="list_data.php" class="btn secondary" style="margin-left: var(--spacing-md);">Kembali ke Daftar</a>
    </div>
  </div>
</body>
</html>
