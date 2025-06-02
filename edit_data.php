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

$data = null; // Variabel untuk menyimpan data yang akan diedit
$pesan_sukses = '';
$pesan_error = '';

// --- AMBIL ID DARI URL DAN FETCH DATA YANG ADA ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT); // Validasi ID sebagai integer

    if ($id === false) {
        die("ID data tidak valid.");
    }

    // Ambil data yang sudah ada untuk ditampilkan di form
    $sql_select = "SELECT * FROM data_kampus WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result_select = $stmt_select->get_result();

        if ($result_select->num_rows > 0) {
            $data = $result_select->fetch_assoc();
        } else {
            die("Data dengan ID tersebut tidak ditemukan.");
        }
        $stmt_select->close();
    } else {
        die("Error saat menyiapkan query SELECT: " . $conn->error);
    }
} else {
    // Jika tidak ada ID di URL, arahkan kembali ke daftar data
    header("Location: list_data.php");
    exit;
}

// --- PROSES UPDATE DATA JIKA FORM DISUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi
    $nama = htmlspecialchars(trim($_POST['nama']));
    $nim_nip = htmlspecialchars(trim($_POST['nim_nip']));
    $prodi_jabatan = htmlspecialchars(trim($_POST['prodi_jabatan']));
    $email = htmlspecialchars(trim($_POST['email']));
    // Catatan: Kolom 'telepon' dan 'alamat' tidak ada di skema tabel data_kampus yang kita buat sebelumnya.
    // Jika Anda ingin menyimpan ini, Anda perlu menambahkan kolom 'telepon' dan 'alamat' ke tabel data_kampus di database Anda.
    // Untuk saat ini, data ini akan diambil dari form tetapi tidak akan disimpan ke database.
    $telepon = htmlspecialchars(trim($_POST['telepon']));
    $alamat = htmlspecialchars(trim($_POST['alamat']));


    // Validasi sederhana
    if (empty($nama) || empty($nim_nip) || empty($prodi_jabatan) || empty($email)) {
        $pesan_error = "Semua field wajib (Nama, NIM/NIP, Prodi/Jabatan, Email) harus diisi!";
    } else {
        // Gunakan Prepared Statement untuk keamanan
        // Sesuaikan query UPDATE jika Anda menambahkan kolom telepon dan alamat ke database
        $sql_update = "UPDATE data_kampus SET nama = ?, nim_nip = ?, prodi_jabatan = ?, email = ? WHERE id = ?";

        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("ssssi", $nama, $nim_nip, $prodi_jabatan, $email, $id); // 'ssssi' untuk 4 string dan 1 integer

            if ($stmt_update->execute()) {
                $pesan_sukses = "Data berhasil diperbarui!";
                // Setelah update, refresh data yang ditampilkan di form
                $sql_select_after_update = "SELECT * FROM data_kampus WHERE id = ?";
                if ($stmt_select_after_update = $conn->prepare($sql_select_after_update)) {
                    $stmt_select_after_update->bind_param("i", $id);
                    $stmt_select_after_update->execute();
                    $result_after_update = $stmt_select_after_update->get_result();
                    $data = $result_after_update->fetch_assoc(); // Update $data dengan data terbaru
                    $stmt_select_after_update->close();
                }
            } else {
                $pesan_error = "Error saat memperbarui data: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $pesan_error = "Error saat menyiapkan query UPDATE: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Data Kampus</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav class="navbar">
    <a href="index.php">Home</a>
    <a href="dashboard_admin.php">Dashboard Admin</a>
    <a href="list_data.php">Data Kampus</a>
    <a href="add_datas.php">Tambah Data</a>
    <a href="free_content.php">Konten Bebas</a>
    <a href="logout.php">Logout</a>
  </nav>

  <div class="container">
    <h2>Edit Data Mahasiswa/Pegawai</h2>
    <?php if ($pesan_sukses): ?>
        <p class="message success"><?= $pesan_sukses ?></p>
    <?php endif; ?>
    <?php if ($pesan_error): ?>
        <p class="message error"><?= $pesan_error ?></p>
    <?php endif; ?>

    <form action="edit_data.php?id=<?= $id ?>" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($data['id']) ?>">

        <label for="nama">Nama Lengkap:</label>
        <input type="text" id="nama" name="nama" required value="<?= htmlspecialchars($data['nama']) ?>">

        <label for="nim_nip">NIM / NIP:</label>
        <input type="text" id="nim_nip" name="nim_nip" required value="<?= htmlspecialchars($data['nim_nip']) ?>">

        <label for="prodi_jabatan">Program Studi / Jabatan:</label>
        <input type="text" id="prodi_jabatan" name="prodi_jabatan" required value="<?= htmlspecialchars($data['prodi_jabatan']) ?>">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>">

        <label for="telepon">Telepon:</label>
        <input type="tel" id="telepon" name="telepon" value="<?= isset($data['telepon']) ? htmlspecialchars($data['telepon']) : '' ?>">

        <label for="alamat">Alamat:</label>
        <textarea id="alamat" name="alamat"><?= isset($data['alamat']) ? htmlspecialchars($data['alamat']) : '' ?></textarea>

        <input type="submit" value="Perbarui Data" class="btn">
        <a href="list_data.php" class="btn secondary" style="margin-left: var(--spacing-md);">Batal</a>
    </form>
  </div>
</body>
</html>
