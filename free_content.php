<?php
// Tampilkan semua error untuk debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Koneksi database langsung (GANTI jika perlu)
$host = 'sql201.infinityfree.com';
$user = 'if0_39101194';
$pass = 'kelompokweb123';
$dbname = 'if0_39101194_kampus';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil konten dari tabel
$konten = [];
$sql = "SELECT * FROM free_content ORDER BY id DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $konten[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konten Bebas</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav class="navbar">
    <a href="index.html">Home</a>
    <a href="login.php">Login</a>
    <a href="about_us.html">Tentang Kami</a>
    <a href="free_content.php">Konten Bebas</a>
  </nav>

  <div class="container">
    <h1>Konten Bebas: Inspirasi Keberhasilan di Kampus</h1>
    <?php if (count($konten) > 0): ?>
      <?php foreach ($konten as $item): ?>
        <div class="content-block">
          <h2><?= htmlspecialchars($item['judul']) ?></h2>
          <p><?= nl2br(htmlspecialchars($item['isi'])) ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Tidak ada konten tersedia.</p>
    <?php endif; ?>

    <p style="margin-top: var(--spacing-xl);">
        <a href="login.php" class="btn">Login</a> untuk mengakses konten eksklusif dan fitur lainnya!
    </p>
  </div>
</body>
</html>
