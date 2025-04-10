<?php
require 'function.php';
require 'cek.php';
require 'config.php';
$conn = mysqli_connect("localhost:3306","root","","sdk");

$queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar='$tahunAjar'");
while ($rowTahunAjar = mysqli_fetch_assoc($queryTahunAjar)) {                            
    $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Halaman Rapor</title>
        <style>
           

            .teks-kecil {
                font-size: 0.8em;
            }

            .row p {
                margin-bottom: 5px; /* Sesuaikan nilai sesuai kebutuhan */
            }

            .row h5 {
                margin-bottom: 5px; /* Sesuaikan nilai sesuai kebutuhan */
            }

        </style>

        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav" class="layoutSidenav">
            <?php include 'sidebar.php'; ?>
            <div id="layoutSidenav_content" class="layoutSidenav_content">
                <main >
                    <div class="container-fluid px-4">
                        <h3 class="mt-4">Rapot Project</h3>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Laporan / Rapor Project</li>    
                        </ol>  
                        <div class="container-fluid px-1">
                            <form method="post" class="form">  
                            <div class="row row-cols-auto">
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="project">Project</label>
                                            </div>
                                            <select class="form-select" name="project" id="project" aria-label="project" required>
                                                <option value="">Pilih Project</option>
                                                <?php
                                                $queryProject = mysqli_query($conn, "SELECT id_project, nama_project FROM p5_project");
                                                while ($rowProject = mysqli_fetch_assoc($queryProject)) {
                                                    echo '<option value="' . $rowProject['id_project'] . '">' . $rowProject['nama_project'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="siswa">Nama Siswa</label>
                                            </div>
                                            <select class="form-select" name="siswa" id="siswa" aria-label="siswa" required>
                                                <option value="">Pilih siswa</option>
                                                <?php
                                                // Ambil data kelas dari tabel kelas
                                                $querySiswa = mysqli_query($conn, "SELECT DISTINCT pp.id_siswa, s.nama 
                                                FROM `p5_penilaian` pp 
                                                INNER JOIN siswa s ON pp.id_siswa = s.id_siswa 
                                                WHERE 
                                                pp.kelas = $kelas AND pp.id_tahun_ajar = $idTahunAjar;");

                                                while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                                    echo '<option value="' . $rowSiswa['id_siswa'] . '">' . $rowSiswa['nama'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="siswa">Tanggal Cetak</label>
                                            </div>
                                            <?php $tanggalSaatIni = date('Y-m-d');?>    
                                            <input type="date" name="tanggalCetak" value="<?=$tanggalSaatIni;?>" class="form-control"> 
                                        </div>
                                    </div>
                                    <div class="col">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <button type="submit" class="btn btn-primary" name="btnTampilLapProject" id="btnTampilLapProject">
                                            Tampilkan
                                        </button>
                                    </div>           
                                </div>
                            </form> 
                        </div><br>                         
                    </div> 

                    <div class="container-fluid px-4">
                    <?php

                    $query = "SELECT DISTINCT pp.id_siswa, s.nama 
                                FROM `p5_penilaian` pp 
                                INNER JOIN siswa s ON pp.id_siswa = s.id_siswa 
                                WHERE 
                                pp.kelas = $kelas AND pp.id_tahun_ajar = $idTahunAjar;";
                    $result = mysqli_query($conn, $query);

                    $siswaArray = array();
                    while ($row = mysqli_fetch_assoc($result)) {
                        $siswaArray[$row['id_siswa']] = $row['nama'];
                    }

                    // Tampilkan Daftar Nilai
                    if (isset($_POST['btnTampilLapProject'])) {
                        $idProject = $_POST['project'];
                        $idSiswa = $_POST['siswa'];
                        $tanggalCetak = $_POST['tanggalCetak'];
                
                        $querySiswa = mysqli_query($conn, "SELECT nama, nis, nisn FROM siswa WHERE id_siswa='$idSiswa'");
                        $rowSiswa = mysqli_fetch_assoc($querySiswa); 
                        $nis = $rowSiswa['nis'];
                        $nisn = $rowSiswa['nisn'];

                        $targetId = $idSiswa; // ID siswa yang ingin dicari
                        $nomorUrut = array_search($targetId, array_keys($siswaArray)) + 1;

                        // Nama dan nomor urutan siswa tertentu
                        $namaSiswa = $siswaArray[$targetId];                        
                
                        ?>
                        <div class="row" style="text-align: center; border: none">
                            <div class="col-md-3" style="text-align: right; border: none">
                            </div>
                            <div class="col-md-6" style="text-transform: uppercase;">
                                <h5>RAPOR PROYEK PENGUATAN</h5>
                                <h5>PROFIL PELAJAR PANCASILA</h5>                           
                            </div>
                        </div><br>
                        <div class="row" style="text-transform: uppercase;">
                                <?php 
                                if ($kelas == 1){
                                    $stringKelas = 'I (Satu)';
                                } elseif ($kelas == 2) {
                                    $stringKelas = 'II (Dua)';
                                } elseif ($kelas == 3) {
                                    $stringKelas = 'III (Tiga)';
                                } elseif ($kelas == 4) {
                                    $stringKelas = 'IV (Empat)';
                                } elseif ($kelas == 5) {
                                    $stringKelas = 'V (Lima)';
                                } elseif ($kelas == 6) {
                                    $stringKelas = 'VI (Enam)';
                                } else {
                                    $stringKelas = '';
                                }

                                if ($kelas == 1 | $kelas == 2) { 
                                    $fase = 'A';
                                } elseif ($kelas == 3 | $kelas == 4) {
                                    $fase = 'B';
                                } elseif ($kelas == 5 | $kelas == 6) {
                                    $fase = 'C';
                                }

                                $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
                                $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
                                $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];
                                ?>                         
                        </div>

                        <div class="container-fluid px-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <table>
                                        <tr>
                                            <th style="width: 15%"></th>
                                            <th style="width: 25%">Nama Sekolah</th>
                                            <th style="width: 10%">:</th>
                                            <th style="width: 45%">SD KATOLIK BHAKTI</th>       
                                        </tr>
                                        <tr>
                                            <th style="width: 15%"></th>
                                            <th style="width: 25%">Alamat Sekolah</th>
                                            <th style="width: 10%">:</th>
                                            <th style="width: 45%">Jl. Ki. Hajar Dewantoro Rogojampi</th>       
                                        </tr>
                                        <tr>
                                            <th style="width: 15%"></th>
                                            <th style="width: 25%">Nama</th>
                                            <th style="width: 10%">:</th>
                                            <th style="width: 45%"><?=$namaSiswa;?></th>       
                                        </tr>
                                        <tr>
                                            <th style="width: 15%"></th>
                                            <th style="width: 25%">No.Induk/NISN</th>
                                            <th style="width: 10%">:</th>
                                            <th style="width: 45%"><?=$nis;?>/<?=$nisn;?></th>       
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table>
                                        <tr>
                                            <th style="width: 15%"></th>
                                            <th style="width: 25%">Kelas</th>
                                            <th style="width: 10%">:</th>
                                            <th style="width: 45%"><?=$stringKelas;?></th>       
                                        </tr>
                                        <tr>
                                            <th style="width: 15%"></th>
                                            <th style="width: 25%">No.Absen</th>
                                            <th style="width: 10%">:</th>
                                            <th style="width: 45%"><?=$nomorUrut;?></th>       
                                        </tr>
                                        <tr>
                                            <th style="width: 15%"></th>
                                            <th style="width: 25%">Fase</th>
                                            <th style="width: 10%">:</th>
                                            <th style="width: 45%"><?=$fase;?></th>       
                                        </tr>
                                        <tr>
                                            <th style="width: 15%"></th>
                                            <th style="width: 25%">Tahun Ajar</th>
                                            <th style="width: 10%">:</th>
                                            <th style="width: 45%"><?=$tahunAjar;?></th>       
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div><br><br>              
                        <?php

                        tabelNilaiProject($idTahunAjar, $kelas, $fase, $idSiswa, $idProject, $nomorUrut, $tanggalCetak);
                    }               
                    ?>
                </div>
                <?php
                function tabelNilaiProject($idTahunAjar, $kelas, $fase, $idSiswa, $idProject, $nomorUrut, $tanggalCetak) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");

                    $queryNamaProject = mysqli_query($conn, "SELECT `nama_project`,`deskripsi_project` 
                    FROM `p5_project` WHERE `id_project` = '$idProject';");
                    while ($rowProject = mysqli_fetch_array($queryNamaProject)) {
                        $namaProject = $rowProject['nama_project'];
                        $deskripsiProject = $rowProject['deskripsi_project'];
                    }

                    $queryNilaiProject = "SELECT
                    dimensi,
                    capaian,
                    MAX(CASE WHEN nilai = 'BB' THEN 1 ELSE 0 END) AS BB,
                    MAX(CASE WHEN nilai = 'MB' THEN 1 ELSE 0 END) AS MB,
                    MAX(CASE WHEN nilai = 'BSH' THEN 1 ELSE 0 END) AS BSH,
                    MAX(CASE WHEN nilai = 'SB' THEN 1 ELSE 0 END) AS SB
                    FROM
                        p5_penilaian pp
                        LEFT JOIN p5_capaian pc ON pp.id_capaian = pc.id_capaian
                        LEFT JOIN p5_sub_elemen pse ON pc.id_sub_elemen = pse.id_sub_elemen
                        LEFT JOIN p5_elemen pe ON pse.id_elemen = pe.id_elemen
                        LEFT JOIN p5_dimensi pd ON pe.id_dimensi = pd.id_dimensi
                    WHERE
                        pp.id_tahun_ajar = '$idTahunAjar' AND
                        pp.kelas = '$kelas' AND
                        pp.id_siswa = '$idSiswa' AND
                        pp.id_project = '$idProject'
                    GROUP BY dimensi, capaian;
                    ";  
                    
                    $nilaiProject = mysqli_query($conn, $queryNilaiProject);

                    echo '<div class="card mb-4">';
                    echo '<div class="card-body">';
                    echo '<h6>Nama Proyek : ' . $namaProject . '</h6>';
                    echo '<table id="datatablesSimple1" class="table table-bordered"  style="text-align: justify;">';
                    echo '<tr><td colspan="5">' . $deskripsiProject . '</td></tr>';
                    echo '<tr><td colspan="5"></td></tr>';
                    echo '<tr>
                            <th>' . $namaProject . '</th>
                            <th>BB</th>
                            <th>MB</th>
                            <th>BSH</th>
                            <th>SB</th>
                          </tr>';
                
                    while ($row = mysqli_fetch_array($nilaiProject)) {
                        $dimensi = $row['dimensi'];
                        $capaian = $row['capaian'];
                        $BB = $row['BB'];
                        $MB = $row['MB'];
                        $BSH = $row['BSH'];
                        $SB = $row['SB'];

                        echo '<tr><td colspan="5" style="background-color: #A9A9A9;">' . $dimensi . '</td></tr>';

                        echo "<tr>";
                        echo "<td>" . $capaian . "</td>";
                        echo "<td>" . ($BB == 1 ? '&#10004;' : '') . "</td>";
                        echo "<td>" . ($MB == 1 ? '&#10004;' : '') . "</td>";
                        echo "<td>" . ($BSH == 1 ? '&#10004;' : '') . "</td>";
                        echo "<td>" . ($SB == 1 ? '&#10004;' : '') . "</td>";
                        echo "</tr>";                       

                    }

                    $query = "SELECT 
                    `catatan` 
                    FROM `p5_penilaian_catatan` 
                    WHERE 
                    `id_tahun_ajar` = '$idTahunAjar' AND
                    `kelas` = '$kelas' AND
                    `id_siswa` = '$idSiswa' AND
                    `id_project` = '$idProject';";

                    $queryCatatanProject = mysqli_query($conn, $query);

                    
                    while ($rowCatatanProject = mysqli_fetch_array($queryCatatanProject)) {
                        $catatanProject = $rowCatatanProject['catatan'];
                    }

                    echo '<tr><td colspan="5"></td></tr>';
                    echo '<tr><td colspan="5"><h6>Catatan proses :</h6></td></tr>';
                    echo '<tr><td colspan="5">' . $catatanProject . '</td></tr>';

                    echo '</table>';
                    echo '<br><br>'; 

                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<form method="post" action="pdf_rapot_project.php" target="_blank">';
                    echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                    echo '<input type="hidden" name="kelas" value="' . $kelas . '">';
                    echo '<input type="hidden" name="idSiswa" value="' . $idSiswa . '">';
                    echo '<input type="hidden" name="nomorUrut" value="' . $nomorUrut . '">'; 
                    echo '<input type="hidden" name="idProject" value="' . $idProject . '">';
                    echo '<input type="hidden" name="tanggalCetak" value="' . $tanggalCetak . '">';                     
                    echo '<button type="submit" class="btn btn-primary" name="btnCetakRapotProject" id="btnCetakRapotProject">Cetak</button>';
                    echo '</form>';
                    echo '</div><br>';
                    echo '<table class="table-bordered" style="width: 60%; text-align: center;">
                            <tr>
                                <th>BB</th>
                                <th>MB</th>
                                <th>BSH</th>
                                <th>SB</th>
                            </tr>
                            <tr>
                                <td>Belum Berkembang</td>
                                <td>Mulai Berkembang</td>
                                <td>Berkembang Sesuai Harapan</td>
                                <td>Sangat Berkembang</td>
                            </tr>
                            <tr>
                                <td>Siswa masih membutuhkan bimbingan dalam mengembangkan kemampuan.</td>
                                <td>Siswa mulai mengembangkan kemampuan namun masih belum ajek.</td>
                                <td>Siswa telah mengembangkan kemampuan hingga berada dalam tahap ajek.</td>
                                <td>Siswa mengembangkan kemampuannya melampaui harapan.</td>
                            </tr>
                          </table><br><br>';
                }

                ?>              
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
</html>
 
