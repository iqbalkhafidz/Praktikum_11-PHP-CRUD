<?php
// Koneksi ke database
$link = mysqli_connect('localhost', 'root', '', 'db_crud');
if (!$link) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Fungsi untuk mencari data berdasarkan nama
function search($table, $keyword) {
    global $link;
    $column = ($table === 't_dosen') ? 'namaDosen' : (($table === 't_mahasiswa') ? 'namaMhs' : 'namaMK');
    $query = "SELECT * FROM $table WHERE $column LIKE '%$keyword%'";
    return mysqli_query($link, $query);
}

// Mengecek apakah form pencarian telah di-submit
$keyword = $_GET['search'] ?? '';
$resultDosen = $keyword ? search('t_dosen', $keyword) : mysqli_query($link, "SELECT * FROM t_dosen");
$resultMahasiswa = $keyword ? search('t_mahasiswa', $keyword) : mysqli_query($link, "SELECT * FROM t_mahasiswa");
$resultMatakuliah = $keyword ? search('t_matakuliah', $keyword) : mysqli_query($link, "SELECT * FROM t_matakuliah");

// Handle create dan delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $table = $_POST['table'];
        if ($table == 'dosen') {
            $query = "INSERT INTO t_dosen (namaDosen, noHP) VALUES ('{$_POST['namaDosen']}', '{$_POST['noHP']}')";
        } elseif ($table == 'mahasiswa') {
            $query = "INSERT INTO t_mahasiswa (npm, namaMhs, prodi, alamat, noHP) VALUES ('{$_POST['npm']}', '{$_POST['namaMhs']}', '{$_POST['prodi']}', '{$_POST['alamat']}', '{$_POST['noHP']}')";
        } elseif ($table == 'matakuliah') {
            $query = "INSERT INTO t_matakuliah (kodeMK, namaMK, sks, jam) VALUES ('{$_POST['kodeMK']}', '{$_POST['namaMK']}', '{$_POST['sks']}', '{$_POST['jam']}')";
        }
        mysqli_query($link, $query);
    } elseif (isset($_POST['delete'])) {
        $table = $_POST['table'];
        $column = ($table == 'dosen') ? 'idDosen' : (($table == 'mahasiswa') ? 'npm' : 'kodeMK');
        $query = "DELETE FROM t_$table WHERE $column='{$_POST['id']}'";
        mysqli_query($link, $query);
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>CRUD Mahasiswa, Dosen, Matakuliah</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        form {
            margin-bottom: 20px;
        }
        form input[type="text"], form input[type="submit"] {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .action-buttons input[type="submit"] {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .action-buttons input[type="submit"]:hover {
            background-color: #dc3545;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
    </style>
</head>
<body>
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Cari Nama..." value="<?php echo htmlspecialchars($keyword); ?>">
        <input type="submit" value="Cari">
    </form>

    <h2>Data Dosen</h2>
    <table border="1">
        <tr><th>ID</th><th>Nama</th><th>No HP</th><th>Action</th></tr>
        <?php while ($row = mysqli_fetch_assoc($resultDosen)) { ?>
            <tr>
                <td><?php echo $row['idDosen']; ?></td>
                <td><?php echo $row['namaDosen']; ?></td>
                <td><?php echo $row['noHP']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $row['idDosen']; ?>">
                        <input type="hidden" name="table" value="dosen">
                        <input type="submit" name="delete" value="Hapus">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2>Data Mahasiswa</h2>
    <table border="1">
        <tr><th>NPM</th><th>Nama</th><th>Prodi</th><th>Alamat</th><th>No HP</th><th>Action</th></tr>
        <?php while ($row = mysqli_fetch_assoc($resultMahasiswa)) { ?>
            <tr>
                <td><?php echo $row['npm']; ?></td>
                <td><?php echo $row['namaMhs']; ?></td>
                <td><?php echo $row['prodi']; ?></td>
                <td><?php echo $row['alamat']; ?></td>
                <td><?php echo $row['noHP']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $row['npm']; ?>">
                        <input type="hidden" name="table" value="mahasiswa">
                        <input type="submit" name="delete" value="Hapus">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2>Data Matakuliah</h2>
    <table border="1">
        <tr><th>Kode</th><th>Nama</th><th>SKS</th><th>Jam</th><th>Action</th></tr>
        <?php while ($row = mysqli_fetch_assoc($resultMatakuliah)) { ?>
            <tr>
                <td><?php echo $row['kodeMK']; ?></td>
                <td><?php echo $row['namaMK']; ?></td>
                <td><?php echo $row['sks']; ?></td>
                <td><?php echo $row['jam']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $row['kodeMK']; ?>">
                        <input type="hidden" name="table" value="matakuliah">
                        <input type="submit" name="delete" value="Hapus">
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2>Tambah Data Baru</h2>
    <form method="POST" action="">
        <h3>Tambah Dosen</h3>
        <input type="hidden" name="table" value="dosen">
        <input type="text" name="namaDosen" placeholder="Nama Dosen" required>
        <input type="text" name="noHP" placeholder="No HP" required>
        <input type="submit" name="create" value="Tambah"><br><br>
    </form>

    <form method="POST" action="">
        <h3>Tambah Mahasiswa</h3>
        <input type="hidden" name="table" value="mahasiswa">
        <input type="text" name="npm" placeholder="NPM" required>
        <input type="text" name="namaMhs" placeholder="Nama Mahasiswa" required>
        <input type="text" name="prodi" placeholder="Prodi" required>
        <input type="text" name="alamat" placeholder="Alamat" required>
        <input type="text" name="noHP" placeholder="No HP" required>
        <input type="submit" name="create" value="Tambah"><br><br>
    </form>

    <form method="POST" action="">
        <h3>Tambah Matakuliah</h3>
        <input type="hidden" name="table" value="matakuliah">
        <input type="text" name="kodeMK" placeholder="Kode MK" required>
        <input type="text" name="namaMK" placeholder="Nama MK" required>
        <input type="text" name="sks" placeholder="SKS" required>
        <input type="text" name="jam" placeholder="Jam" required>
        <input type="submit" name="create" value="Tambah"><br><br>
    </form>
</body>
</html>
