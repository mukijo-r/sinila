<?php
require 'function.php';
require 'cek.php';
require 'config.php';
date_default_timezone_set('Asia/Jakarta');
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
                            <li class="breadcrumb-item active">PROSES / Input Data / Nilai Kepribadian</li>                            
                        </ol>                        
                        <br>
                        <div class="container-fluid px-4">
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahNilaiKepribadian">
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
                                Daftar Nilai Kepribadian
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
                                            <th>Kategori</th>
                                            <th>Nilai</th>
                                            <th>Penilai</th>
                                            <th colspan='2'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    $dataNilaiKepribadian = mysqli_query($conn, "SELECT
                                    nk.*,
                                    ta.tahun_ajar AS tahun_ajar,
                                    s.nama AS nama_siswa,
                                    s.id_kelas AS id_kelas
                                    FROM nilai_kepribadian nk
                                    LEFT JOIN tahun_ajar ta ON nk.id_tahun_ajar = ta.id_tahun_ajar
                                    LEFT JOIN siswa s ON nk.id_siswa = s.id_siswa
                                    ORDER BY nk.tanggal DESC;");

                                    $totalEntries = mysqli_num_rows($dataNilaiKepribadian);
                                    $i = $totalEntries;
                                    
                                    while($data=mysqli_fetch_array($dataNilaiKepribadian)){
                                        $idNilaiKepribadian = $data['id_nk'];                                       
                                        $tanggal = $data['tanggal'];
                                        $tanggalTampil = date("d-m-Y", strtotime($tanggal));
                                        $tahunAjar = $data['tahun_ajar'];
                                        $semester = $data['semester'];                                      
                                        $kelas = $data['kelas'];
                                        $idSiswa = $data['id_siswa'];
                                        $namaSiswa = $data['nama_siswa'];                                  
                                        $kategoriKepribadian = $data['kategori_kepribadian'];
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
                                            <td><?=$kategoriKepribadian;?></td>
                                            <td><?=$nilai;?></td>
                                            <td><?=$guruPenilai;?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" name="tblEdit" data-bs-toggle="modal" data-bs-target="#modalEditNilaiKepribadian<?=$idNilaiKepribadian;?>">Edit</button>
                                                <button type="button" class="btn btn-danger" name="tblHapus" data-bs-toggle="modal" data-bs-target="#modalHapusNilaiKepribadian<?=$idNilaiKepribadian;?>">Hapus</button> 
                                            </td>
                                        </tr>
                                        <!-- Modal edit Nilai Kepribadian Siswa -->
                                        <div class="modal fade" id="modalEditNilaiKepribadian<?=$idNilaiKepribadian;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Edit Nilai Kepribadian Siswa</h4>
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
                                                                <label for="kategoriKepribadian">Kategori Kepribadian :</label>
                                                                <select class="form-select" name="kategoriKepribadian" id="kategoriKepribadian" aria-label="kategoriKepribadian" required>
                                                                    <option value="<?=$kategoriKepribadian;?>"><?=$kategoriKepribadian;?></option>                          
                                                                    <option value="Seni Tari">Seni Tari</option>
                                                                    <option value="Seni Rupa">Seni Rupa</option>
                                                                    <option value="Seni Teater">Seni Teater</option>
                                                                    <option value="Seni Musik">Seni Musik</option>
                                                                </select>
                                                            </div>                                                            
                                                            <div class="mb-3">
                                                                <label for="nilai">Nilai :</label>   
                                                                <select class="form-select" name="nilai" id="nilai" aria-label="nilai" required>
                                                                    <option value="<?=$nilai;?>"><?=$nilai;?></option>                         
                                                                    <option value="A">A</option>
                                                                    <option value="B">B</option>
                                                                    <option value="C">C</option>
                                                                    <option value="D">D</option>
                                                                    <option value="E">E</option>
                                                                </select>                    
                                                            </div>
                                                        </div>
                                                        <!-- Modal Footer -->
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="idNilaiKepribadian" value="<?=$idNilaiKepribadian;?>">
                                                            <button type="submit" class="btn btn-primary" name="ubahNilaiKepribadian">Simpan</button>
                                                        </div><br>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    <!-- Modal Hapus Nilai Kepribadian Siswa-->
                                    <div class="modal fade" id="modalHapusNilaiKepribadian<?=$idNilaiKepribadian;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">Hapus Nilai Kepribadian Siswa ini?</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <!-- Modal body -->                                            
                                            <form method="post">
                                            <div class="modal-body">
                                            <h5>Anda yakin ingin menghapus data nilai <b><?=$nilai;?></b> atas nama <u><?=$namaSiswa;?></u>?</h5>
                                                
                                            </div>
                                            <div class="modal-footer">
                                                <input type="hidden" name="idNilaiKepribadian" value="<?=$idNilaiKepribadian;?>">
                                                <button type="submit" class="btn btn-danger" name="hapusNilaiKepribadian">Hapus</button> 
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

    <!-- Modal Tambah Nilai Kepribadian Siswa -->
    <div class="modal fade" id="modalTambahNilaiKepribadian">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Nilai Kepribadian Siswa kelas <?=$kelas;?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal Body -->
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="semester">Semester :</label><br>
                            <select class="form-select" name="semester" id="semester" aria-label="Semester" required>>
                                <option selected>Pilih semester</option>                            
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>      
                        <div class="mb-3">
                            <label for="siswa">Siswa :</label>
                            <select name="siswa" class="form-select" id="siswa" aria-label="Siswa" required>>
                                <option selected disabled>Pilih Siswa</option>
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
                            <label for="kategoriKepribadian">Kategori Kepribadian :</label>
                            <select class="form-select" name="kategoriKepribadian" id="kategoriKepribadian" aria-label="kategoriKepribadian" required>
                                <option selected disabled>Pilih Kategori</option>                         
                                <option value="Jujur">Jujur</option>
                                <option value="Disiplin">Disiplin</option>
                                <option value="Tanggung Jawab">Tanggung Jawab</option>
                                <option value="Santun">Santun</option>
                                <option value="Percaya Diri">Percaya Diri</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nilai">Nilai :</label>                        
                            <select class="form-select" name="nilai" id="nilai" aria-label="nilai" required>
                                <option selected disabled>Pilih Nilai</option>                         
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>                 
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="tambahNilaiKepribadian">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</html>
