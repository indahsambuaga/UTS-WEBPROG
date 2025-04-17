<?php
session_start();
include 'connection.php';

$pesan_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nim = $_POST['nim'];
  $nama = $_POST['nama'];
  $tahun_masuk = $_POST['tahun_masuk'];
  $alamat = $_POST['alamat'];
  $telp = $_POST['telp'];
  $email = $_POST['email'];
  $password = md5($_POST['password']);
  $tanggal_input = $_POST['tanggal_input'];
  $role = 'mahasiswa';

  // Cek apakah email sudah digunakan
  $cekEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $cekEmail->bind_param("s", $email);
  $cekEmail->execute();
  $resEmail = $cekEmail->get_result();

  if ($resEmail->num_rows === 0) {
    // Insert ke tabel users
    $insertUser = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
    $insertUser->bind_param("sss", $email, $password, $role);
    if ($insertUser->execute()) {
      $user_id = $conn->insert_id;

      // Cek NIM duplicate
      $cekNim = $conn->prepare("SELECT 1 FROM mahasiswa WHERE nim = ?");
      $cekNim->bind_param("s", $nim);
      $cekNim->execute();
      $resNim = $cekNim->get_result();

      if ($resNim->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO mahasiswa (user_id, nim, nama, tahun_masuk, alamat, telp, email, password, user_input, tanggal_input) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississssss", $user_id, $nim, $nama, $tahun_masuk, $alamat, $telp, $email, $password, $email, $tanggal_input);
        $stmt->execute();
        $stmt->close();
      } else {
        $pesan_error = "NIM sudah terdaftar di database.";
      }
    } else {
      $pesan_error = "Gagal menambahkan user ke tabel users.";
    }
  } else {
    $pesan_error = "Email sudah terdaftar, gunakan email lain.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modul Master Mahasiswa</title>
  <link rel="stylesheet" href="modul_master_mahasiswa.css">
</head>
<body>

<div class="navbar">
  <div class="logo"><img src="asset/logo.jpg" alt="Logo Kampus"></div>
  <button class="signout-button" onclick="window.location.href='modul_login.php'">Sign Out</button>
</div>

  <!-- Tombol Kembali ke Halaman Utama -->
  <div style="padding: 1rem;">
    <button onclick="window.location.href='menu_utama.php'" style="padding: 8px 16px; font-size: 14px;">← Kembali ke Menu Utama</button>
  </div>

<main class="main-content">
  <section class="section-form">
    <h2>Modul Master Mahasiswa</h2>
    <p class="desc">Form untuk menambah atau mengubah informasi mahasiswa pada kampus Anda.</p>

    <?php if (!empty($pesan_error)) : ?>
      <p style="color: red; font-weight: bold;">
        <?= htmlspecialchars($pesan_error) ?>
      </p>
    <?php endif; ?>

    <form class="form-mahasiswa" method="post">
      <label for="nim">NIM:</label>
      <input type="text" id="nim" name="nim" required>

      <label for="nama">Nama:</label>
      <input type="text" id="nama" name="nama" required>

      <label for="tahun_masuk">Tahun Masuk:</label>
      <input type="number" id="tahun_masuk" name="tahun_masuk" required>

      <label for="alamat">Alamat:</label>
      <input type="text" id="alamat" name="alamat" required>

      <label for="telp">Telp:</label>
      <input type="text" id="telp" name="telp" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <label for="tanggal_input">Tanggal Input:</label>
      <input type="date" id="tanggal_input" name="tanggal_input" required>

      <button type="submit">Tambah Mahasiswa</button>
    </form>
  </section>

  <section class="section-tabel">
    <h3>Daftar Mahasiswa</h3>
    <table class="table-mahasiswa">
      <thead>
        <tr>
          <th>NIM</th>
          <th>Nama</th>
          <th>Tahun Masuk</th>
          <th>Alamat</th>
          <th>Telp</th>
          <th>Email</th>
          <th>Tanggal Input</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT nim, nama, tahun_masuk, alamat, telp, email, tanggal_input FROM mahasiswa ORDER BY tanggal_input DESC");
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
            <td>" . htmlspecialchars($row['nim']) . "</td>
            <td>" . htmlspecialchars($row['nama']) . "</td>
            <td>" . htmlspecialchars($row['tahun_masuk']) . "</td>
            <td>" . htmlspecialchars($row['alamat']) . "</td>
            <td>" . htmlspecialchars($row['telp']) . "</td>
            <td>" . htmlspecialchars($row['email']) . "</td>
            <td>" . htmlspecialchars($row['tanggal_input']) . "</td>
          </tr>";
        }
        ?>
      </tbody>
    </table>
  </section>
</main>

</body>
</html>