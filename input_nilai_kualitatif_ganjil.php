<?php
require 'function.php';
require 'cek.php';
require 'config.php';
date_default_timezone_set('Asia/Jakarta');

$queryUser = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE username = '$username'");
$rowUser = mysqli_fetch_array($queryUser);
$namaUser = $rowUser['nama_lengkap']; 
$semester = 'Ganjil';
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
                            <li class="breadcrumb-item active">PROSES / Input Data / Nilai Asesmen Semester Ganjil</li>                            
                        </ol>                        
                        <br>
                        <div class="container-fluid px-4">
                            <div class="row">
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahDeskripsi">
                                        Deskripsi Baru
                                    </button>
                                </div>
                                <div class="col-md-1"><a href="capaian_kompetensi_siswa_ganjil.php">
                                    <button type="button" class="btn btn-primary">
                                        Asesmen Siswa
                                    </button></a>
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
                                Daftar Asesmen kelas <?= $kelas ;?>
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple1" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="line-height: 2; width: 3%">No.</th>
                                            <th style="line-height: 2; width: 25%">Mata Pelajaran</th>
                                            <th style="line-height: 2; width: 50%">Deskripsi</th>
                                            <th colspan='2' style="line-height: 2; width: 10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $groupedDescriptions = [];
                                    $sequenceNumbers = []; 
                                    $dataAsesmen = mysqli_query($conn, "SELECT 
                                    id_asesmen,
                                    a.id_mapel,
                                    deskripsi,
                                    mapel
                                    FROM 
                                    asesmen a
                                    LEFT JOIN mapel m ON a.id_mapel = m.id_mapel                                    
                                    WHERE 
                                    semester = '$semester' AND
                                    id_kelas = '$kelas'
                                    ORDER BY mapel, id_asesmen;");   
                                    
                                    while($data=mysqli_fetch_array($dataAsesmen)){
                                        $idAsesemen = $data['id_asesmen'];                                       
                                        $idMapel = $data['id_mapel'];
                                        $mapel = $data['mapel'];
                                        $deskripsi = $data['deskripsi'];
                                        $deskripsi = ucfirst($deskripsi);

                                        if (!isset($groupedDescriptions[$idMapel])) {
                                            $groupedDescriptions[$idMapel] = ['mapel' => $mapel, 'descriptions' => []];
                                            $sequenceNumbers[$idMapel] = 1;
                                        }
                                    
                                        // Menambahkan deskripsi ke array
                                        $groupedDescriptions[$idMapel]['descriptions'][] = $deskripsi;
                                        
                                    }
                                        
                                    foreach ($groupedDescriptions as $group) {
                                        $namaMapel = $group['mapel'];
                                        
                                        $queryMapel = mysqli_query($conn, "SELECT id_mapel
                                        FROM mapel                  
                                        WHERE                                         
                                        `mapel` = '$namaMapel';");

                                        while ($rowMapel = mysqli_fetch_array($queryMapel)){
                                        $id_mapel = $rowMapel['id_mapel'];
                                        }
                                        echo '<tr>';
                                        echo '<td>' . $sequenceNumbers[$idMapel]++ . '</td>';
                                        echo '<td>' . $namaMapel . '</td>';
                                        
                                        // Menggabungkan deskripsi menjadi satu paragraf
                                        $combinedDescription = implode('. ', array_map('ucfirst', $group['descriptions']));
                                        echo '<td>' . $combinedDescription . '.</td>';                                        
                                        echo '<td>
                                            <button type="button" class="btn btn-warning" name="editAsesmen" data-bs-toggle="modal" data-bs-target="#modalEditAsesmen'. $id_mapel . '">Edit</button>
                                            <button type="button" class="btn btn-danger" name="hapusAsesmen" data-bs-toggle="modal" data-bs-target="#modalHapusAsesmen'. $id_mapel . '">Hapus</button> 
                                        </td>
                                        </tr>
                                    ';
                                    
                                    ?>
                                        <!-- Modal edit Deskripsi -->
                                    <div class="modal fade bd-example-modal-lg" id="modalEditAsesmen<?=$id_mapel;?>" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Edit Deskripsi</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <!-- Modal Body -->
                                                <form method="post">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <h5>Mata Pelajaran : <?=$namaMapel;?></h5>
                                                        </div>
                                                        <div class="mb-3">
                                                        <?php
                                                        // Ambil data deskripsi
                                                        $queryDeskripsi = mysqli_query($conn, "SELECT id_asesmen, deskripsi FROM asesmen WHERE id_mapel = $id_mapel AND id_kelas = $kelas AND semester = '$semester';");
                                                        
                                                        // Check if there are students
                                                        if (mysqli_num_rows($queryDeskripsi) > 0) {
                                                            $counter = 1; // Initialize a counter variable

                                                            while ($rowDeskripsi = mysqli_fetch_assoc($queryDeskripsi)) {
                                                                echo '<div class="row mb-3">';
                                                                echo '<div class="col-md-6"> Deskripsi ' . $counter . ' :</div><br>';
                                                                
                                                                echo '<div class="col-md-12">';
                                                                echo '<input type="text" name="deskripsi_' . $rowDeskripsi['id_asesmen']  . '" value = "' . $rowDeskripsi['deskripsi'] . '" class="form-control" required max="99999">';
                                                                echo '</div>';
                                                                
                                                                echo '</div>';

                                                                $counter++; 
                                                            }
                                                        } else {
                                                            echo '<p>Deskripsi tidak ditemukan.</p>';
                                                        }
                                                        ?>
                                                        </div>
                                                    </div>
                                                    <!-- Modal Footer -->
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="semester" value="<?=$semester;?>">
                                                        <input type="hidden" name="kelas" value="<?=$kelas;?>">
                                                        <input type="hidden" name="idMapel" value="<?=$id_mapel;?>">
                                                        <button type="submit" class="btn btn-primary" name="ubahDeskripsi">Simpan</button>
                                                    </div>
                                                    <br>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Modal Hapus Nilai Ulangan Siswa-->
                                    <div class="modal fade bd-example-modal-lg" id="modalHapusAsesmen<?=$id_mapel;?>"  tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">

                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Hapus Deskripsi</h4>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <!-- Modal body -->
                                                
                                                <form method="post">
                                                    <div class="modal-body">
                                                    <h5>Pilih deskripsi untuk dihapus dari <?=$namaMapel;?></h5><br>
                                                    <select class="form-select" name="deskripsi" id="deskripsi" aria-label="deskripsi" required>
                                                        <?php
                                                        $queryDeskripsi = mysqli_query($conn, "SELECT 
                                                            id_asesmen, deskripsi 
                                                            FROM 
                                                            asesmen 
                                                            WHERE 
                                                            id_mapel = $id_mapel AND 
                                                            id_kelas = $kelas AND 
                                                            semester = '$semester';");

                                                        if (mysqli_num_rows($queryDeskripsi) > 0) {
                                                            while ($rowDeskripsi = mysqli_fetch_assoc($queryDeskripsi)) {
                                                                echo '<div class="row mb-3">';
                                                                echo '<div class="col-md-12">';
                                                                echo '<option value="' . $rowDeskripsi['id_asesmen'] . '">' . $rowDeskripsi['deskripsi'] . '</option>';
                                                                echo '</div>';
                                                                echo '</div>';
                                                            }
                                                        } else {
                                                            echo '<p>Deskripsi tidak ditemukan.</p>';
                                                        }
                                                        ?>
                                                    </select>
                                                        
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="semester" value="<?=$semester;?>">
                                                        <input type="hidden" name="idMapel" value="<?=$id_mapel;?>">
                                                        <input type="hidden" name="kelas" value="<?=$kelas;?>">
                                                        <button type="submit" class="btn btn-danger" name="hapusAsesmen">Hapus</button> 
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
            var myModal = new bootstrap.Modal(document.getElementById('modalPilihMapel'));
            var myModal2 = new bootstrap.Modal(document.getElementById('exampleModalToggle2'));
        </script>


    </body>   

    <!-- Modal Tambah Deskripsi Asesmen -->
    <div class="modal fade bd-example-modal-lg" id="modalTambahDeskripsi" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Deskripsi Penilaian kelas <?=$kelas;?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal Body -->
                <form method="post">
                    <div class="modal-body">
                        <div class="row">
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
                        <div class="mb-3">
                            <label for="deskripsi">Deskripsi :</label>
                            <input type="text" name="deskripsi" class="form-control" required>
                        </div>
                        <div class="text-center">
                            <input type="hidden" name="semester" value="<?=$semester;?>">
                            <input type="hidden" name="kelas" value="<?=$kelas;?>">
                            <button type="submit" class="btn btn-primary" name="tambahDeskripsi">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Pilih Mapel
    <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel">Capaian Kompetensi Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                    <div class="mb-3">
                        <label for="idMapel">Pilih Mata Pelajaran:</label>
                        <select class="form-select" name="idMapel" id="idMapel" aria-label="idMapel" required>
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
                    <div class="mb-3">
                    <input type="hidden" name="idMap" id="idMap" class="form-control">
                    </div>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                <button class="btn btn-primary" name="lanjut" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">Lanjut</button>
                </div>
            </form>
            </div>
        </div>
    </div> -->

    <!-- Modal Pilih Capaian Kompetensi -->
    <!-- <div class="modal fade bd-example-modal-lg" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalToggleLabel2">Capaian Kompetensi Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <label>Pilih deskripsi sesuai capaian kompetensi setiap siswa :</label><br>
                <?php
                if(isset($_POST['lanjut'])) {
                    // Ambil nilai idMap dari formulir
                    $idMap = $_POST['idMap'];

                    // Sekarang, Anda dapat menggunakan nilai $idMap di sini sesuai kebutuhan
                    echo "Nilai idMap yang dikirim: " . $idMap;

                }
                ?>
                <label>id mata pelajaran : <?=$idMap;?></label>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">                            
                            <?php
                            $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
                            $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
                            $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

                            $queryCk = "SELECT
                            ck.id_siswa,
                            s.nama,
                            ck.id_asesmen,
                            a.deskripsi,
                            ck.capaian
                            FROM 
                            `capaian_kompetensi` ck
                            LEFT JOIN asesmen a ON ck.id_asesmen = a.id_asesmen  
                            LEFT JOIN siswa s ON ck.id_siswa = s.id_siswa                                  
                            WHERE
                            id_tahun_ajar = '$idTahunAjar' AND
                            semester = 'Ganjil' AND
                            a.id_kelas = '$kelas' AND
                            a.id_mapel = '$idMapel'  
                            ORDER BY ck.id_siswa ASC;";

                            $capaianKompetensi = mysqli_query($conn, $queryCk); 
                            
                            echo $queryCk;

                            while ($rowCapKom = mysqli_fetch_assoc($capaianKompetensi)) {
                                $siswa = $rowCapKom['id_tahun_ajar'];
                                $deskripsi = $rowCapKom['deskripsi'];
                                $capaian = $rowCapKom['capaian'];

                                // Menentukan status checklist berdasarkan nilai capaian
                                $checked = ($capaian == 1) ? 'checked' : '';

                                // Menampilkan data siswa dan checklist
                                echo '<div>';
                                echo '<p>'.$siswa.'</p>';
                                echo '<input type="checkbox" '.$checked.'>'.$deskripsi;
                                echo '</div>';
                            }
                            ?>                          
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">Kembali</button>
                <button class="btn btn-success" name="updateCapaianKompetensi" data-bs-toggle="modal" data-bs-dismiss="modal">Simpan</button>
            </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Temukan elemen "kelas" dan "siswa" pada Tambah Transaksi Tabungan
        var mapelDropdown = document.getElementById('idMapel');
        var idInput = document.getElementById('idMap');

        mapelDropdown.addEventListener('change', function() {
            // updateIdMapel();
            idInput.value = mapelDropdown.value;
        });       
    
    });
    </script> -->

</html>
