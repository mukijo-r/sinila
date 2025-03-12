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
        <title>Halaman Legger</title>
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
                        <h3 class="mt-4">Legger</h3>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Laporan / Legger</li>    
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
                
                        ?>
                        <div class="row" style="text-align: center; border: none">
                            <div class="col-md-3" style="text-align: right; border: none">
                            </div>
                            <div class="col-md-6">
                                <h5>LEGGER SEMESTER <?php if ($semester == 'Ganjil') { echo 'I'; } elseif ($semester == 'Genap') { echo 'II'; } else { echo ''; }?></h5>
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
                        </div>
                        <?php

                        tabelLegger($idTahunAjar, $semester, $kelas);
                    }               
                    ?>
                </div>
                <?php
                function tabelLegger($idTahunAjar, $semester, $kelas) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");
                    echo '<div class="card mb-4">';
                    echo '<div class="card-body">';

                    echo '<table id="datatablesSimple1" class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th style="vertical-align: middle;">No.</th>';
                    echo '<th style="vertical-align: middle;">No. Induk</th>';
                    echo '<th style="vertical-align: middle;">NISN</th>';
                    echo '<th style="vertical-align: middle;">Nama</th>';

                    $subjectsQuery = "SELECT id_mapel, mapel FROM mapel ORDER BY urutan;";
                    $subjectsResult = mysqli_query($conn, $subjectsQuery);

                    while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
                        $idMapel = $subjectRow['id_mapel'];
                        $mapel = $subjectRow['mapel'];
                        if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                            if ($mapel == 'Bahasa Using') {
                                continue;
                            }
                        }
                        echo '<th style="vertical-align: middle;">' . $mapel . '</th>';
                    }

                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    // Fetch student data
                    $query = "
                        SELECT s.nis, s.nisn, s.nama
                        FROM siswa s
                        WHERE id_kelas = $kelas
                        GROUP BY s.nis, s.nisn, s.nama;
                    ";
                    $studentResult = mysqli_query($conn, $query);

                    $i = 1;

                    while ($studentRow = mysqli_fetch_assoc($studentResult)) {
                        $nis = $studentRow['nis'];
                        $nisn = $studentRow['nisn'];
                        $namaSiswa = $studentRow['nama'];

                        echo '<tr>';
                        echo '<td>' . $i++ . '</td>';
                        echo '<td>' . $nis . '</td>';
                        echo '<td>' . $nisn . '</td>';
                        echo '<td>' . $namaSiswa . '</td>';

                        // Fetch and display nilaiRapot for each subject
                        mysqli_data_seek($subjectsResult, 0); // Reset the subjects result pointer

                        while ($subjectRow = mysqli_fetch_assoc($subjectsResult)) {
                            $idMapel = $subjectRow['id_mapel'];
                            $mapel = $subjectRow['mapel'];

                            if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                                if ($mapel == 'Bahasa Using') {
                                    continue;
                                }
                            }
                        
                            $query = "
                                SELECT
                                    MAX(CASE WHEN nul.lingkup_materi = 'LM 1' THEN nul.nilai ELSE 0 END) AS LM1,
                                    MAX(CASE WHEN nul.lingkup_materi = 'LM 2' THEN nul.nilai ELSE 0 END) AS LM2,
                                    MAX(CASE WHEN nul.lingkup_materi = 'LM 3' THEN nul.nilai ELSE 0 END) AS LM3,
                                    MAX(CASE WHEN nul.lingkup_materi = 'LM 4' THEN nul.nilai ELSE 0 END) AS LM4,
                                    MAX(CASE WHEN nuj.ujian = 'STS' THEN nuj.nilai ELSE 0 END) AS STS,
                                    MAX(CASE WHEN nuj.ujian = 'SAS' THEN nuj.nilai ELSE 0 END) AS SAS
                                FROM siswa s
                                LEFT JOIN nilai_ulangan nul ON s.id_siswa = nul.id_siswa
                                LEFT JOIN nilai_ujian nuj ON s.id_siswa = nuj.id_siswa
                                WHERE
                                    nul.kelas = '$kelas' AND
                                    nul.semester = '$semester' AND
                                    nul.id_mapel = '$idMapel' AND
                                    nul.id_tahun_ajar = '$idTahunAjar' AND
                                    nuj.kelas = '$kelas' AND
                                    nuj.semester = '$semester' AND
                                    nuj.id_mapel = '$idMapel' AND
                                    nuj.id_tahun_ajar = '$idTahunAjar' AND
                                    s.nis = '$nis'
                                GROUP BY s.nis;
                            ";
                        
                            $queryResult = mysqli_query($conn, $query);
                        
                            // Check if there is data
                            if ($queryResult && mysqli_num_rows($queryResult) > 0) {
                                $row = mysqli_fetch_assoc($queryResult);
                        
                                $LM1 = $row['LM1'];
                                $LM2 = $row['LM2'];
                                $LM3 = $row['LM3'];
                                $LM4 = $row['LM4'];
                                $rerataUlangan = ($LM1 + $LM2 + $LM3 + $LM4) / 4;
                                $STS = $row['STS'];
                                $SAS = $row['SAS'];
                                $nilaiRapot = round(((2 * $rerataUlangan) + $STS + (2 * $SAS)) / 5);
                        
                                echo '<td>' . $nilaiRapot . '</td>';
                            } else {
                                echo '<td>-</td>';
                            }
                        }
                        

                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';

                    echo '</div>';
                    echo '</div>';

                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<form method="post" action="pdf_LEGGER.php" target="_blank">';
                    echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                    echo '<input type="hidden" name="semester" value="' . $semester . '">';
                    echo '<input type="hidden" name="kelas" value="' . $kelas . '">';
                    echo '<button type="submit" class="btn btn-primary" name="btnCetakLegger" id="btnCetakLegger">Cetak</button>';
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
