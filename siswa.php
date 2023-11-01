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
        <title>Halaman Siswa</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-fNPd/1L37kqYjQbdzC2J9PTzivtbDpVf05/5CAZP9ZR5QLr18P+RvBDZ57bKvzsmwxOLY/6i7jzWBn5CBb4er4dg==" crossorigin="anonymous" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav">
            <?php include 'sidebar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h2 class="mt-4">Daftar Siswa</h2>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">DATA/Siswa</li>
                        </ol>
                        <?php 
                        $queryJumlahSiswa = mysqli_query($conn, "SELECT
                        (SELECT COUNT(*) FROM siswa WHERE `status` = 'aktif') AS jumlah_total,
                        (SELECT COUNT(*) FROM siswa WHERE jk = 'L' AND `status` = 'aktif') AS jumlah_laki_laki,
                        (SELECT COUNT(*) FROM siswa WHERE jk = 'P' AND `status` = 'aktif') AS jumlah_perempuan;");

                        $dataJumlah = mysqli_fetch_assoc($queryJumlahSiswa);
                        $jumlahLaki = $dataJumlah['jumlah_laki_laki'];
                        $jumlahPerempuan = $dataJumlah['jumlah_perempuan']; 
                        $totalSiswa = $dataJumlah['jumlah_total'];  

                        ?>

                        <!-- Card -->
                        <div class="container-fluid px-2">
                            <div class="row">
                                <div class="col-md-1">
                                    <img src="assets/img/group2.png" width="50px">
                                    <h6>Total</h6>
                                    <h3><?=$totalSiswa;?></h3>
                                </div>
                                <div class="col-md-1">
                                    <img src="assets/img/male.png" width="50px">
                                    <h6>Laki-laki</h6>
                                    <h3><?=$jumlahLaki;?></h3>
                                </div>
                                <div class="col-md-1">
                                    <img src="assets/img/female.png" width="50px">
                                    <h6>Perempuan</h6>
                                    <h3><?=$jumlahPerempuan;?></h3>
                                </div>
                                <div class="col-md-9">
                                </div>
                            </div>
                            
                        </div><br>                       

                        <?php
                        // Loop untuk membuat tabel untuk setiap kelas
                        for ($kelas = 1; $kelas <= 6; $kelas++) {
                            // Query untuk mengambil data siswa berdasarkan kelas
                            $query = "SELECT * FROM siswa WHERE id_kelas = $kelas AND `status`='aktif' ORDER BY id_kelas ASC";
                            $result = mysqli_query($conn, $query);
                            
                            // Mulai tabel baru untuk setiap kelas
                            echo '<div class="card mb-4">';
                            echo '<div class="card-header">';
                            echo '<i class="fas fa-table me-1"></i>';
                            echo 'Daftar Siswa Kelas ' . $kelas;
                            echo '</div>';
                            echo '<div class="card-body">';
                            echo '<table id="datatablesSimple1" class="table table-bordered">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th>No.</th>';
                            echo '<th>Nama</th>';
                            echo '<th>NISN</th>';
                            echo '<th>Kelas</th>';
                            echo '<th>Jenis Kelamin</th>';
                            echo '<th>Tempat Lahir</th>';
                            echo '<th>Tanggal Lahir</th>';
                            echo '<th>Agama</th>';
                            echo '<th>Alamat</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            $i = 1;
                            $jkTampil = '';
                            $jumlahLakiLaki = 0;
                            $jumlahPerempuan = 0;

                            while ($data = mysqli_fetch_array($result)) {
                                // Ambil data siswa untuk setiap kelas
                                $namaSiswa = $data['nama'];
                                $nisn = $data['nisn'];
                                $kelas = $data['id_kelas'];
                                $jk = $data['jk'];
                                $tempatLahir = $data['tempat_lahir'];
                                $tanggalLahir = $data['tanggal_lahir'];
                                $agama = $data['agama'];
                                $alamat = $data['alamat'];
                                $ids = $data['id_siswa'];
                                $status = $data['status'];
                    
                                if ($jk == 'L') {
                                    $jumlahLakiLaki++;
                                } elseif ($jk == 'P') {
                                    $jumlahPerempuan++;
                                }

                                // Tampilkan data siswa dalam tabel
                                echo '<tr>';
                                echo '<td>' . $i++ . '</td>';
                                echo '<td>' . $namaSiswa . '</td>';
                                echo '<td>' . $nisn . '</td>';
                                echo '<td>' . $kelas . '</td>';
                                echo '<td>'; 
                                if ($jk == 'L'){
                                    echo "Laki-laki";
                                } elseif ($jk == 'P') {
                                    echo "Perempuan";
                                }                                
                                echo '</td>';
                                echo '<td>' . $tempatLahir . '</td>';
                                echo '<td>' . $tanggalLahir . '</td>';
                                echo '<td>' . $agama . '</td>';
                                echo '<td>' . $alamat . '</td>';                                
                                echo '</tr>';
                                ?>
                                
                            <?php
                            }

                            // Tampilkan jumlah siswa dan jenis kelamin di bawah tabel
                            echo '</tbody>';
                            echo '</table>';
                            echo '</div>';
                            echo '<div class="card-footer">';
                            echo 'Jumlah Siswa: ' . ($i - 1) . '<br>';
                            echo 'Jumlah Laki-Laki: ' . $jumlahLakiLaki . '<br>';
                            echo 'Jumlah Perempuan: ' . $jumlahPerempuan . '<br>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>


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
</html>
