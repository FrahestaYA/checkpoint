<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "akademik";

$konesi = mysqli_connect($host, $user, $pass, $db);
if (!$konesi) {
  die("Tidak bisa terkoneksi");
}
$nim = "";
$nama_mahasiswa = "";
$alamat = "";
$prodi = "";
$success = "";
$error = "";

if (isset($_POST['simpan'])) {
  $nim = $_POST['nim'];
  $nama_mahasiswa = $_POST['nama_mahasiswa'];
  $alamat = $_POST['alamat'];
  $prodi = $_POST['prodi'];

  if ($nim && $nama_mahasiswa && $alamat && $prodi) {
    // Cek apakah NIM sudah ada di database
    $sql_check = "SELECT * FROM `mahasiswa` WHERE nim='$nim'";
    $result_check = mysqli_query($konesi, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
      $error = "NIM sudah terdaftar. Silahkan gunakan NIM yang lain.";
    } else {
      $sql1 = "INSERT INTO `mahasiswa`(nim, nama_mahasiswa, alamat, prodi) VALUES ('$nim','$nama_mahasiswa','$alamat','$prodi')";
      $q1 = mysqli_query($konesi, $sql1);
      if ($q1) {
        $success = "Berhasil memasukkan data baru";
      } else {
        $error = "Gagal memasukkan data: " . mysqli_error($konesi);
      }
    }
  } else {
    $error = "Silahkan isi semua data";
  }
}

// Proses edit data
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $nim = $_POST['nim'];
  $nama_mahasiswa = $_POST['nama_mahasiswa'];
  $alamat = $_POST['alamat'];
  $prodi = $_POST['prodi'];

  $sql_update = "UPDATE `mahasiswa` SET nim='$nim', nama_mahasiswa='$nama_mahasiswa', alamat='$alamat', prodi='$prodi' WHERE id='$id'";
  if (mysqli_query($konesi, $sql_update)) {
    $success = "Data berhasil diperbarui";
  } else {
    $error = "Gagal memperbarui data: " . mysqli_error($konesi);
  }
}

// Proses hapus data
if (isset($_GET['delete_id'])) {
  $delete_id = $_GET['delete_id'];
  $sql_delete = "DELETE FROM `mahasiswa` WHERE id='$delete_id'";
  if (mysqli_query($konesi, $sql_delete)) {
    echo "<script>
            Swal.fire({
              title: 'Berhasil',
              text: 'Data berhasil dihapus',
              icon: 'success'
            }).then(() => {
              window.location.href = 'index.php';
            });
          </script>";
  } else {
    echo "<script>
            Swal.fire({
              title: 'Gagal',
              text: 'Gagal menghapus data: " . mysqli_error($konesi) . "',
              icon: 'error'
            });
          </script>";
  }
}

// Menampilkan data mahasiswa
$sql2 = "SELECT * FROM `mahasiswa`";
$result = mysqli_query($konesi, $sql2);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <title>Data Mahasiswa</title>
  <style>
    .mx-auto {
      max-width: 800px;
      width: 100%;
    }

    .card {
      margin-top: 10px;
    }

    @media (max-width: 576px) {
      .mx-auto {
        padding: 0 15px;
      }
    }
  </style>
</head>

<body>

  <div class="mx-auto">

    <div class="card">
      <div class="card-header">
        Create / Edit data
      </div>
      <div class="card-body">

        <?php
        if ($error) {
        ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
          </div>
        <?php
        }
        ?>
        <?php
        if ($success) {
        ?>
          <div class="alert alert-success" role="alert">
            <?php echo $success; ?>
          </div>
        <?php
        }
        ?>
        <form method="POST" action="">
          <div class="mb-3 row">
            <label for="nim" class="col-sm-2 col-form-label">NIM</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="nim" name="nim">
            </div>
          </div>
          <div class="mb-3 row">
            <label for="nama_mahasiswa" class="col-sm-2 col-form-label">Nama Mahasiswa</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="nama_mahasiswa" name="nama_mahasiswa">
            </div>
          </div>
          <div class="mb-3 row">
            <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="alamat" name="alamat">
            </div>
          </div>
          <div class="mb-3 row">
            <label for="prodi" class="col-sm-2 col-form-label">Prodi <span class="text-danger">*</span></label>
            <div class="col-sm-10">
              <select class="form-control" name="prodi" id="prodi" required>
                <option> -- Pilih Prodi -- </option>
                <option value="RPL">RPL</option>
                <option value="BD">BD</option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <input type="submit" name="simpan" value="Simpan Data" class="btn btn-primary">
          </div>
        </form>
        <div class="card">
          <div class="card-header text-white bg-secondary">
            Data Mahasiswa
          </div>
          <div class="card-body">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">No</th>
                  <th scope="col">NIM</th>
                  <th scope="col">Nama Mahasiswa</th>
                  <th scope="col">Alamat</th>
                  <th scope="col">Prodi</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                  $no = 1; // Inisialisasi nomor
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <th scope='row'>{$no}</th>
                            <td>{$row['nim']}</td>
                            <td>{$row['nama_mahasiswa']}</td>
                            <td>{$row['alamat']}</td>
                            <td>{$row['prodi']}</td>
                            <td>
                              <button type='button' class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal{$row['id']}'>Edit</button>
                              <a href='javascript:void(0);' onclick='confirmDelete({$row['id']});' class='btn btn-danger btn-sm'>Hapus</a>
                            </td>
                          </tr>";

                    // Modal Edit
                    echo "<div class='modal fade' id='editModal{$row['id']}' tabindex='-1' aria-labelledby='editModalLabel' aria-hidden='true'>
                            <div class='modal-dialog'>
                              <div class='modal-content'>
                                <div class='modal-header'>
                                  <h5 class='modal-title' id='editModalLabel'>Edit Data Mahasiswa</h5>
                                  <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <div class='modal-body'>
                                  <form method='POST' action=''>
                                    <input type='hidden' name='id' value='{$row['id']}'>
                                    <div class='mb-3'>
                                      <label for='nim' class='form-label'>NIM</label>
                                      <input type='text' class='form-control' id='nim' name='nim' value='{$row['nim']}'>
                                    </div>
                                    <div class='mb-3'>
                                      <label for='nama_mahasiswa' class='form-label'>Nama Mahasiswa</label>
                                      <input type='text' class='form-control' id='nama_mahasiswa' name='nama_mahasiswa' value='{$row['nama_mahasiswa']}'>
                                    </div>
                                    <div class='mb-3'>
                                      <label for='alamat' class='form-label'>Alamat</label>
                                      <input type='text' class='form-control' id='alamat' name='alamat' value='{$row['alamat']}'>
                                    </div>
                                    <div class='mb-3'>
                                      <label for='prodi' class='form-label'>Prodi</label>
                                      <select class='form-control' name='prodi' id='prodi'>
                                        <option value='RPL' " . ($row['prodi'] == 'RPL' ? 'selected' : '') . ">RPL</option>
                                        <option value='BD' " . ($row['prodi'] == 'BD' ? 'selected' : '') . ">BD</option>
                                      </select>
                                    </div>
                                    <div class='modal-footer'>
                                      <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                                      <input type='submit' name='update' value='Update Data' class='btn btn-primary'>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                          </div>";
                    $no++; // Increment nomor
                  }
                } else {
                  echo "<tr><td colspan='6'>Tidak ada data mahasiswa</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script>
        function confirmDelete(id) {
          Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus data ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = '?delete_id=' + id;
            }
          });
        }
      </script>
</body>

</html>