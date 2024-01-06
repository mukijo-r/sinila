<?php
require 'function.php';
require 'cek.php';
require 'config.php';
date_default_timezone_set('Asia/Jakarta');

$queryUser = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE username = '$username'");
$rowUser = mysqli_fetch_array($queryUser);
$namaUser = $rowUser['nama_lengkap']; 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Halaman Input Nilai</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav">
            <?php include 'sidebar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h2 class="mt-4">Input Data</h2>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">PROSES / Input Data / Nilai Harian</li>                            
                        </ol>                        
                        <br>
                        <div class="container-fluid px-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahNilaiMapel">
                                        Input Nilai
                                    </button>
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
                                Daftar Nilai Harian
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Tanggal</th>    
                                            <th>Tahun Ajar</th>
                                            <th>Semester</th>
                                            <th>Kelas</th>
                                            <th>Nama Siswa</th>                                            
                                            <th>Mapel</th>
                                            <th>LM</th>
                                            <th>TP</th>
                                            <th>Nilai</th>
                                            <th>Penilai</th>
                                            <th colspan='2'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $dataNilaiMapel = mysqli_query($conn, "SELECT
                                    nm.*,
                                    ta.tahun_ajar AS tahun_ajar,
                                    s.nama AS nama_siswa,
                                    s.id_kelas AS id_kelas,
                                    m.id_mapel as id_mapel,
                                    m.mapel as mapel
                                    FROM nilai_mapel nm
                                    LEFT JOIN tahun_ajar ta ON nm.id_tahun_ajar = ta.id_tahun_ajar
                                    LEFT JOIN siswa s ON nm.id_siswa = s.id_siswa
                                    LEFT JOIN mapel m ON nm.id_mapel = m.id_mapel
                                    WHERE id_kelas = $kelas
                                    ORDER BY nm.id_nm DESC;");

                                    $totalEntries = mysqli_num_rows($dataNilaiMapel);
                                    $i = $totalEntries;
                                    
                                    while($data=mysqli_fetch_array($dataNilaiMapel)){
                                        $idNilaiMapel = $data['id_nm'];                                       
                                        $tanggal = $data['tanggal'];
                                        $tanggalTampil = date("d-m-Y", strtotime($tanggal));
                                        $tahunAjar = $data['tahun_ajar'];
                                        $semester = $data['semester'];                                      
                                        $kelas = $data['kelas'];
                                        $idSiswa = $data['id_siswa'];
                                        $namaSiswa = $data['nama_siswa'];
                                        $idMapel = $data['id_mapel'];                                       
                                        $mapel = $data['mapel'];
                                        $lingkupMateri = $data['lingkup_materi'];
                                        $tujuanPembelajaran = $data['tujuan_pembelajaran'];
                                        $nilai = $data['nilai'];
                                        $guruPenilai = $data['guru_penilai'];  
                                        ?>
                                        <tr>
                                            <td><?=$i--;?></td>
                                            <td><?=$tanggalTampil;?></td>
                                            <td><?=$tahunAjar;?></td>
                                            <td><?=$semester;?></td>
                                            <td><?=$kelas;?></td>
                                            <td><?=$namaSiswa;?></td>                                            
                                            <td><?=$mapel;?></td>
                                            <td><?=$lingkupMateri;?></td>
                                            <td><?=$tujuanPembelajaran;?></td>
                                            <td><?=$nilai;?></td>
                                            <td><?=$guruPenilai;?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" name="tblEdit" data-bs-toggle="modal" data-bs-target="#modalEditNilaiSiswa<?=$idNilaiMapel;?>">Edit</button>
                                                <button type="button" class="btn btn-danger" name="hapusNilaiMapel" data-bs-toggle="modal" data-bs-target="#modalHapusNilaiSiswa<?=$idNilaiMapel;?>">Hapus</button> 
                                            </td>
                                        </tr>
                                        <!-- Modal edit Nilai Mapel Siswa -->
                                        <div class="modal fade" id="modalEditNilaiSiswa<?=$idNilaiMapel;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Edit Nilai Mapel Siswa</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <!-- Modal Body -->
                                                    <form method="post">
                                                        <div class="modal-body">
                                                        <div class="mb-3">
                                                                <label for="semester">Semester :</label><br>
                                                                <select class="form-select" name="semester" id="semester" aria-label="Semester" required>>
                                                                    <option value="<?=$semester;?>"><?=$semester;?></option>                            
                                                                    <option value="Ganjil">Ganjil</option>
                                                                    <option value="Genap">Genap</option>
                                                                </select>
                                                            </div>      
                                                            <div class="mb-3">
                                                                <label for="siswa">Siswa :</label>
                                                                <select name="siswa" class="form-select" id="siswa" aria-label="Siswa" required>>
                                                                    <option value="<?=$idSiswa;?>"><?=$namaSiswa;?></option>
                                                                    <?php
                                                                    // Ambil data siswa dari tabel siswa
                                                                    $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas");
                                                                    while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                                                        echo '<option value="' . $rowSiswa['id_siswa'] . '">' . $rowSiswa['nama'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="mapel">Mata Pelajaran :</label>
                                                                <select class="form-select" name="mapel" id="mapel" aria-label="mapel" required>
                                                                    <option value="<?=$idMapel;?>"><?=$mapel;?></option>
                                                                    <?php
                                                                    // Ambil data kelas dari tabel kelas
                                                                    $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel");
                                                                    while ($rowMapel = mysqli_fetch_assoc($queryMapel)) {
                                                                        echo '<option value="' . $rowMapel['id_mapel'] . '">' . $rowMapel['mapel'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="lingkupMateri">Lingkup Materi :</label>
                                                                <select class="form-select" name="lingkupMateri" id="lingkupMateri" aria-label="lingkupMateri" required>
                                                                    <option value="<?=$lingkupMateri;?>"><?=$lingkupMateri;?></option>                         
                                                                    <option value="LM 1">LM 1</option>
                                                                    <option value="LM 2">LM 2</option>
                                                                    <option value="LM 3">LM 3</option>
                                                                    <option value="LM 4">LM 4</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="tujuanPembelajaran">Tujuan Pembelajaran :</label>
                                                                <select class="form-select" name="tujuanPembelajaran" id="tujuanPembelajaran" aria-label="tujuanPembelajaran" required>
                                                                    <option value="<?=$tujuanPembelajaran;?>"><?=$tujuanPembelajaran;?></option>                         
                                                                    <option value="TP1">TP 1</option>
                                                                    <option value="TP2">TP 2</option>
                                                                    <option value="TP3">TP 3</option>
                                                                    <option value="TP4">TP 4</option>
                                                                    <option value="TP5">TP 5</option>
                                                                    <option value="TP6">TP 6</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="nilai">Nilai :</label>                        
                                                                <input type="number" name="nilai" value="<?=$nilai;?>" class="form-control" required max="100">                    
                                                            </div>
                                                        </div>
                                                        <!-- Modal Footer -->
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="idNilaiMapel" value="<?=$idNilaiMapel;?>">
                                                            <input type="hidden" name="namaUser" value="<?=$namaUser;?>">
                                                            <button type="submit" class="btn btn-primary" name="ubahNilaiMapel">Simpan</button>
                                                        </div><br>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <!-- Modal Hapus Nilai Mapel Siswa-->
                                    <div class="modal fade" id="modalHapusNilaiSiswa<?=$idNilaiMapel;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">Hapus Nilai Siswa ini?</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <!-- Modal body -->
                                            
                                            <form method="post">
                                            <div class="modal-body">
                                            <h5>Anda yakin ingin menghapus data nilai <b><?=$nilai;?></b> atas nama <u><?=$namaSiswa;?></u>?</h5>
                                                
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="idNilaiMapel" value="<?=$idNilaiMapel;?>">
                                                <button type="submit" class="btn btn-danger" name="hapusNilaiMapel">Hapus</button> 
                                            </div>
                                            <br> 
                                            </form>       
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
    </body>   

    <!-- Modal Tambah Nilai Siswa -->
<div class="modal fade bd-example-modal-lg" id="modalTambahNilaiMapel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Nilai Harian Siswa kelas <?=$kelas;?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal Body -->
            <form method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="semester">Semester :</label><br>
                                <select class="form-select" name="semester" id="semester" aria-label="Semester" required>
                                    <option selected>Pilih semester</option>
                                    <option value="Ganjil">Ganjil</option>
                                    <option value="Genap">Genap</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mapel">Mata Pelajaran :</label>
                                <select class="form-select" name="mapel" id="mapel" aria-label="mapel" required>
                                    <option selected disabled>Pilih Mapel</option>
                                    <?php
                                    // Ambil data kelas dari tabel kelas
                                    $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel");
                                    while ($rowMapel = mysqli_fetch_assoc($queryMapel)) {
                                        echo '<option value="' . $rowMapel['id_mapel'] . '">' . $rowMapel['mapel'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="lingkupMateri">Lingkup Materi :</label>
                                <select class="form-select" name="lingkupMateri" id="lingkupMateri" aria-label="lingkupMateri" required>
                                    <option selected disabled>Pilih LM</option>
                                    <option value="LM 1">LM 1</option>
                                    <option value="LM 2">LM 2</option>
                                    <option value="LM 3">LM 3</option>
                                    <option value="LM 4">LM 4</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tujuanPembelajaran">Tujuan Pembelajaran :</label>
                                <select class="form-select" name="tujuanPembelajaran" id="tujuanPembelajaran" aria-label="tujuanPembelajaran" required>
                                    <option selected disabled>Pilih TP</option>
                                    <option value="TP1">TP 1</option>
                                    <option value="TP2">TP 2</option>
                                    <option value="TP3">TP 3</option>
                                    <option value="TP4">TP 4</option>
                                    <option value="TP5">TP 5</option>
                                    <option value="TP6">TP 6</option>
                                </select>
                            </div>
                        </div>
                    </div>                    

                    <!-- Displaying a list of students with input fields for grades -->
                    <div class="mb-3">
                        <label for="siswa">Nilai Siswa :</label>
                        <?php
                        // Ambil data siswa dari tabel siswa
                        $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas");
                        
                        // Check if there are students
                        if (mysqli_num_rows($querySiswa) > 0) {
                            $counter = 1; // Initialize a counter variable

                            while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                // Display each student's name and sequential number
                                echo '<div class="row mb-3">';
                                echo '<div class="col-md-1">' . $counter . '.</div>';
                                echo '<div class="col-md-6">' . $rowSiswa['nama'] . '</div>';
                                
                                // Input field for each student's grade
                                echo '<div class="col-md-2">';
                                echo '<input type="number" name="nilai_' . $rowSiswa['id_siswa'] . '" class="form-control" required max="99999">';
                                echo '</div>';
                                
                                echo '</div>';

                                $counter++; // Increment the counter
                            }
                        } else {
                            echo '<p>No students found.</p>';
                        }
                        ?>
                    </div>

                    <div class="text-center">
                        <input type="hidden" name="namaUser" value="<?=$namaUser;?>">
                        <button type="submit" class="btn btn-primary" name="tambahNilaiMapel">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
        // Mendapatkan nilai input
    const nilai = document.getElementById("nilai").value;

    // Memeriksa apakah nilai lebih dari 100
    if (nilai > 100) {
    // Menampilkan pesan error
    alert("Nilai maksimal adalah 100");

    // Mengatur nilai input ke 100
    document.getElementById("nilai").value = 100;
    }

    </script>

</html>
