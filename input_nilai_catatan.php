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
                            <li class="breadcrumb-item active">PROSES / Input Data / Nilai Catatan</li>                            
                        </ol>
                        <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Catatan Siswa</a>                            
                        </li>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="input_nilai_catatan_ekstrakurikuler.php">Catatan Ekstrakurikuler</a>
                        </li>
                        </ul>
                        <br><br>
                        <div class="container-fluid px-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahNilaiCatatan">
                                        Catatan Baru
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
                        <?php 
                        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar='$tahunAjar'");
                        while ($rowTahunAjar = mysqli_fetch_assoc($queryTahunAjar)) {                            
                            $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];
                        }
                        ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Daftar Nilai Catatan
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
                                            <th>Catatan</th>
                                            <th>Penilai</th>
                                            <th colspan='2'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $dataNilaiCatatan = mysqli_query($conn, "SELECT
                                    nc.*,
                                    ta.tahun_ajar AS tahun_ajar,
                                    s.nama AS nama_siswa,
                                    s.id_kelas AS id_kelas
                                    FROM nilai_catatan nc
                                    LEFT JOIN tahun_ajar ta ON nc.id_tahun_ajar = ta.id_tahun_ajar
                                    LEFT JOIN siswa s ON nc.id_siswa = s.id_siswa
                                    WHERE id_kelas = $kelas
                                    AND nc.id_tahun_ajar = '$idTahunAjar'
                                    ORDER BY nc.id_nc DESC;");

                                    $totalEntries = mysqli_num_rows($dataNilaiCatatan);
                                    $i = $totalEntries;
                                    
                                    while($data=mysqli_fetch_array($dataNilaiCatatan)){
                                        $idNilaiCatatan = $data['id_nc'];                                       
                                        $tanggal = $data['tanggal'];
                                        $tanggalTampil = date("d-m-Y", strtotime($tanggal));
                                        $tahunAjar = $data['tahun_ajar'];
                                        $semester = $data['semester'];                                      
                                        $kelas = $data['kelas'];
                                        $idSiswa = $data['id_siswa'];
                                        $namaSiswa = $data['nama_siswa'];                                  
                                        $catatan = $data['catatan'];
                                        $guruPenilai = $data['guru_penilai'];  
                                        ?>
                                        <tr>
                                            <td><?=$i--;?></td>
                                            <td><?=$tanggalTampil;?></td>
                                            <td><?=$tahunAjar;?></td>
                                            <td><?=$semester;?></td>
                                            <td><?=$kelas;?></td>
                                            <td><?=$namaSiswa;?></td>                                            
                                            <td><?=$catatan;?></td>
                                            <td><?=$guruPenilai;?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" name="tblEdit" data-bs-toggle="modal" data-bs-target="#modalEditNilaiCatatan<?=$idNilaiCatatan;?>">Edit</button>
                                                <button type="button" class="btn btn-danger" name="tblHapus" data-bs-toggle="modal" data-bs-target="#modalHapusNilaiCatatan<?=$idNilaiCatatan;?>">Hapus</button> 
                                            </td>
                                        </tr>
                                        <!-- Modal edit Nilai Catatan Siswa -->
                                        <div class="modal fade" id="modalEditNilaiCatatan<?=$idNilaiCatatan;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Nilai Catatan Siswa kelas <?=$kelas;?></h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <!-- Modal Body -->
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="semester">Semester :</label><br>
                                                                <select class="form-select" name="semester" id="semester" aria-label="Semester" required>
                                                                    <option value="<?=$semester;?>"><?=$semester;?></option>                            
                                                                    <option value="Ganjil">Ganjil</option>
                                                                    <option value="Genap">Genap</option>
                                                                </select>
                                                            </div>      
                                                            <div class="mb-3">
                                                                <label for="siswa">Siswa :</label>
                                                                <select name="siswa" class="form-select" id="siswa" aria-label="Siswa" required>
                                                                    <option value="<?=$idSiswa;?>"><?=$namaSiswa;?></option>
                                                                    <?php
                                                                    // Ambil data siswa dari tabel siswa
                                                                    $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas ORDER BY nama");
                                                                    while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                                                        echo '<option value="' . $rowSiswa['id_siswa'] . '">' . $rowSiswa['nama'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>                        
                                                            <div class="mb-3">
                                                                <label for="catatan">Catatan :</label>
                                                                <input type="text" name="catatan" id="catatan" value="<?=$catatan;?>" class="form-control" required>
                                                            </div>
                                                            <div class="text-center">
                                                                <input type="hidden" name="idNilaiCatatan" value="<?=$idNilaiCatatan;?>">
                                                                <input type="hidden" name="namaUser" value="<?=$namaUser;?>">
                                                                <button type="submit" class="btn btn-primary" name="ubahNilaiCatatan">Simpan</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Hapus Nilai Catatan Siswa-->
                                        <div class="modal fade" id="modalHapusNilaiCatatan<?=$idNilaiCatatan;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">

                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Hapus Nilai Catatan Siswa ini?</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <!-- Modal body -->                                            
                                                <form method="post">
                                                    <div class="modal-body">
                                                    <h5>Anda yakin ingin menghapus data catatan di bawah atas nama <u><?=$namaSiswa;?></u>?</h5>
                                                    <br>
                                                    <h5>"<i><?=$catatan;?></i>"</h5>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="idNilaiCatatan" value="<?=$idNilaiCatatan;?>">
                                                        <button type="submit" class="btn btn-danger" name="hapusNilaiCatatan">Hapus</button> 
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

    <!-- Modal Tambah Nilai Catatan Siswa -->
    <div class="modal fade bd-example-modal-lg" id="modalTambahNilaiCatatan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Nilai Catatan Siswa kelas <?=$kelas;?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal Body -->
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="semester">Semester :</label><br>
                            <select class="form-select" name="semester" id="semester" aria-label="Semester" required>
                                <option value="">Pilih semester</option>                            
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>      
                        <div class="mb-3">
                            <label for="siswa">Catatan Siswa :</label>
                            <?php
                            // Ambil data siswa dari tabel siswa
                            $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas ORDER BY nama");
                            
                            // Check if there are students
                            if (mysqli_num_rows($querySiswa) > 0) {
                                $counter = 1; // Initialize a counter variable

                                while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                    // Display each student's name and sequential number
                                    echo '<div class="row mb-3">';
                                    echo '<div class="col-md-6">' . $counter . '.  ' . $rowSiswa['nama'] . ' :</div><br>';
                                    
                                    // Input field for each student's grade
                                    echo '<div class="col-md-12">';
                                    echo '<input type="text" name="nilai_' . $rowSiswa['id_siswa'] . '" class="form-control" required max="99999">';
                                    echo '</div>';
                                    
                                    echo '</div>';

                                    $counter++; 
                                }
                            } else {
                                echo '<p>No students found.</p>';
                            }
                            ?>
                        </div>
                        <div class="text-center">
                        <input type="hidden" name="namaUser" value="<?=$namaUser;?>">
                            <button type="submit" class="btn btn-primary" name="tambahNilaiCatatan">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</html>
