<?php
require 'function.php';
require 'cek.php';
require 'config.php';
//session_start();
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
        <title>Dashboard - Manajemen Penilaian</title>
        <style>
            .with-background {
                background-image: url('assets/img/welcome.jpg'); /* Ganti 'url-gambar-anda.jpg' dengan URL gambar yang ingin Anda gunakan */
                background-size: cover; /* Untuk mengatur gambar agar menutupi seluruh div */
                background-repeat: no-repeat; /* Agar gambar tidak diulang */
                background-position: center center; /* Agar gambar terpusat dalam div */
                /* opacity: 0.6; */
            }

            #clock {
            font-size: 5vmin; 
            text-align: right;
            margin-top: 0.5%;
            margin-right: 2%;
            color: Blue;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th:hover {
            cursor: pointer;
        }


        </style>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav">
            <?php include 'sidebar.php'; ?>
            <div id="layoutSidenav_content" class="with-background">
                <main>
                    <div class="container-fluid px-4" >
                    <br>
                    <figure class="bg-light p-4"
                            style="border-left: .35rem solid #fcdb5e; border-top: 1px solid #eee; border-right: 1px solid #eee; border-bottom: 1px solid #eee; opacity: 0.85;">
                            <blockquote class="blockquote pb-2">
                                <i><h1>
                                    Selamat datang <?=$namaUser; ?>, Anda berada di tahun ajaran <u><?=$tahunAjar;?></u> dan kelas <?=$kelas;?></h1></i>                                
                            </blockquote>
                    </figure>
                    </div>
                    <div id="clock"></div><br>
                    <div class="container-fluid px-4" >                        
                        <figure class="bg-light p-4"
                            style="border-left: .35rem solid #fcdb5e; border-top: 1px solid #eee; border-right: 1px solid #eee; border-bottom: 1px solid #eee; opacity: 0.85;">
                            <h5>Progres Pengisian Nilai : </h5>
                            <p>(Dihitung berdasarkan nilai Harian, Ulangan, dan Ujian)</p>
                            <hr>
                            <div class="row"> 
                            <?php                                
                                $bulan = date("m");
                                if ($bulan >=7 && $bulan <= 12) {
                                    $semester = 'Ganjil';
                                } else {
                                    $semester = 'Genap';
                                }

                                $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
                                $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
                                $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];
                                
                                // Eksekusi query untuk mendapatkan daftar mata pelajaran
                                if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                                    $queryMapel = mysqli_query($conn, "SELECT * FROM mapel WHERE mapel <> 'Bahasa Using';");
                                } else {
                                    $queryMapel = mysqli_query($conn, "SELECT * FROM mapel;");
                                }
                                

                                $totalProgress = 0;

                                while ($data = mysqli_fetch_array($queryMapel)) {
                                    $idMapel = $data['id_mapel'];
                                    $mapel = $data['mapel'];

                                    //1. Harian 1
                                    $queryLM1 = mysqli_query($conn, "SELECT `nilai` FROM `nilai_mapel` WHERE
                                    `id_tahun_ajar`= '$idTahunAjar' AND
                                    `semester`= '$semester' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 1' AND
                                    `tujuan_pembelajaran` = 'TP1';");

                                    if ($queryLM1 && mysqli_num_rows($queryLM1) > 0) {
                                        $totalProgress += 1;
                                    }

                                    //2. Harian 2
                                    $queryLM2 = mysqli_query($conn, "SELECT `nilai` FROM `nilai_mapel` WHERE
                                    `id_tahun_ajar`= '$idTahunAjar' AND
                                    `semester`= '$semester' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 2' AND
                                    `tujuan_pembelajaran` = 'TP1';");

                                    if ($queryLM2 && mysqli_num_rows($queryLM2) > 0) {
                                        $totalProgress += 1;
                                    }

                                    //3. Ulangan 1
                                    $queryUlangan1 = mysqli_query($conn, "SELECT `nilai` FROM `nilai_ulangan` WHERE
                                    `id_tahun_ajar`= '$idTahunAjar' AND
                                    `semester`= '$semester' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 1';");

                                    if ($queryUlangan1 && mysqli_num_rows($queryUlangan1) > 0) {
                                        $totalProgress += 1;
                                    }

                                    //4. Ulangan 2
                                    $queryUlangan2 = mysqli_query($conn, "SELECT `nilai` FROM `nilai_ulangan` WHERE
                                    `id_tahun_ajar`= '$idTahunAjar' AND
                                    `semester`= '$semester' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 2';");

                                    if ($queryUlangan2 && mysqli_num_rows($queryUlangan2) > 0) {
                                        $totalProgress += 1;
                                    }

                                    //5. Ulangan 3
                                    $queryUlangan3 = mysqli_query($conn, "SELECT `nilai` FROM `nilai_ulangan` WHERE
                                    `id_tahun_ajar`= '$idTahunAjar' AND
                                    `semester`= '$semester' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 3';");

                                    if ($queryUlangan3 && mysqli_num_rows($queryUlangan3) > 0) {
                                        $totalProgress += 1;
                                    }

                                    //6. Ulangan 4
                                    $queryUlangan4 = mysqli_query($conn, "SELECT `nilai` FROM `nilai_ulangan` WHERE
                                    `id_tahun_ajar`= '$idTahunAjar' AND
                                    `semester`= '$semester' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 4';");

                                    if ($queryUlangan4 && mysqli_num_rows($queryUlangan4) > 0) {
                                        $totalProgress += 1;
                                    }

                                    //7. Ujian 1
                                    $querySTS = mysqli_query($conn, "SELECT `nilai` FROM `nilai_ujian` WHERE
                                    `id_tahun_ajar`= '$idTahunAjar' AND
                                    `semester`= '$semester' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `ujian` = 'STS';");

                                    if ($querySTS && mysqli_num_rows($querySTS) > 0) {
                                        $totalProgress += 1;
                                    }

                                    //8. Ujian 2
                                    $querySAS = mysqli_query($conn, "SELECT `nilai` FROM `nilai_ujian` WHERE
                                    `id_tahun_ajar`= '$idTahunAjar' AND
                                    `semester`= '$semester' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `ujian` = 'SAS';");

                                    if ($querySAS && mysqli_num_rows($querySAS) > 0) {
                                        $totalProgress += 1;
                                    }

                                    $value = $totalProgress * 12.5; 

                                    // Tampilkan progres bar untuk setiap mata pelajaran menggunakan Bootstrap
                                    echo '<p style="font-weight: bold;">' . $mapel . '</p>';
                                    echo '<div class="row">';
                                    echo '<div class="col-md-8">';
                                    echo '<div class="progress">';
                                    echo '<div class="progress-bar bg-success" role="progressbar" style="width: ' . $value . '%;" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100">' . $value . '%</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '<br>';
                                    $totalProgress = 0;
                                }                                
                                
                                ?>

                            </div>
                        </figure>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Daftar Peringkat Siswa Berdasarkan Nilai :
                        </div>
                        <div class="card-body">
                            <table id="myTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th onclick="sortTable(0)">Nama  (Klik untuk mengurutkan nama)</th>
                                        <th onclick="sortTable(1)">Nilai  (KLIK di sini untuk mengurutkan peringkat)</th>
                                    </tr>
                                </thead>
                                <tbody>
                        <?php

                        $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas");

                            while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                $idSiswa = $rowSiswa['id_siswa'];
                                $namaSiswa = $rowSiswa['nama'];

                                $queryMapel = mysqli_query($conn, "SELECT * FROM mapel;");  
                                $jumlahMapel = mysqli_num_rows($queryMapel);

                                $nilaiRapotMapel = 0;
                                while ($rowMapel = mysqli_fetch_array($queryMapel)) {
                                    $idMapel = $rowMapel['id_mapel'];
                                    $mapel = $rowMapel['mapel'];

                                    //query nilai ulangan 1
                                    $query1= "SELECT MAX(nilai) AS nilai FROM `nilai_ulangan` 
                                    WHERE 
                                    `id_tahun_ajar` = '$idTahunAjar' AND 
                                    `semester` = '$semester' AND
                                    `id_siswa` = '$idSiswa' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 1';";
                                    $queryNSLM1 = mysqli_query($conn, $query1);

                                    while ($rowNSLM1 = mysqli_fetch_array($queryNSLM1)){
                                        $nilaiNSLM1 = $rowNSLM1['nilai'];
                                    }

                                    //query nilai ulangan 2
                                    $queryNSLM2 = mysqli_query($conn, "SELECT MAX(nilai) AS nilai FROM `nilai_ulangan` 
                                    WHERE 
                                    `id_tahun_ajar` = '$idTahunAjar' AND 
                                    `semester` = '$semester' AND
                                    `id_siswa` = '$idSiswa' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 2';");

                                    while ($rowNSLM2 = mysqli_fetch_array($queryNSLM2)){
                                        $nilaiNSLM2 = $rowNSLM2['nilai'];
                                    }

                                    //query nilai ulangan 3
                                    $queryNSLM3 = mysqli_query($conn, "SELECT MAX(nilai) AS nilai FROM `nilai_ulangan` 
                                    WHERE 
                                    `id_tahun_ajar` = '$idTahunAjar' AND 
                                    `semester` = '$semester' AND
                                    `id_siswa` = '$idSiswa' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 3';");

                                    while ($rowNSLM3 = mysqli_fetch_array($queryNSLM3)){
                                        $nilaiNSLM3 = $rowNSLM3['nilai'];
                                    }

                                    //query nilai ulangan 4
                                    $queryNSLM4 = mysqli_query($conn, "SELECT MAX(nilai) AS nilai FROM `nilai_ulangan` 
                                    WHERE 
                                    `id_tahun_ajar` = '$idTahunAjar' AND 
                                    `semester` = '$semester' AND
                                    `id_siswa` = '$idSiswa' AND
                                    `kelas` = '$kelas' AND 
                                    `id_mapel` = '$idMapel' AND
                                    `lingkup_materi` = 'LM 4';");

                                    while ($rowNSLM4 = mysqli_fetch_array($queryNSLM4)){
                                        $nilaiNSLM4 = $rowNSLM4['nilai'];
                                    }

                                    //query nilai STS
                                    $querySTS = mysqli_query($conn, "SELECT MAX(nilai) AS nilai FROM `nilai_ujian` 
                                    WHERE 
                                    `id_tahun_ajar` = '$idTahunAjar' AND  
                                    `semester` = '$semester' AND 
                                    `id_siswa` = '$idSiswa' AND 
                                    `kelas` = '$kelas' AND  
                                    `id_mapel` = '$idMapel' AND 
                                    `ujian` = 'STS';");

                                    while ($rowSTS = mysqli_fetch_array($querySTS)){
                                        $nilaiSTS = $rowSTS['nilai'];
                                    }

                                    //query nilai STS
                                    $querySAS = mysqli_query($conn, "SELECT MAX(nilai) AS nilai FROM `nilai_ujian` 
                                    WHERE 
                                    `id_tahun_ajar` = '$idTahunAjar' AND  
                                    `semester` = '$semester' AND 
                                    `id_siswa` = '$idSiswa' AND 
                                    `kelas` = '$kelas' AND  
                                    `id_mapel` = '$idMapel' AND 
                                    `ujian` = 'SAS';");

                                    while ($rowSAS = mysqli_fetch_array($querySAS)){
                                        $nilaiSAS = $rowSAS['nilai'];
                                    }
                                    $rerataUlangan = ($nilaiNSLM1 + $nilaiNSLM2 + $nilaiNSLM3 + $nilaiNSLM4) / 4;

                                    $nilaiLM = array();

                                    // Memasukkan nilai ke dalam array jika nilai tersebut bukan 0
                                    if ($nilaiNSLM1 != 0) {
                                        $nilaiLM[] = $nilaiNSLM1;
                                    }
                                    if ($nilaiNSLM2 != 0) {
                                        $nilaiLM[] = $nilaiNSLM2;
                                    }
                                    if ($nilaiNSLM3 != 0) {
                                        $nilaiLM[] = $nilaiNSLM3;
                                    }
                                    if ($nilaiNSLM4 != 0) {
                                        $nilaiLM[] = $nilaiNSLM4;
                                    }

                                    // Menghitung rata-rata jika ada nilai yang bukan 0
                                    if (count($nilaiLM) > 0) {
                                        $rerataUlangan = array_sum($nilaiLM) / count($nilaiLM);
                                    } else {
                                        $rerataUlangan = 0;
                                    }

                                    $nilaiRapotMapel += ((2 * $rerataUlangan) + $nilaiSTS + (2 * $nilaiSAS)) / 5;                                        
                                }

                                $nilaiRataRata = $nilaiRapotMapel / $jumlahMapel;
                                echo '<tr>
                                        <td>' . $namaSiswa . '</td>
                                        <td>' . $nilaiRataRata . '</td>
                                    </tr>';
                            }
                            ?>
                            </tbody>  
                            </table>
                        </div>
                    </div>

                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>

        <!-- script untuk jam -->
        <script>
            function updateClock() {
                var now = new Date();
                var options = { year: 'numeric', month: 'long', day: 'numeric' };
                var formattedDate = now.toLocaleDateString(undefined, options);
                var time = now.toLocaleTimeString();

                var clockElement = document.getElementById('clock');
                clockElement.innerHTML = formattedDate + ' ' + time;
                }

                // Memanggil fungsi updateClock setiap detik
                setInterval(updateClock, 1000);

                // Memanggil updateClock pada saat halaman pertama kali dimuat
                updateClock();
        </script>

        <script>
            function sortTable(columnIndex) {
                var table, rows, switching, i, x, y, shouldSwitch;
                table = document.getElementById("myTable");
                switching = true;
                while (switching) {
                    switching = false;
                    rows = table.rows;
                    for (i = 1; i < (rows.length - 1); i++) {
                        shouldSwitch = false;
                        x = rows[i].getElementsByTagName("td")[columnIndex];
                        y = rows[i + 1].getElementsByTagName("td")[columnIndex];
                        if (columnIndex === 0) { // Sorting for the "Nama" column
                            if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                                shouldSwitch = true;
                                break;
                            }
                        } else if (columnIndex === 1) { // Sorting for the "Nilai" column
                            if (Number(x.innerHTML) < Number(y.innerHTML)) {
                                shouldSwitch = true;
                                break;
                            }
                        }
                    }
                    if (shouldSwitch) {
                        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                        switching = true;
                    }
                }
            }
        </script>

    </body>
</html>

