<?php
include 'connection.php';

$success = '';
$error = '';

// Proses hapus jika ada parameter ?hapus=kode
if (isset($_GET['hapus'])) {
    $kode_hapus = $_GET['hapus'];
    $hapus = $conn->prepare("DELETE FROM matakuliah WHERE kode_matkul = ?");
    $hapus->bind_param("s", $kode_hapus);
    if ($hapus->execute()) {
        $success = "Data mata kuliah berhasil dihapus.";
    } else {
        $error = "Gagal menghapus data: " . $hapus->error;
    }
}

// Proses simpan data jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_matkul = $_POST['kode_matkul'];
    $nama_matkul = $_POST['nama_matkul'];
    $sks = $_POST['sks'];
    $semester = $_POST['semester'];
    $user_input = $_POST['user_input'];
    $tanggal_input = $_POST['tanggal_input'];

    // Cek apakah kode_matkul sudah ada
    $check = $conn->prepare("SELECT * FROM matakuliah WHERE kode_matkul = ?");
    $check->bind_param("s", $kode_matkul);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $error = "Kode Matkul '$kode_matkul' sudah terdaftar.";
    } else {
        // Simpan data baru
        $stmt = $conn->prepare("INSERT INTO matakuliah (kode_matkul, nama_matkul, sks, semester, user_input, tanggal_input) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiss", $kode_matkul, $nama_matkul, $sks, $semester, $user_input, $tanggal_input);

        if ($stmt->execute()) {
            $success = "Data berhasil disimpan.";
        } else {
            $error = "Gagal menyimpan data: " . $stmt->error;
        }
    }
}

// Ambil semua data untuk ditampilkan
$daftar_mk = $conn->query("SELECT * FROM matakuliah ORDER BY semester, kode_matkul ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modul Master Mata Kuliah</title>
  <link rel="stylesheet" href="modul_master_mk.css">
</head>

<body>
  <div class="navbar">
    <div class="logo"><img src="asset/logo.jpg" alt="Logo Kampus"></div>
    <button class="signout-button" onclick="window.location.href='modul_login.php'">Sign Out</button>
  </div>

  <div style="padding: 1rem;">
    <button onclick="window.location.href='menu_utama.php'" style="padding: 8px 16px; font-size: 14px;">← Kembali ke Menu Utama</button>
  </div>

  <main class="main-content">
    <section class="section-form">
      <h2>Manajemen Data Mata Kuliah</h2>

      <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
      <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

      <form class="form-mk" method="POST" action="">
        <label for="kode_matkul">Kode Matkul:</label>
        <input type="text" id="kode_matkul" name="kode_matkul" required>

        <label for="nama_matkul">Nama Matkul:</label>
        <input type="text" id="nama_matkul" name="nama_matkul" required>

        <label for="sks">SKS:</label>
        <input type="number" id="sks" name="sks" required>

        <label for="semester">Semester:</label>
        <input type="number" id="semester" name="semester" required>

        <label for="user_input">User Input:</label>
        <input type="text" id="user_input" name="user_input" required>

        <label for="tanggal_input">Tanggal Input:</label>
        <input type="date" id="tanggal_input" name="tanggal_input" required>

        <button type="submit">Simpan</button>
      </form>
    </section>

    <section class="section-tabel">
      <h3>Daftar Mata Kuliah</h3>
      <table class="table-mk">
        <thead>
          <tr>
            <th>Kode Matkul</th>
            <th>Nama Matkul</th>
            <th>SKS</th>
            <th>Semester</th>
            <th>User Input</th>
            <th>Tanggal Input</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($mk = $daftar_mk->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($mk['kode_matkul']) ?></td>
            <td><?= htmlspecialchars($mk['nama_matkul']) ?></td>
            <td><?= $mk['sks'] ?></td>
            <td><?= $mk['semester'] ?></td>
            <td><?= htmlspecialchars($mk['user_input']) ?></td>
            <td><?= $mk['tanggal_input'] ?></td>
            <td>
              <button onclick="alert('Fitur edit belum tersedia')">Edit</button>
              <a href="?hapus=<?= urlencode($mk['kode_matkul']) ?>" onclick="return confirm('Yakin ingin menghapus data ini?')">
                <button>Hapus</button>
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </section>
  </main>
</body>
</html>
