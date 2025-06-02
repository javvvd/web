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
$host = 'sql201.infinityfree.com'; // Pastikan ini hostname yang benar
$user = 'if0_39101194';           // Pastikan ini username yang benar
$pass = 'kelompokweb123';         // Pastikan ini password yang benar untuk user ini
$dbname = 'if0_39101194_kampus';  // Pastikan ini nama database yang benar

// Buat koneksi database
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek jika ada error koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// --- PROSES INSERT DATA ---
// Cek jika form telah disubmit (metode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan sanitasi
    $nama = htmlspecialchars(trim($_POST['nama']));
    $nim_nip = htmlspecialchars(trim($_POST['nim_nip']));
    $prodi_jabatan = htmlspecialchars(trim($_POST['prodi_jabatan']));
    $email = htmlspecialchars(trim($_POST['email']));

    // Validasi sederhana (pastikan tidak ada yang kosong)
    if (empty($nama) || empty($nim_nip) || empty($prodi_jabatan) || empty($email)) {
        $pesan_error = "Semua field harus diisi!";
    } else {
        // Gunakan Prepared Statements untuk keamanan
        $sql = "INSERT INTO data_kampus (nama, nim_nip, prodi_jabatan, email) VALUES (?, ?, ?, ?)";

        // Persiapkan statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameter ke statement sebagai string
            $stmt->bind_param("ssss", $nama, $nim_nip, $prodi_jabatan, $email);

            // Eksekusi statement
            if ($stmt->execute()) {
                $pesan_sukses = "Data berhasil ditambahkan!";
                // Opsional: Redirect ke list_data.php setelah sukses
                // header("Location: list_data.php");
                // exit;
            } else {
                $pesan_error = "Error saat menambahkan data: " . $stmt->error;
            }

            // Tutup statement
            $stmt->close();
        } else {
            $pesan_error = "Error saat menyiapkan query: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Data Kampus</title>
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
    <h1>Form Tambah Data Mahasiswa/Pegawai</h1>
    <p>Gunakan formulir ini untuk menambahkan data baru ke sistem.</p>

    <?php
    // Tampilkan pesan sukses atau error
    if (isset($pesan_sukses)) {
        echo "<p style='color: green; font-weight: bold;'>$pesan_sukses</p>";
    }
    if (isset($pesan_error)) {
        echo "<p style='color: red; font-weight: bold;'>$pesan_error</p>";
    }
    ?>

    <form action="" method="POST"> <div class="form-group">
            <label for="nama">Nama:</label>
            <input type="text" id="nama" name="nama" required>
        </div>
        <div class="form-group">
            <label for="nim_nip">NIM/NIP:</label>
            <input type="text" id="nim_nip" name="nim_nip" required>
        </div>
        <div class="form-group">
            <label for="prodi_jabatan">Prodi/Jabatan:</label>
            <input type="text" id="prodi_jabatan" name="prodi_jabatan" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <button type="submit" class="btn">Tambah Data</button>
    </form>
  </div>
</body>
</html>
