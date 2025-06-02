<?php
// Tampilkan semua error untuk debug (hapus atau sesuaikan di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autentikasi admin
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
  header("Location: login2.php"); // Ubah ke login2.php sesuai perubahan Anda
  exit;
}

// Koneksi database
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

$pesan_sukses = '';
$pesan_error = '';

// --- PROSES HAPUS DATA JIKA ADA PERMINTAAN GET 'action=delete' ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_to_delete = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($id_to_delete === false) {
        $pesan_error = "ID data yang akan dihapus tidak valid.";
    } else {
        // Gunakan Prepared Statement untuk keamanan
        $sql_delete = "DELETE FROM data_kampus WHERE id = ?";
        if ($stmt_delete = $conn->prepare($sql_delete)) {
            $stmt_delete->bind_param("i", $id_to_delete);
            if ($stmt_delete->execute()) {
                $pesan_sukses = "Data berhasil dihapus!";
                // Redirect kembali ke halaman ini tanpa parameter action/id hapus
                header("Location: list_data.php?status=deleted");
                exit;
            } else {
                $pesan_error = "Error saat menghapus data: " . $stmt_delete->error;
            }
            $stmt_delete->close();
        } else {
            $pesan_error = "Error saat menyiapkan query DELETE: " . $conn->error;
        }
    }
}

// Query untuk mengambil data dari tabel data_kampus (akan dijalankan setelah proses hapus jika ada)
$result = $conn->query("SELECT * FROM data_kampus ORDER BY created_at DESC");

// Cek jika ada error pada query
if (!$result) {
    die("Query gagal: " . $conn->error);
}

// Tutup koneksi database setelah semua operasi selesai
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Data Kampus</title>
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
    <h1>Daftar Data Mahasiswa/Pegawai</h1>
    <p>Ini adalah daftar semua data mahasiswa dan pegawai yang tersimpan dalam sistem.</p>

    <?php if ($pesan_sukses): ?>
        <p class="message success"><?= $pesan_sukses ?></p>
    <?php endif; ?>
    <?php if ($pesan_error): ?>
        <p class="message error"><?= $pesan_error ?></p>
    <?php endif; ?>
    <?php
    // Tampilkan pesan sukses dari redirect setelah hapus
    if (isset($_GET['status']) && $_GET['status'] === 'deleted') {
        echo "<p class='message success'>Data berhasil dihapus!</p>";
    }
    ?>

    <div style="margin-bottom: var(--spacing-md);">
        <a href="add_datas.php" class="btn">Tambah Data Baru</a> </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIM/NIP</th>
                <th>Prodi/Jabatan</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
          <?php
          // Cek apakah ada data yang ditemukan
          if ($result->num_rows > 0) {
              // Loop melalui setiap baris data yang diambil dari database
              while ($row = $result->fetch_assoc()):
          ?>
            <tr>
                <td data-label="Nama"><?= htmlspecialchars($row['nama']) ?></td>
                <td data-label="NIM/NIP"><?= htmlspecialchars($row['nim_nip']) ?></td>
                <td data-label="Prodi/Jabatan"><?= htmlspecialchars($row['prodi_jabatan']) ?></td>
                <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                <td data-label="Aksi">
                    <a href="detail_data.php?id=<?= $row['id'] ?>" class="action-link">Detail</a>
                    <a href="edit_data.php?id=<?= $row['id'] ?>" class="action-link">Edit</a>
                    <a href="list_data.php?action=delete&id=<?= $row['id'] ?>" class="action-link delete" onclick="return confirm('Yakin ingin menghapus data ini?');">Hapus</a>
                </td>
            </tr>
          <?php
              endwhile;
          } else {
              // Tampilkan pesan jika tidak ada data
              echo "<tr><td colspan='5'>Tidak ada data mahasiswa/pegawai yang ditemukan.</td></tr>";
          }
          ?>
        </tbody>
    </table>
  </div>
</body>
</html>
