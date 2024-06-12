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
                            <li class="breadcrumb-item active">PROSES / Input Data / Nilai Ulangan</li>                            
                        </ol>                        
                        <br>
                        <div class="container-fluid px-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahNilaiUlangan">
                                        Input Nilai
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahNilaiUlanganRemed">
                                        Remedial
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
                                Daftar Nilai Ulangan
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
                                            <th>Nilai</th>
                                            <th>Penilai</th>
                                            <th colspan='2'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $dataNilaiUlangan = mysqli_query($conn, "SELECT
                                    nul.*,
                                    ta.tahun_ajar AS tahun_ajar,
                                    s.nama AS nama_siswa,
                                    s.id_kelas AS id_kelas,
                                    m.id_mapel as id_mapel,
                                    m.mapel as mapel
                                    FROM nilai_ulangan nul
                                    LEFT JOIN tahun_ajar ta ON nul.id_tahun_ajar = ta.id_tahun_ajar
                                    LEFT JOIN siswa s ON nul.id_siswa = s.id_siswa
                                    LEFT JOIN mapel m ON nul.id_mapel = m.id_mapel
                                    WHERE id_kelas = $kelas
                                    AND nul.id_tahun_ajar = '$idTahunAjar'
                                    ORDER BY nul.id_ul DESC;");

                                    $totalEntries = mysqli_num_rows($dataNilaiUlangan);
                                    $i = $totalEntries;
                                    
                                    while($data=mysqli_fetch_array($dataNilaiUlangan)){
                                        $idNilaiUlangan = $data['id_ul'];                                       
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
                                            <td><?=$nilai;?></td>
                                            <td><?=$guruPenilai;?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" name="tblEdit" data-bs-toggle="modal" data-bs-target="#modalEditNilaiSiswa<?=$idNilaiUlangan;?>">Edit</button>
                                                <button type="button" class="btn btn-danger" name="hapusNilaiUlangan" data-bs-toggle="modal" data-bs-target="#modalHapusNilaiSiswa<?=$idNilaiUlangan;?>">Hapus</button> 
                                            </td>
                                        </tr>
                                        <!-- Modal edit Nilai Ulangan Siswa -->
                                        <div class="modal fade" id="modalEditNilaiSiswa<?=$idNilaiUlangan;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Edit Nilai Ulangan Siswa</h4>
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
                                                                    if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                                                                        $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel WHERE mapel <> 'Bahasa Using';");
                                                                    } else {
                                                                        $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel");
                                                                    }
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
                                                                <label for="nilai">Nilai :</label>                        
                                                                <input type="number" name="nilai" value="<?=$nilai;?>" class="form-control" required max="100">                    
                                                            </div>
                                                        </div>
                                                        <!-- Modal Footer -->
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="idNilaiUlangan" value="<?=$idNilaiUlangan;?>">
                                                            <input type="hidden" name="namaUser" value="<?=$namaUser;?>">
                                                            <button type="submit" class="btn btn-primary" name="ubahNilaiUlangan">Simpan</button>
                                                        </div><br>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <!-- Modal Hapus Nilai Ulangan Siswa-->
                                    <div class="modal fade" id="modalHapusNilaiSiswa<?=$idNilaiUlangan;?>">
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
                                                <input type="hidden" name="idNilaiUlangan" value="<?=$idNilaiUlangan;?>">
                                                <button type="submit" class="btn btn-danger" name="hapusNilaiUlangan">Hapus</button> 
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
    <div class="modal fade bd-example-modal-lg" id="modalTambahNilaiUlangan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Nilai Ulangan Siswa kelas <?=$kelas;?></h4>
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
                                        <option value="">Pilih semester</option>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mapel">Mata Pelajaran :</label>
                                    <select class="form-select" name="mapel" id="mapel" aria-label="mapel" required>
                                        <option value="">Pilih Mapel</option>
                                        <?php
                                        // Ambil data kelas dari tabel kelas
                                        if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                                            $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel WHERE mapel <> 'Bahasa Using';");
                                        } else {
                                            $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel");
                                        }
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
                                        <option value="">Pilih LM</option>
                                        <option value="LM 1">LM 1</option>
                                        <option value="LM 2">LM 2</option>
                                        <option value="LM 3">LM 3</option>
                                        <option value="LM 4">LM 4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">                                
                            </div>
                        </div>                    

                        <!-- Displaying a list of students with input fields for grades -->
                        <div class="mb-3">
                            <label for="siswa">Nilai Siswa :</label>
                            <?php
                            // Ambil data siswa dari tabel siswa
                            $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas ORDER BY nama");
                            
                            // Check if there are students
                            if (mysqli_num_rows($querySiswa) > 0) {
                                $counter = 1; // Initialize a counter variable

                                while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                    // Display each student's name and sequential number
                                    echo '<div class="row mb-3">';
                                    echo '<div class="col-md-6">' . $counter . '.  ' . $rowSiswa['nama'] . '</div>';
                                    
                                    // Input field for each student's grade
                                    echo '<div class="col-md-2">';
                                    echo '<input type="number" name="nilai_' . $rowSiswa['id_siswa'] . '" class="form-control" required max="100">';
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
                            <button type="submit" class="btn btn-primary" name="tambahNilaiUlangan">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Perbaiki Nilai Siswa -->
    <div class="modal fade bd-example-modal-lg" id="modalTambahNilaiUlanganRemed" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Perbaiki Nilai Ulangan Siswa kelas <?=$kelas;?></h4>
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
                                        <option value="">Pilih semester</option>
                                        <option value="Ganjil">Ganjil</option>
                                        <option value="Genap">Genap</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mapel">Mata Pelajaran :</label>
                                    <select class="form-select" name="mapel" id="mapel" aria-label="mapel" required>
                                        <option value="">Pilih Mapel</option>
                                        <?php
                                        // Ambil data kelas dari tabel kelas
                                        if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                                            $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel WHERE mapel <> 'Bahasa Using';");
                                        } else {
                                            $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel");
                                        }
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
                                        <option value="">Pilih LM</option>
                                        <option value="LM 1">LM 1</option>
                                        <option value="LM 2">LM 2</option>
                                        <option value="LM 3">LM 3</option>
                                        <option value="LM 4">LM 4</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="siswa">Siswa :</label>
                                    <select class="form-select" name="siswa" id="siswa" aria-label="siswa" required>
                                        <option value="">Pilih Siswa</option>
                                        <?php
                                        // Ambil data kelas dari tabel kelas
                                        $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas ORDER BY nama");
                                        while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                            echo '<option value="' . $rowSiswa['id_siswa'] . '">' . $rowSiswa['nama'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>                              
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <input type="number" name="nilai" class="form-control" required max="100">
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="text-center">
                                <input type="hidden" name="namaUser" value="<?=$namaUser;?>">
                                <button type="submit" class="btn btn-primary" name="tambahNilaiUlanganRemed">Simpan</button>
                            </div>
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
