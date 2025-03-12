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
                                <?php
                                    if (isset($_SESSION['flash_message'])) {
                                        $message_class = isset($_SESSION['flash_message_class']) ? $_SESSION['flash_message_class'] : 'alert-success';
                                        echo '<div class="alert ' . $message_class . ' text-center">' . $_SESSION['flash_message'] . '</div>';
                                        unset($_SESSION['flash_message']); // Hapus pesan flash setelah ditampilkan
                                    }                                    
                                    ?>
                                </div>
                            </div>
                            
                        </div><br>

                        <?php   
                            if ($role == 'admin') {
                                echo '
                                <div class="container-fluid px-2">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                                                Tambah Siswa
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
                                                Import Excel
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalNaikKelas">
                                                Naikkan Siswa
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalLulus">
                                                Luluskan Siswa
                                            </button>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalTarikSiswa">
                                                Masukkan Siswa Lama
                                            </button>
                                        </div>
                                    </div>
                                </div><br>';
                            }
                        
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
                            echo '<th>Aksi</th>';
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
                                $nipd = $data['nis'];
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
                                echo '<td>';
                                echo '<button type="button" class="btn btn-warning" name="tblEdit" data-bs-toggle="modal" data-bs-target="#modalEditSiswa' . $ids . '">Edit</button>';
                                echo '<input type="hidden" name="idsis" value="' . $ids . '">';
                                //echo '<button type="button" class="btn btn-danger" name="tblHapus" data-bs-toggle="modal" data-bs-target="#modalHapusSiswa' . $ids . '">Hapus</button>';
                                echo '</td>';                              
                                echo '</tr>';
                                ?>
                                <!-- Modal Edit Siswa-->
                                <div class="modal fade" id="modalEditSiswa<?=$ids;?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                            <!-- Modal Header -->
                                            <div class="modal-header">
                                                <h4 class="modal-title">Edit Siswa</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <!-- Modal body -->
                                            
                                            <form method="post">
                                            <div class="modal-body">
                                                <label for="nipd">NIPD :</label>  
                                                <input type="text" name="nipd" value="<?=$nipd;?>" class="form-control">
                                                <br>
                                                <label for="nisn">NISN :</label>  
                                                <input type="text" name="nisn" value="<?=$nisn;?>" class="form-control">
                                                <br>
                                                <label for="namaSiswa">Nama siswa :</label> 
                                                <input type="text" name="namaSiswa" value="<?=$namaSiswa;?>" class="form-control">
                                                <br>
                                                <label for="kelas">Kelas :</label> 
                                                <select class="form-select" name="kelas" aria-label="Pilih Kelas">
                                                    <option selected><?=$kelas;?></option>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                </select>
                                                <br>
                                                <label for="jk">Jenis kelamin :</label> 
                                                <select class="form-select" name="jk" aria-label="Jenis Kelamin">
                                                    <option value="<?=$jk;?>"><?php if ($jk == 'L'){ echo "Laki-laki"; } elseif ($jk == 'P') { echo "Perempuan"; } ?></option>
                                                    <option value="L">Laki-laki</option>
                                                    <option value="P">Perempuan</option>
                                                </select>
                                                <br>
                                                <label for="tempatLahir">Tempat lahir :</label> 
                                                <input type="text" name="tempatLahir" value="<?=$tempatLahir;?>" class="form-control">
                                                <br>
                                                <label for="tanggalLahir">Tanggal lahir :</label> 
                                                <input type="date" name="tanggalLahir" value="<?=$tanggalLahir;?>" class="form-control">
                                                <br>
                                                <label for="agama">Agama :</label> 
                                                <select class="form-select" name="agama" aria-label="Agama">
                                                    <option selected><?=$agama;?></option>
                                                    <option value="Katolik">Katolik</option>
                                                    <option value="Kristen">Kristen</option>
                                                    <option value="Islam">Islam</option>
                                                    <option value="Hindu">Hindu</option>
                                                    <option value="Buddha">Buddha</option>
                                                    <option value="Khonghucu">Khonghucu</option>
                                                </select>
                                                <br>
                                                <label for="alamat">Alamat :</label><br> 
                                                <textarea name="alamat" rows="3" cols="45"><?=$alamat;?></textarea>
                                                <br><br>
                                                <label for="status">Status :</label> 
                                                <select class="form-select" name="status" aria-label="Agama">
                                                    <option value="<?=$status;?>"><?=$status;?></option>
                                                    <option value="aktif">Aktif</option>
                                                    <option value="drop out">Drop out</option>
                                                </select>
                                                <input type="hidden" name="ids" value="<?=$ids;?>">                                                
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-warning" name="editSiswa">Edit</button> 
                                            </div>
                                            <br> 
                                            </form>        
                                            </div>
                                        </div>
                                    </div>
                                
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
    <!-- Modal Tambah Siswa-->
    <div class="modal fade" id="modalTambahSiswa">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Siswa</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->      
                    <form method="post">
                    <div class="modal-body">
                        <input type="text" name="nipd" placeholder="NIPD" class="form-control">
                        <br>
                        <input type="text" name="nisn" placeholder="NISN" class="form-control">
                        <br>
                        <input type="text" name="namaSiswa" placeholder="Nama Siswa" class="form-control" required>
                        <br>
                        <select class="form-select" name="kelas" aria-label="Pilih Kelas">
                            <option selected>Kelas</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                        </select>
                        <br>
                        <select class="form-select" name="jk" aria-label="Jenis Kelamin">
                            <option selected>Jenis Kelamin</option>
                            <option value="L">L</option>
                            <option value="P">P</option>
                        </select>
                        <br>
                        <input type="text" name="tempatLahir" placeholder="Tempat Lahir" class="form-control">
                        <br>
                        <input type="date" name="tanggalLahir" placeholder="Tanggal Lahir" class="form-control">
                        <br>
                        <select class="form-select" name="agama" aria-label="Agama">
                            <option selected>Agama</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Islam">Islam</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Khonghucu">Khonghucu</option>
                        </select>
                        <br>
                        <textarea name="alamat" rows="5" cols="45">Alamat</textarea>                    
                        <br>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success" name="tambahSiswa">Tambah</button> 
                    </div>
                    <br> 
                </form>   
                </div>
            </div>
        </div>

        <!-- Modal Import Excel-->
        <div class="modal fade" id="modalImportExcel">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">Import Excel</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <!-- Modal body -->      
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                        <div class="mb-3">
                        <label for="formFile" class="form-label">Pilih file Excel yang akan diimport</label>
                        <input type="file" name="formFile" id="formFile" class="form-control">
                        </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success" name="importExcel">Import</button> 
                        </div>
                        <br> 
                    </form>
                    <div class="container-fluid px-4">
                        <h6>Siapkan file Excel (.xls, .xlsx) dengan format seperti di bawah :</h6>
                        <h6>Format tanggal : yyyy-mm-dd</h6>
                        <h6>Format jenis kelamin : L/P</h6>
                            <table class="table table-bordered">
                                <tr> 
                                    <th>Nama</th>
                                    <th>Kelas</th>                                                       
                                    <th>Jenis Kelamin</th>
                                    <th>NIPD</th>
                                    <th>NISN</th> 
                                    <th>Tampat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Agama</th>
                                    <th>Alamat</th>
                                </tr>
                            </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Menaikkan Siswa-->
        <div class="modal fade" id="modalNaikKelas">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Naikkan Siswa</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->      
                    <form method="post">
                    <div class="modal-body">
                        <h6>Semua siswa kelas 1 - 5 akan naik satu tingkat sekaligus.</h6>
                        <h6>Apabila terdapat siswa yang tidak naik kelas, ubah kelas secara individu melalui menu edit.</h6>
                        <h6>Sebelum menjalankan proses ini, <u><b>pastikan siswa kelas 6 sudah diluluskan.</b></u></h6>  
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-warning" name="naikkanSiswa">Naikkan</button> 
                    </div>
                    <br> 
                </form>   
                </div>
            </div>
        </div>

        <!-- Modal Meluluskan Siswa-->
        <div class="modal fade" id="modalLulus">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Luluskan Siswa</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->      
                    <form method="post">
                    <div class="modal-body">
                        <h6>Meluluskan semua siswa kelas 6 sekaligus.</h6>
                        <h6>Siswa yang sudah diluluskan tidak dapat dikembalikan. 
                            Apabila terdapat siswa yang tidak lulus, turunkan ke kelas 5 secara individu melalui menu edit sebelum menjalankan proses ini.</h6>  
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-danger" name="luluskanSiswa">Luluskan</button> 
                    </div>
                    <br> 
                </form>   
                </div>
            </div>
        </div>    

        <!-- Modal Memasukkan Siswa Lama-->
        <div class="modal fade" id="modalTarikSiswa">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambahkan Siswa Lama</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->      
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama">Nama :</label>
                            <select class="form-select" name="idSiswa" id="idSiswa" aria-label="Nama">
                                <option selected disabled>Pilih Nama</option>
                                <?php
                                // Ambil data nama dari tabel alumni
                                $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = 404 AND `status` = 'drop out'");
                                while ($siswa = mysqli_fetch_assoc($querySiswa)) {
                                    echo '<option value="' . $siswa['id_siswa'] . '">' . $siswa['nama'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="idKelas">Masukkan ke kelas :</label>
                            <select class="form-select" name="idKelas" id="idKelas" aria-label="Kelas">
                                <option selected>Kelas</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success" name="tarikSiswa">Tarik</button> 
                    </div>
                    <br> 
                </form>   
                </div>
            </div>
        </div>    
    </div>
</html>
