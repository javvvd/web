<?php
session_start();

// Tampilkan semua error untuk debug (hapus di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- KONEKSI DATABASE ---
$host = 'sql201.infinityfree.com';
$db = 'if0_39101194_kampus';
$user = 'if0_39101194';
$pass = 'kelompokweb123'; // sesuaikan jika password MySQL-mu tidak kosong

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nim_nip_input = trim($_POST['username'] ?? ''); // Ini akan jadi NIM/NIP yang diinput
    $password_input = trim($_POST['password'] ?? '');
    $role_input     = $_POST['role'] ?? '';

    if ($action === 'login') {
        // --- PROSES LOGIN (Menggunakan tabel 'data_kampus' untuk autentikasi) ---
        // Mencari pengguna berdasarkan NIM/NIP dan mengambil password serta role
        $stmt_login = $conn->prepare("SELECT password, role FROM data_kampus WHERE nim_nip = ?");
        $stmt_login->bind_param("s", $nim_nip_input);
        $stmt_login->execute();
        $stmt_login->store_result();

        if ($stmt_login->num_rows === 1) {
            $stmt_login->bind_result($db_password, $db_role);
            $stmt_login->fetch();

            // Verifikasi password dan role
            if ($password_input === $db_password && $role_input === $db_role) {
                // Login berhasil
                $_SESSION['username'] = $nim_nip_input; // Simpan NIM/NIP yang digunakan login
                $_SESSION['role'] = $role_input;
                $_SESSION['nim_nip'] = $nim_nip_input; // Simpan NIM/NIP ke session khusus untuk halaman profil

                header("Location: dashboard_" . $role_input . ".php");
                exit;
            } else {
                $error = "Password atau role salah.";
            }
        } else {
            $error = "Akun tidak ditemukan.";
        }
        $stmt_login->close();
    }

    elseif ($action === 'register') {
        // --- PROSES REGISTER (Menggunakan tabel 'data_kampus' untuk registrasi) ---
        // Mengambil data profil tambahan dari form registrasi
        $nama_input = trim($_POST['nama'] ?? '');
        $prodi_jabatan_input = trim($_POST['prodi_jabatan'] ?? '');
        $email_input = trim($_POST['email'] ?? '');

        // Cek input wajib
        if (!$nim_nip_input || !$password_input || !$role_input || !$nama_input || !$prodi_jabatan_input || !$email_input) {
            $error = "Semua field wajib diisi.";
        } else {
            // Cek apakah NIM/NIP sudah terdaftar di data_kampus
            $stmt_check_user = $conn->prepare("SELECT id FROM data_kampus WHERE nim_nip = ?");
            $stmt_check_user->bind_param("s", $nim_nip_input);
            $stmt_check_user->execute();
            $stmt_check_user->store_result();

            if ($stmt_check_user->num_rows > 0) {
                $error = "NIM/NIP sudah terdaftar.";
            } else {
                // Insert data baru ke data_kampus, termasuk detail profil
                $stmt_insert_user = $conn->prepare("INSERT INTO data_kampus (nim_nip, password, role, nama, prodi_jabatan, email) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_insert_user->bind_param("ssssss", $nim_nip_input, $password_input, $role_input, $nama_input, $prodi_jabatan_input, $email_input);
                if ($stmt_insert_user->execute()) {
                    $success = "Registrasi berhasil! Silakan login.";
                } else {
                    $error = "Gagal menyimpan data registrasi: " . $stmt_insert_user->error;
                }
            }
            $stmt_check_user->close();
        }
    }
}
$conn->close(); // Tutup koneksi setelah semua operasi selesai
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login / Register - SI Data Kampus</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .hidden { display: none; }
    .form-toggle { text-align: center; margin-top: 1em; }
    .form-toggle a { cursor: pointer; color: blue; text-decoration: underline; }
  </style>
</head>
<body>
  <div class="form-container">
    <h2 id="form-title">Login</h2>

    <?php if ($error): ?>
      <p class="message error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
      <p class="message success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form id="login-form" method="POST" action="login.php">
      <input type="hidden" name="action" value="login">
      <label for="username">NIM / NIP:</label>
      <input type="text" name="username" required autocomplete="username">

      <label for="password">Password:</label>
      <input type="password" name="password" required autocomplete="current-password">

      <label for="role">Masuk sebagai:</label>
      <select name="role">
        <option value="admin">Admin</option>
        <option value="user">Mahasiswa/Pegawai</option>
      </select>

      <button type="submit">Login</button>
    </form>

    <form id="register-form" method="POST" action="login.php" class="hidden">
      <input type="hidden" name="action" value="register">
      <label for="username">NIM / NIP:</label>
      <input type="text" name="username" required>

      <label for="password">Password:</label>
      <input type="password" name="password" required>

      <label for="nama">Nama Lengkap:</label>
      <input type="text" name="nama" required>

      <label for="prodi_jabatan">Program Studi / Jabatan:</label>
      <input type="text" name="prodi_jabatan" required>

      <label for="email">Email:</label>
      <input type="email" name="email" required>

      <label for="role">Daftar sebagai:</label>
      <select name="role">
        <option value="user">Mahasiswa/Pegawai</option>
        <option value="admin">Admin</option>
      </select>

      <button type="submit">Register</button>
    </form>

    <div class="form-toggle">
      <p id="toggle-login">Belum punya akun? <a onclick="showRegister()">Daftar di sini</a></p>
      <p id="toggle-register" class="hidden">Sudah punya akun? <a onclick="showLogin()">Login di sini</a></p>
    </div>
  </div>

  <script>
    function showRegister() {
      document.getElementById('login-form').classList.add('hidden');
      document.getElementById('register-form').classList.remove('hidden');
      document.getElementById('form-title').innerText = "Register";
      document.getElementById('toggle-login').classList.add('hidden');
      document.getElementById('toggle-register').classList.remove('hidden');
    }

    function showLogin() {
      document.getElementById('register-form').classList.add('hidden');
      document.getElementById('login-form').classList.remove('hidden');
      document.getElementById('form-title').innerText = "Login";
      document.getElementById('toggle-register').classList.add('hidden');
      document.getElementById('toggle-login').classList.remove('hidden');
    }
  </script>
</body>
</html>
