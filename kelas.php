<?php
require 'function.php';
require 'cek.php';
require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Halaman Kelas</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav">
            <?php include 'sidebar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h2 class="mt-4">Daftar Kelas</h2>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">MASTER/Kelas</li>                            
                        </ol>
                                               
                        <br>
                        <div class="container-fluid px-4">
                            <div class="row">
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-8">
                                    <?php
                                    if (isset($_SESSION['flash_message'])) {
                                        $message_class = isset($_SESSION['flash_message_class']) ? $_SESSION['flash_message_class'] : 'alert-success';
                                        echo '<div class="alert ' . $message_class . ' text-center">' . $_SESSION['flash_message'] . '</div>';
                                        unset($_SESSION['flash_message']); // Hapus pesan flash setelah ditampilkan
                                    }
                                    
                                    ?>
                                </div>
                            </div>
                        </div>                    
                        <br>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Daftar Kelas
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No.</th>    
                                            <th>Kelas</th>
                                            <th>Wali Kelas</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $dataKelas = mysqli_query($conn, "SELECT *
                                    FROM kelas
                                    LEFT JOIN guru g ON kelas.id_guru = g.id_guru
                                    WHERE kelas.id_kelas <> 404;");
                                    $i = 1;
                                    while($data=mysqli_fetch_array($dataKelas)){
                                        $idKelas = $data['id_kelas'];
                                        $namaKelas = $data['nama_kelas'];
                                        $waliKelas = $data['nama_lengkap'];                                      
                                    ?>
                                    <tr>
                                        <td><?=$i++;?></td>
                                        <td><?=$namaKelas;?></td>
                                        <td><?=$waliKelas;?></td>
                                        <td>
                                            <button type="button" class="btn btn-warning" name="tblEdit" data-bs-toggle="modal" data-bs-target="#modalEditKelas<?=$idKelas;?>">Edit</button>                                         
                                        </td>
                                    </tr>
                                    <!-- Modal Edit Kelas-->
                                    <div class="modal fade" id="modalEditKelas<?=$idKelas;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">Edit Kelas</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <!-- Modal body -->                                            
                                            <form method="post">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="waliKelas">Kelas :</label>   
                                                    <select class="form-select" name="kelas" id="kelas" aria-label="Kelas">
                                                            <option selected><?=$idKelas;?></option>
                                                            <?php
                                                            // Ambil data kelas dari tabel kelas
                                                            $queryKelas = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas");
                                                            while ($kelas = mysqli_fetch_assoc($queryKelas)) {
                                                                echo '<option value="' . $kelas['id_kelas'] . '">' . $kelas['nama_kelas'] . '</option>';
                                                            }
                                                            ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">   
                                                    <label for="waliKelas">Wali Kelas :</label>                     
                                                    <select name="waliKelas" class="form-select" id="waliKelas" aria-label="Guru">>
                                                    <option selected><?=$waliKelas;?></option>
                                                        <?php
                                                        // Ambil data guru dari tabel guru
                                                        $queryGuru = mysqli_query($conn, "SELECT id_guru, nama_lengkap FROM guru");
                                                        while ($guru = mysqli_fetch_assoc($queryGuru)) {
                                                            echo '<option value="' . $guru['id_guru'] . '">' . $guru['nama_lengkap'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <br>
                                                <input type="hidden" name="idk" value="<?=$idKelas;?>">
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-warning" name="editKelas">Edit</button> 
                                            </div>
                                            <br> 
                                            </form>        
                                            </div>
                                        </div>
                                    </div>                                    
                                    <?php
                                    };

                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <script>
        </script>
    </body>
    <!-- Modal Tambah Guru-->
    <div class="modal fade" id="modalTambahGuru">
    <div class="modal-dialog">
        <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Tambah Guru</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <!-- Modal body -->      
            <form method="post">
            <div class="modal-body">
                <input type="text" name="nip" placeholder="NIP" class="form-control">
                <br>
                <input type="text" name="namaGuru" placeholder="Nama Guru" class="form-control" required>
                <br>
                <select class="form-select" name="jk" aria-label="Jenis Kelamin">
                    <option selected>Jenis Kelamin</option>
                    <option value="L">L</option>
                    <option value="P">P</option>
                </select>
                <br>
                <select class="form-select" name="jabatan" aria-label="jabatan">
                    <option selected>Jabatan</option>
                    <option value="Guru">Guru</option>
                    <option value="Bendahara Sekolah">Bendahara Sekolah</option>
                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                    <option value="Tenaga Administrasi Sekolah">Tenaga Administrasi Sekolah</option>
                </select>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success" name="tambahGuru">Tambah</button> 
            </div>
            <br> 
        </form>   
        </div>
    </div>
    </div>

    
</html>
