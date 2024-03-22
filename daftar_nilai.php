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
        <title>Halaman Daftar Nilai</title>
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
                        <h3 class="mt-4">Daftar Nilai</h3>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Laporan / Daftar Nilai</li>    
                        </ol>  
                        <div class="container-fluid px-1">
                            <form method="post" class="form">  
                                <div class="row row-cols-auto">
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="semester">Semester</label>
                                            </div>
                                            <select class="custom-select" id="semester" name="semester" required>
                                                <option value="">Pilih Semester </option>                                                
                                                <option value="Ganjil">Ganjil</option>
                                                <option value="Genap">Genap</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="kategori">Mata Pelajaran</label>
                                            </div>
                                            <select class="form-select" name="mapel" id="mapel" aria-label="mapel" required>
                                                <option value="">Pilih Mapel</option>
                                                <?php
                                                if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                                                    $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel WHERE mapel <> 'Bahasa Using'");
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
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary" name="btnTampilLapSiswa" id="btnTampilLapSiswa">
                                            Tampilkan
                                        </button>
                                    </div>            
                                </div>
                            </form> 
                        </div>
                    </div><br> 

                    <div class="container-fluid px-4">
                    <?php
                    // Tampilkan Daftar Nilai
                    if (isset($_POST['btnTampilLapSiswa'])) {
                        $semester = $_POST['semester'];
                        $idMapel = $_POST['mapel'];
                
                        $queryMapel = mysqli_query($conn, "SELECT mapel FROM mapel WHERE id_mapel='$idMapel'");
                        $rowMapel = mysqli_fetch_assoc($queryMapel);
                        $namaMapel = $rowMapel['mapel'];
                
                        ?>
                        <div class="row" style="text-align: center; border: none">
                            <div class="col-md-3" style="text-align: right; border: none">
                            </div>
                            <div class="col-md-6">
                                <h5>DAFTAR NILAI SEMESTER <?php if ($semester == 'Ganjil') { echo 'I'; } elseif ($semester == 'Genap') { echo 'II'; } else { echo ''; }?></h5>
                                <h5>SD KATOLIK BHAKTI ROGOJAMPI </h5>
                                <h5>TAHUN PELAJARAN <?=$tahunAjar; ?></h5>
                            </div>
                        </div><br>
                        <div class="row" style="text-transform: uppercase;">
                            <h6>KELAS : 
                                <?php 
                                if ($kelas == 1){
                                    echo 'I';
                                } elseif ($kelas == 2) {
                                    echo 'II';
                                } elseif ($kelas == 3) {
                                    echo 'III';
                                } elseif ($kelas == 4) {
                                    echo 'IV';
                                } elseif ($kelas == 5) {
                                    echo 'V';
                                } elseif ($kelas == 6) {
                                    echo 'VI';
                                } else {
                                    echo '';
                                }

                                $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
                                $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
                                $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

                                ?>
                            </h6>
                            <h6>MATA PELAJARAN : <?=$namaMapel;?></H6>
                        </div>
                        <?php

                        tabelDaftarNilai($idTahunAjar, $semester, $kelas, $idMapel);
                    }               
                    ?>
                </div>
                <?php
                function tabelDaftarNilai($idTahunAjar, $semester, $kelas, $idMapel) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");
                    echo '<div class="card mb-4">';
                    echo '<div class="card-header">';
                    echo '<i class="fas fa-table me-1"></i>';
                    echo 'Daftar Nilai kelas ' . $kelas;
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<table id="datatablesSimple1" class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th rowspan="3" style="vertical-align: middle;">No.</th>';
                    echo '<th rowspan="3" style="vertical-align: middle;">No. Induk</th>';
                    echo '<th rowspan="3" style="vertical-align: middle;">NISN</th>';
                    echo '<th rowspan="3"  style="vertical-align: middle;">Nama</th>';
                    echo '<th colspan="16" style="text-align: center;">FORMATIF</th>';
                    echo '<th rowspan="2" colspan="5" style="text-align: center; vertical-align: middle;">NILAI SUMATIF LINGKUP MATERI</th>';
                    echo '<th rowspan="2" colspan="2" style="text-align: center; vertical-align: middle;">NILAI SUMATIF</th>';
                    echo '<th rowspan="3" style="vertical-align: middle;">NR</th>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th colspan="4">Lingkup Materi 1</th>';
                    echo '<th colspan="4">Lingkup Materi 2</th>';
                    echo '<th colspan="4">Lingkup Materi 3</th>';
                    echo '<th colspan="4">Lingkup Materi 4</th>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<th>TP1</th>';
                    echo '<th>TP2</th>';
                    echo '<th>TP3</th>';
                    echo '<th>TP4</th>';
                    echo '<th>TP1</th>';
                    echo '<th>TP2</th>';
                    echo '<th>TP3</th>';
                    echo '<th>TP4</th>';
                    echo '<th>TP1</th>';
                    echo '<th>TP2</th>';
                    echo '<th>TP3</th>';
                    echo '<th>TP4</th>';
                    echo '<th>TP1</th>';
                    echo '<th>TP2</th>';
                    echo '<th>TP3</th>';
                    echo '<th>TP4</th>';
                    echo '<th>LM1</th>';
                    echo '<th>LM2</th>';
                    echo '<th>LM3</th>';
                    echo '<th>LM4</th>';
                    echo '<th>AVG</th>';
                    echo '<th>STS</th>';
                    echo '<th>SAS</th>';
                    echo '</tr>'; 
                    echo '</thead>';
                    echo '<tbody>';

                    $quer = "SELECT s.nis, s.nisn, s.nama,
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
                FROM siswa s 
                LEFT JOIN nilai_mapel nm ON s.id_siswa = nm.id_siswa
                LEFT JOIN nilai_ulangan nul ON s.id_siswa = nul.id_siswa
                LEFT JOIN nilai_ujian nuj ON s.id_siswa = nuj.id_siswa
                WHERE nm.kelas = '$kelas' AND
                    nm.semester = '$semester' AND
                    nm.id_mapel = '$idMapel' AND
                    nm.id_tahun_ajar = '$idTahunAjar' AND
                    nul.kelas = '$kelas' AND
                    nul.semester = '$semester' AND
                    nul.id_mapel = '$idMapel' AND
                    nul.id_tahun_ajar = '$idTahunAjar' AND
                    nuj.kelas = '$kelas' AND
                    nuj.semester = '$semester' AND
                    nuj.id_mapel = '$idMapel' AND
                    nuj.id_tahun_ajar = '$idTahunAjar'
                GROUP BY s.nis, s.nisn, s.nama;
                ";
                    $query = mysqli_query($conn, $quer);
                    $i = 1;

                    while ($row = mysqli_fetch_array($query)) {
                        $nis = $row['nis'];
                        $nisn = $row['nisn'];
                        $namaSiswa = $row['nama'];
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
                        
                        echo '<tr>';
                        echo '<td>' . $i++ . '</td>';
                        echo '<td>' . $nis . '</td>';
                        echo '<td>' . $nisn . '</td>';
                        echo '<td>' . $namaSiswa . '</td>';
                        echo '<td>' . $LM1_TP1 . '</td>';
                        echo '<td>' . $LM1_TP2 . '</td>';
                        echo '<td>' . $LM1_TP3 . '</td>';
                        echo '<td>' . $LM1_TP4 . '</td>';
                        echo '<td>' . $LM2_TP1 . '</td>';
                        echo '<td>' . $LM2_TP2 . '</td>';
                        echo '<td>' . $LM2_TP3 . '</td>';
                        echo '<td>' . $LM2_TP4 . '</td>';
                        echo '<td>' . $LM3_TP1 . '</td>';
                        echo '<td>' . $LM3_TP2 . '</td>';
                        echo '<td>' . $LM3_TP3 . '</td>';
                        echo '<td>' . $LM3_TP4 . '</td>';
                        echo '<td>' . $LM4_TP1 . '</td>';
                        echo '<td>' . $LM4_TP2 . '</td>';
                        echo '<td>' . $LM4_TP3 . '</td>';
                        echo '<td>' . $LM4_TP4 . '</td>';
                        echo '<td>' . $LM1 . '</td>';
                        echo '<td>' . $LM2 . '</td>';
                        echo '<td>' . $LM3 . '</td>';
                        echo '<td>' . $LM4 . '</td>';
                        echo '<td>' . $rerataUlangan . '</td>';
                        echo '<td>' . $STS . '</td>';
                        echo '<td>' . $SAS . '</td>';
                        echo '<td>' . $nilaiRapot . '</td>';
                        echo '</tr>';
                    }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                        echo '<form method="post" action="pdf_cover_daftar_nilai.php" target="_blank">';
                        echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                        echo '<input type="hidden" name="semester" value="' . $semester . '">';
                        echo '<input type="hidden" name="kelas" value="' . $kelas . '">';
                        echo '<input type="hidden" name="idMapel" value="' . $idMapel . '">';                        
                        echo '<button type="submit" class="btn btn-primary" name="btnCetakDaftarNilai" id="btnCetakDaftarNilai">Cetak Cover</button>';
                        echo '</form><br>';
                        echo '<form method="post" action="pdf_daftar_nilai.php" target="_blank">';
                        echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                        echo '<input type="hidden" name="semester" value="' . $semester . '">';
                        echo '<input type="hidden" name="kelas" value="' . $kelas . '">';
                        echo '<input type="hidden" name="idMapel" value="' . $idMapel . '">';                        
                        echo '<button type="submit" class="btn btn-primary" name="btnCetakDaftarNilai" id="btnCetakDaftarNilai">Cetak</button>';
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
