<?php
require 'function.php';
require 'cek.php';
require 'config.php';
$conn = mysqli_connect("localhost:3306","root","","sdk");

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
                        <h3 class="mt-4">Rapot Semester</h3>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Laporan / Rapor Semester</li>    
                        </ol>  
                        <div class="container-fluid px-1">
                            <form method="post" class="form">  
                                <div class="row row-cols-auto">
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="semester">Semester</label>
                                            </div>
                                            <select class="custom-select" id="semester" name="semester">
                                                <option value="">Pilih Semester </option>                                                
                                                <option value="Ganjil">Ganjil</option>
                                                <option value="Genap">Genap</option>
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
                                                $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas='$kelas'");
                                                while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                                    echo '<option value="' . $rowSiswa['id_siswa'] . '">' . $rowSiswa['nama'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">&nbsp;
                                        <button type="submit" class="btn btn-primary" name="btnTampilLapSiswa" id="btnTampilLapSiswa">
                                            Tampilkan
                                        </button>
                                    </div>         
                                </div>                                
                                <!-- <div class="row row-cols-auto">
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="tplm1">LM 1</label>
                                            </div>
                                            <select class="custom-select" id="tplm1" name="tplm1">
                                                <option value="">Jumlah TP </option>                                                
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="tplm2">LM 2</label>
                                            </div>
                                            <select class="custom-select" id="tplm2" name="tplm2">
                                                <option value="">Jumlah TP </option>                                                
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="tplm3">LM 3</label>
                                            </div>
                                            <select class="custom-select" id="tplm3" name="tplm3">
                                                <option value="">Jumlah TP </option>                                                
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>
                                    </div>  
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="tplm4">LM 4</label>
                                            </div>
                                            <select class="custom-select" id="tplm4" name="tplm4">
                                                <option value="">Jumlah TP </option>                                                
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="col">
                                        <a data-bs-toggle="modal" data-bs-target="#modalInfo"><img src="assets/img/question.png" width="30px"></a>
                                    </div>                                              
                                </div> -->
                            </form> 
                        </div><br>                         
                    </div><br> 

                    <div class="container-fluid px-4">
                    <?php

                    $query = "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas ORDER BY nama";
                    $result = mysqli_query($conn, $query);

                    $siswaArray = array();
                    while ($row = mysqli_fetch_assoc($result)) {
                        $siswaArray[$row['id_siswa']] = $row['nama'];
                    }

                    $tplm1 = 0;
                    $tplm2 = 0;
                    $tplm3 = 0;
                    $tplm4 = 0;


                    // Tampilkan Daftar Nilai
                    if (isset($_POST['btnTampilLapSiswa'])) {
                        $semester = $_POST['semester'];
                        $idSiswa = $_POST['siswa'];
                        // $tplm1 = $_POST['tplm1'];
                        // $tplm2 = $_POST['tplm2'];
                        // $tplm3 = $_POST['tplm3'];
                        // $tplm4 = $_POST['tplm4'];
                
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
                                <h5>LAPORAN PENILAIAN HASIL BELAJAR</h5>
                                <h5>AKHIR SEMESTER <?=$semester;?></h5>
                                <h5>TAHUN PELAJARAN <?=$tahunAjar;?></h5>                            
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
                                    </table>
                                </div>
                            </div>
                        </div><br><br>              
                        <?php

                        tabelDaftarNilai($idTahunAjar, $semester, $kelas, $idSiswa, $nomorUrut, $tplm1, $tplm2, $tplm3, $tplm4);
                    }               
                    ?>
                </div>
                <?php
                function tabelDaftarNilai($idTahunAjar, $semester, $kelas, $idSiswa, $nomorUrut, $tplm1, $tplm2, $tplm3, $tplm4) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");

                    $queryRapotSisipan = "SELECT m.id_mapel, m.mapel,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM1_TP1,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM1_TP2,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM1_TP3,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 1' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM1_TP4,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM2_TP1,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM2_TP2,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM2_TP3,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 2' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM2_TP4,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM3_TP1,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM3_TP2,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM3_TP3,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 3' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM3_TP4,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP1' THEN nm.nilai ELSE 0 END) AS LM4_TP1,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP2' THEN nm.nilai ELSE 0 END) AS LM4_TP2,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP3' THEN nm.nilai ELSE 0 END) AS LM4_TP3,
                    MAX(CASE WHEN nm.lingkup_materi = 'LM 4' AND nm.tujuan_pembelajaran = 'TP4' THEN nm.nilai ELSE 0 END) AS LM4_TP4,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 1' THEN nul.nilai ELSE 0 END) AS LM1,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 2' THEN nul.nilai ELSE 0 END) AS LM2,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 3' THEN nul.nilai ELSE 0 END) AS LM3,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 4' THEN nul.nilai ELSE 0 END) AS LM4,                        
                    MAX(CASE WHEN nuj.ujian = 'STS' THEN nuj.nilai ELSE 0 END) AS STS,
                    MAX(CASE WHEN nuj.ujian = 'SAS' THEN nuj.nilai ELSE 0 END) AS SAS
                    FROM mapel m
                    LEFT JOIN nilai_mapel nm ON m.id_mapel = nm.id_mapel
                    LEFT JOIN nilai_ulangan nul ON m.id_mapel = nul.id_mapel
                    LEFT JOIN nilai_ujian nuj ON m.id_mapel = nuj.id_mapel
                    WHERE nm.kelas = '$kelas' AND
                        nm.semester = '$semester' AND
                        nm.id_siswa = '$idSiswa' AND
                        nm.id_tahun_ajar = '$idTahunAjar' AND
                        nul.kelas = '$kelas' AND
                        nul.semester = '$semester' AND
                        nul.id_siswa = '$idSiswa' AND
                        nul.id_tahun_ajar = '$idTahunAjar' AND
                        nuj.kelas = '$kelas' AND
                        nuj.semester = '$semester' AND
                        nuj.id_siswa = '$idSiswa' AND
                        nuj.id_tahun_ajar = '$idTahunAjar'
                    GROUP BY  m.id_mapel, m.mapel;
                    ";

                    $query = mysqli_query($conn, $queryRapotSisipan);

                    echo '<div class="card mb-4">';
                    echo '<div class="card-header">';
                    echo '<i class="fas fa-table me-1"></i>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<table id="datatablesSimple1" class="table table-bordered"  style="text-align: center;">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th rowspan="3" style="vertical-align: middle;">No.</th>';
                    echo '<th rowspan="3" style="vertical-align: middle; text-align: left;">Mata Pelajaran</th>';
                    echo '<th colspan="8" style="text-align: center;">FORMATIF</th>';
                    echo '<th rowspan="2" colspan="4" style="text-align: center; vertical-align: middle;">NSLM</th>';
                    echo '<th rowspan="2" colspan="2" style="vertical-align: middle;">NR</th>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th colspan="2">LM 1</th>';
                    echo '<th colspan="2">LM 2</th>';
                    echo '<th colspan="2">LM 3</th>';
                    echo '<th colspan="2">LM 4</th>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>TP1</th>';
                    echo '<th>TP2</th>';                   
                    echo '<th>TP1</th>';
                    echo '<th>TP2</th>';
                    echo '<th>TP1</th>';
                    echo '<th>TP2</th>';
                    echo '<th>TP1</th>';
                    echo '<th>TP2</th>';
                    echo '<th>LM1</th>';
                    echo '<th>LM2</th>';
                    echo '<th>LM3</th>';
                    echo '<th>LM4</th>';
                    echo '<th>STS</th>';
                    echo '<th>SAS</th>';
                    echo '</tr>'; 
                    echo '</thead>';
                    echo '<tbody>';
                    
                    $i = 1;
                
                    while ($row = mysqli_fetch_array($query)) {
                        $idMapel = $row['id_mapel'];
                        $mapel = $row['mapel'];
                        $LM1_TP1 = $row['LM1_TP1'];
                        $LM1_TP2 = $row['LM1_TP2'];
                        $LM1_TP3 = $row['LM1_TP3'];
                        $LM1_TP4 = $row['LM1_TP4'];
                        $LM2_TP1 = $row['LM2_TP1'];
                        $LM2_TP2 = $row['LM2_TP2'];
                        $LM2_TP3 = $row['LM2_TP3'];
                        $LM2_TP4 = $row['LM2_TP4'];
                        $LM3_TP1 = $row['LM3_TP1'];
                        $LM3_TP2 = $row['LM3_TP2'];
                        $LM3_TP3 = $row['LM3_TP3'];
                        $LM3_TP4 = $row['LM3_TP4'];
                        $LM4_TP1 = $row['LM4_TP1'];
                        $LM4_TP2 = $row['LM4_TP2'];
                        $LM4_TP3 = $row['LM4_TP3'];
                        $LM4_TP4 = $row['LM4_TP4'];
                        $LM1 = $row['LM1'];
                        $LM2 = $row['LM2'];
                        $LM3 = $row['LM3'];
                        $LM4 = $row['LM4'];
                        $rerataUlangan = ($LM1 + $LM2 + $LM3 + $LM4)/4;
                        $STS = $row['STS'];
                        $SAS = $row['SAS'];
                        $nilaiRapot = ((2 * $rerataUlangan) + $STS + (2 * $SAS)) / 5;

                        if ($tplm1 == 3) {
                            $lm1tp1 = ($LM1_TP1 + $LM1_TP2)/2;
                            $lm1tp2 = ($LM1_TP2 + $LM1_TP3)/2;                                                        
                        } elseif ($tplm1 == 4) {
                            $lm1tp1 = ($LM1_TP1 + $LM1_TP2)/2;
                            $lm1tp2 = ($LM1_TP3 + $LM1_TP4)/2;
                        } else {
                            $lm1tp1 = $LM1_TP1;
                            $lm1tp2 = $LM1_TP2;
                        }

                        if ($tplm2 == 3) {
                            $lm2tp1 = ($LM2_TP1 + $LM2_TP2)/2;
                            $lm2tp2 = ($LM2_TP2 + $LM2_TP3)/2;
                        } elseif ($tplm2 == 4) {
                            $lm2tp1 = ($LM2_TP1 + $LM2_TP2)/2;
                            $lm2tp2 = ($LM2_TP3 + $LM2_TP4)/2;
                        } else {
                            $lm2tp1 = $LM2_TP1;
                            $lm2tp2 = $LM2_TP2;
                        }

                        if ($tplm3 == 3) {
                            $lm3tp1 = ($LM3_TP1 + $LM3_TP2)/2;
                            $lm3tp2 = ($LM3_TP2 + $LM3_TP3)/2;
                        } elseif ($tplm3 == 4) {
                            $lm3tp1 = ($LM3_TP1 + $LM3_TP2)/2;
                            $lm3tp2 = ($LM3_TP3 + $LM3_TP4)/2;
                        } else {
                            $lm3tp1 = $LM3_TP1;
                            $lm3tp2 = $LM3_TP2;
                        }

                        if ($tplm4 == 3) {
                            $lm4tp1 = ($LM4_TP1 + $LM4_TP2)/2;
                            $lm4tp2 = ($LM4_TP2 + $LM4_TP3)/2;
                        } elseif ($tplm4 == 4) {
                            $lm4tp1 = ($LM4_TP1 + $LM4_TP2)/2;
                            $lm4tp2 = ($LM4_TP3 + $LM4_TP4)/2;
                        } else {
                            $lm4tp1 = $LM4_TP1;
                            $lm4tp2 = $LM4_TP2;
                        }


                        echo '<tr>';
                        echo '<td>' . $i++ . '</td>';
                        echo '<td style="text-align: left;">' . $mapel . '</td>';
                        echo '<td>' . $LM1_TP1 . '</td>';
                        echo '<td>' . $LM1_TP2 . '</td>';
                        echo '<td>' . $LM2_TP1 . '</td>';
                        echo '<td>' . $LM2_TP2 . '</td>';
                        echo '<td>' . $LM3_TP1 . '</td>';
                        echo '<td>' . $LM3_TP2 . '</td>';
                        echo '<td>' . $LM4_TP1 . '</td>';
                        echo '<td>' . $LM4_TP2 . '</td>';
                        echo '<td>' . $LM1 . '</td>';
                        echo '<td>' . $LM2 . '</td>';
                        echo '<td>' . $LM3 . '</td>';
                        echo '<td>' . $LM4 . '</td>';
                        echo '<td>' . $STS . '</td>';
                        echo '<td>' . $SAS . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '</div>';
                    echo '</div>';

                    echo '<div class="row" style="border-style: none;">';
                    echo '<div class="col-md-4"><br>';
                    //Nilai praktek
                    echo '<table class="table table-bordered" style="text-align: center;">';
                    echo '<tr>';
                    echo '<th colspan="3">Nilai Praktek</th>';   
                    echo '</tr>';
                    
                    $queryNilaiPraktek = "SELECT `kategori_praktek`, `nilai` 
                    FROM `nilai_praktek` 
                    WHERE 
                    `id_tahun_Ajar` = '$idTahunAjar' AND 
                    `semester`='$semester' AND 
                    `id_siswa`='$idSiswa';";

                    $i = 1;
                    $nilaiPraktek = mysqli_query($conn, $queryNilaiPraktek);
                    while ($rowPraktek = mysqli_fetch_array($nilaiPraktek)) {
                        $kategoriPraktek = $rowPraktek['kategori_praktek'];
                        $nilaiPrak = $rowPraktek['nilai'];
                        echo '<tr>';
                        echo '<td style="width: 10%">' . $i++ . '</td>';
                        echo '<td style="width: 25%; text-align: left;">' . $kategoriPraktek . '</td>';
                        echo '<td style="width: 15%">' . $nilaiPrak  . '</td>';   
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '</div>';

                    echo '<div class="col-md-4"><br>';
                    //Nilai Kepribadian
                    echo '<table class="table table-bordered" style="text-align: center;">';
                    echo '<tr>';
                    echo '<th colspan="3">Kepribadian</th>'; 
                    echo '</tr>';

                    $queryNilaiKepribadian = "SELECT `kategori_kepribadian`, `nilai` 
                    FROM `nilai_kepribadian` 
                    WHERE 
                    `id_tahun_Ajar` = '$idTahunAjar' AND 
                    `semester`='$semester' AND 
                    `id_siswa`='$idSiswa';";

                    $i = 1;
                    $nilaiKepribadian = mysqli_query($conn, $queryNilaiKepribadian);
                    while ($rowKepribadian = mysqli_fetch_array($nilaiKepribadian)) {
                        $kategoriKepribadian = $rowKepribadian['kategori_kepribadian'];
                        $nilaiKeprib = $rowKepribadian['nilai'];
                        echo '<tr>';
                        echo '<td style="width: 10%">' . $i++ . '</td>';
                        echo '<td style="width: 25%; text-align: left;">' . $kategoriKepribadian . '</td>';
                        echo '<td style="width: 15%">' . $nilaiKeprib  . '</td>';   
                        echo '</tr>';
                    }

                    echo '</table>';
                    echo '</div>';
                    //Nilai Kepribadian Absensi
                    echo '<div class="col-md-4"><br>';
                    echo '<table class="table table-bordered" style="text-align: center;">';
                    echo '<tr>';
                    echo '<th colspan="3">Absensi Siswa</th>';
                    echo '</tr>';

                    $queryAbsensi = "SELECT absen, COUNT(absen) AS count
                    FROM absensi                    
                    WHERE 
                    `id_tahun_Ajar` = '$idTahunAjar' AND 
                    `semester`='$semester' AND 
                    `id_siswa`='$idSiswa'
                    GROUP BY absen 
                    ;";

                    $i = 1;
                    $absensiSiswa = mysqli_query($conn, $queryAbsensi);
                    while ($rowAbsensi = mysqli_fetch_array($absensiSiswa)) {
                        $kategoriAbsen = $rowAbsensi['absen'];
                        $jumlahAbsen = $rowAbsensi['count'];
                        echo '<tr>';
                        echo '<td style="width: 10%">' . $i++ . '</td>';
                        echo '<td style="width: 25%; text-align: left;">' . $kategoriAbsen . '</td>';
                        echo '<td style="width: 15%">' . $jumlahAbsen  . '</td>';   
                        echo '</tr>';
                    }

                    echo '</table>';
                    echo '</div><br><br><br>';
                    //catatan
                    echo '<div class="row">';
                    echo '<div class="col-md-2">';
                    echo '<table';
                    echo '<tr>';
                    echo '<th colspan="3"></th>';
                    echo '</tr>';    
                    echo '</table>';
                    echo '</div>';
                    echo '<div class="col-md-8">';
                    echo '<table class="table table-bordered" style="text-align: center;">';
                    echo '<tr>';
                    echo '<th colspan="3">Catatan</th>';
                    echo '</tr>';
                    echo '<tr>';

                    $queryCatatan = "SELECT catatan
                    FROM nilai_catatan                  
                    WHERE 
                    `id_tahun_ajar` = '$idTahunAjar' AND 
                    `semester`='$semester' AND 
                    `id_siswa`='$idSiswa'
                    ;";

                    $catatanSiswa = mysqli_query($conn, $queryCatatan);
                    while ($rowCatatan = mysqli_fetch_array($catatanSiswa)) {
                    $catatan = $rowCatatan['catatan'];

                        echo '<tr>';
                        echo '<td>' . $catatan . '</td>';   
                        echo '</tr>';
                    }
                   
                    echo '</tr>';    
                    echo '</table>';
                    echo '</div>';
                    echo '<div class="col-md-2">';
                    echo '<table>';
                    echo '<tr>';
                    echo '<th colspan="3"></th>';
                    echo '</tr>';    
                    echo '</table>';
                    echo '</div>';
                    echo '</div>';

                     //Naik / Tidak Naik
                     if ($semester == 'Genap'){
                        echo '<div class="row">';
                        echo '<div class="col-md-2">';
                        echo '<table';
                        echo '<tr>';
                        echo '<th colspan="3"></th>';
                        echo '</tr>';    
                        echo '</table>';
                        echo '</div>';
                        echo '<div class="col-md-8">';
                        echo '<table class="table table-bordered" style="text-align: center;">';
                        echo '<tr>';
                        echo '<th colspan="3">Naik / Tidak Naik</th>';
                        echo '</tr>';
                        echo '<tr>';
    
                        $queryKenaikan = "SELECT `status`
                        FROM kenaikan_kelas                 
                        WHERE 
                        `id_tahun_Ajar` = '$idTahunAjar' AND 
                        `semester`='$semester' AND 
                        `id_siswa`='$idSiswa'
                        ;";
    
                        $i = 1;
                        $kenaikanSiswa = mysqli_query($conn, $queryKenaikan);
                        $rowKenaikan = mysqli_fetch_array($kenaikanSiswa);
                        $kenaikan = $rowKenaikan['status'];
    
                            echo '<tr>';
                            echo '<td>' . $kenaikan  . '</td>';   
                            echo '</tr>';
                        
                        echo '</tr>';    
                        echo '</table>';
                        echo '</div>';
                        echo '<div class="col-md-2">';
                        echo '<table>';
                        echo '<tr>';
                        echo '<th colspan="3"></th>';
                        echo '</tr>';    
                        echo '</table>';
                        echo '</div>';
                        echo '</div>';
                     }
                    echo '</div><br><br>';

                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<form method="post" action="pdf_rapot_semester.php" target="_blank">';
                    echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                    echo '<input type="hidden" name="semester" value="' . $semester . '">';
                    echo '<input type="hidden" name="kelas" value="' . $kelas . '">';
                    echo '<input type="hidden" name="idSiswa" value="' . $idSiswa . '">'; 
                    echo '<input type="hidden" name="tplm1" value="' . $tplm1 . '">'; 
                    echo '<input type="hidden" name="tplm2" value="' . $tplm2 . '">';
                    echo '<input type="hidden" name="tplm3" value="' . $tplm3 . '">'; 
                    echo '<input type="hidden" name="tplm4" value="' . $tplm4 . '">';
                    echo '<input type="hidden" name="nomorUrut" value="' . $nomorUrut . '">';                       
                    echo '<button type="submit" class="btn btn-primary" name="btnCetakRapotSemester" id="btnCetakRapotSemester">Cetak</button>';
                    echo '</form>';
                    echo '</div><br>';
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
        <script>
            // Ambil referensi ke elemen-elemen dropdown
            var tahunAjarDropdown = document.getElementById('tahunAjar');
            var bulanDropdown = document.getElementById('bulan');
            var kategoriDropdown = document.getElementById('kategori');
            var tampilkanButton = document.getElementById('btnTampilLapUmum');

            // Tambahkan event listener ke setiap dropdown
            tahunAjarDropdown.addEventListener('change', checkDropdowns);
            bulanDropdown.addEventListener('change', checkDropdowns);
            kategoriDropdown.addEventListener('change', checkDropdowns);

            // Fungsi untuk memeriksa setiap dropdown
            function checkDropdowns() {
                if (tahunAjarDropdown.value !== '' && bulanDropdown.value !== '' && kategoriDropdown.value !== '') {
                    tampilkanButton.disabled = false;
                } else {
                    tampilkanButton.disabled = true;
                }
            }
        </script>
    
    </body>
</html>

<!-- Modal Info-->
<div class="modal fade" id="modalInfo">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Info</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->      
                <form method="post">
                <div class="modal-body">
                    <h6>Jumlah TP untuk setiap LM opsional. Bila tidak dipilih, TP1 dan TP2 pada Daftar Nilai akan dipakai untuk setiap LM.</h6>
                    <h6>Formula bila dipilih :</h6>
                    <h6><u>Jumlah TP 2 :</u></h6>
                    <h6>TP1 diambil dari TP1 pada Daftar Nilai</h6>
                    <h6>TP2 diambil dari TP2 pada Daftar Nilai</h6>  
                    <h6><u>Jumlah TP 3 :</u></h6>
                    <h6>TP1 dihitung dari rata-rata TP1 dan TP2 pada Daftar Nilai</h6>
                    <h6>TP2 dihitung dari rata-rata TP2 dan TP3 pada Daftar Nilai</h6>  
                    <h6><u>Jumlah TP 4 :</u></h6>
                    <h6>TP1 dihitung dari rata-rata TP1 dan TP2 pada Daftar Nilai</h6>
                    <h6>TP2 dihitung dari rata-rata TP3 dan TP4 pada Daftar Nilai</h6>                 
                </div>
                <br> 
            </form>   
        </div>
    </div>
</div> 
