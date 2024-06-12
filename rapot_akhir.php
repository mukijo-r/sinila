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
                        <h3 class="mt-4">Rapot Akhir</h3>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Laporan / Rapor Akhir</li>    
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
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="siswa">Tanggal Cetak</label>
                                            </div>
                                            <?php $tanggalSaatIni = date('Y-m-d');?>    
                                            <input type="date" name="tanggalCetak" value="<?=$tanggalSaatIni;?>" class="form-control"> 
                                        </div>
                                    </div>
                                    <div class="col">&nbsp;
                                        <button type="submit" class="btn btn-primary" name="btnTampilLapSiswa" id="btnTampilLapSiswa">
                                            Tampilkan
                                        </button>
                                    </div>         
                                </div>    
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

                    // Tampilkan Daftar Nilai
                    if (isset($_POST['btnTampilLapSiswa'])) {
                        $semester = $_POST['semester'];
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
                                <h5>LAPORAN HASIL BELAJAR (RAPOR)</h5>
                                <h5>SEMESTER <?=$semester;?></h5>
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

                        tabelDaftarNilai($idTahunAjar, $semester, $kelas, $idSiswa, $namaSiswa, $nomorUrut, $tanggalCetak);
                    }               
                    ?>
                </div>
                <?php
                function tabelDaftarNilai($idTahunAjar, $semester, $kelas, $idSiswa, $namaSiswa, $nomorUrut, $tanggalCetak) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");

                    $queryNilaiHarian = "SELECT m.id_mapel, m.mapel,                    
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 1' THEN nul.nilai ELSE 0 END) AS LM1,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 2' THEN nul.nilai ELSE 0 END) AS LM2,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 3' THEN nul.nilai ELSE 0 END) AS LM3,
                    MAX(CASE WHEN nul.lingkup_materi = 'LM 4' THEN nul.nilai ELSE 0 END) AS LM4,                        
                    MAX(CASE WHEN nuj.ujian = 'STS' THEN nuj.nilai ELSE 0 END) AS STS,
                    MAX(CASE WHEN nuj.ujian = 'SAS' THEN nuj.nilai ELSE 0 END) AS SAS
                    FROM mapel m                    
                    LEFT JOIN nilai_ulangan nul ON m.id_mapel = nul.id_mapel
                    LEFT JOIN nilai_ujian nuj ON m.id_mapel = nuj.id_mapel
                    WHERE 
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
                    
                    $nilaiHarian = mysqli_query($conn, $queryNilaiHarian);

                    echo '<div class="card mb-4">';
                    echo '<div class="card-header">';
                    echo '<i class="fas fa-table me-1"></i>';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<table id="datatablesSimple1" class="table table-bordered"  style="text-align: center;">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th style="vertical-align: middle; width: 3%">No.</th>';
                    echo '<th style="vertical-align: middle; text-align: left; width: 20%"">Mata Pelajaran</th>';
                    echo '<th style="vertical-align: middle; width: 5%"">Nilai</th>';
                    echo '<th style="vertical-align: middle; width: 50%"">Deskripsi</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    $i = 1;
                
                    while ($row = mysqli_fetch_array($nilaiHarian)) {
                        $mapel = $row['mapel'];

                        if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                            if ($mapel == 'Bahasa Using') {
                                continue;
                            }
                        }

                        $LM1 = $row['LM1'];
                        $LM2 = $row['LM2'];
                        $LM3 = $row['LM3'];
                        $LM4 = $row['LM4'];

                        // Array untuk menampung nilai yang bukan 0
                        $nilaiLM = array();

                        // Memasukkan nilai ke dalam array jika nilai tersebut bukan 0
                        if ($LM1 != 0) {
                            $nilaiLM[] = $LM1;
                        }
                        if ($LM2 != 0) {
                            $nilaiLM[] = $LM2;
                        }
                        if ($LM3 != 0) {
                            $nilaiLM[] = $LM3;
                        }
                        if ($LM4 != 0) {
                            $nilaiLM[] = $LM4;
                        }

                        // Menghitung rata-rata jika ada nilai yang bukan 0
                        if (count($nilaiLM) > 0) {
                            $rerataUlangan = array_sum($nilaiLM) / count($nilaiLM);
                        } else {
                            $rerataUlangan = 0;
                        }

                        $STS = $row['STS'];
                        $SAS = $row['SAS'];
                        $nilaiRapot = round(((2 * $rerataUlangan) + $STS + (2 * $SAS)) / 5);

                        $queryMapel = mysqli_query($conn, "SELECT id_mapel
                        FROM mapel                  
                        WHERE                                         
                        `mapel` = '$mapel';");

                        while ($rowMapel = mysqli_fetch_array($queryMapel)){
                        $idMapel = $rowMapel['id_mapel'];
                        }

                        $queryDeskripsi1 = "SELECT GROUP_CONCAT(ck.deskripsi SEPARATOR '; ') AS combined_deskripsi1
                        FROM
                        `asesmen_capkom` ack
                        LEFT JOIN capaian_kompetensi ck ON ack.id_ck = ck.id_ck  
                        LEFT JOIN siswa s ON ack.id_siswa = s.id_siswa                                  
                        WHERE
                        semester = '$semester' AND
                        ack.id_siswa = '$idSiswa' AND
                        ack.tampil = '1' AND
                        ack.capaian = '1' AND
                        ack.kelas = '$kelas' AND
                        ck.id_mapel = '$idMapel';";                        

                        $result1 = mysqli_query($conn, $queryDeskripsi1);

                        if ($result1) {
                            $row1 = mysqli_fetch_assoc($result1);
                            $combinedDeskripsi1 = $row1['combined_deskripsi1'];
                        }

                        $queryDeskripsi0 = "SELECT GROUP_CONCAT(ck.deskripsi SEPARATOR '; ') AS combined_deskripsi0
                        FROM
                        `asesmen_capkom` ack
                        LEFT JOIN capaian_kompetensi ck ON ack.id_ck = ck.id_ck  
                        LEFT JOIN siswa s ON ack.id_siswa = s.id_siswa                                  
                        WHERE
                        semester = '$semester' AND
                        ack.id_siswa = '$idSiswa' AND
                        ack.tampil = '1' AND
                        ack.capaian = '0' AND
                        ack.kelas = '$kelas' AND
                        ck.id_mapel = '$idMapel';";

                        $result0 = mysqli_query($conn, $queryDeskripsi0);

                        if ($result0) {
                            $row0 = mysqli_fetch_assoc($result0);
                            $combinedDeskripsi0 = $row0['combined_deskripsi0'];
                        } 

                        echo '<tr>';
                        echo '<td rowspan="2">' . $i++ . '</td>';
                        echo '<td rowspan="2" style="text-align: left;">' . $mapel . '</td>';
                        echo '<td rowspan="2">' . $nilaiRapot . '</td>';
                        if ($combinedDeskripsi1 <> '') {
                            if ($nilaiRapot > 74){
                                echo '<td style="text-align: left;">'. $namaSiswa . ' memiliki pemahaman dalam ' . $combinedDeskripsi1 .'.</td>';
                            } else {
                                echo '<td style="text-align: left;">'. $namaSiswa . ' membutuhkan bantuan dalam ' . $combinedDeskripsi1 .'.</td>';
                            } 
                        } else {
                            echo '<td style="text-align: left;"></td>';
                        }
                        echo '</tr>';
                        echo '<tr>';
                        if ($combinedDeskripsi0 <> '') {
                                echo '<td style="text-align: left;">'. $namaSiswa . ' membutuhkan bantuan dalam ' . $combinedDeskripsi0 .'.</td>';
                        } else {
                            echo '<td style="text-align: left;"></td>';
                        }
                        echo '</tr>';
                    }

                    echo '</tbody>';
                    echo '</table>';
                    echo '<br><br>';
                    
                    echo '<table class="table table-bordered" style="width: 100%; font-family: times; font-size: 12px">';
                    echo '<tr>';
                    echo '<th border="1" style="line-height: 1.5; width: 5%; text-align: center; vertical-align: middle; font-weight: bold;"><br>No.</th>';
                    echo '<th border="1" style="line-height: 1.5; width: 25%; text-align: center; vertical-align: middle; font-weight: bold;"><br>Ekstrakurikuler</th>';
                    echo '<th border="1" style="line-height: 1.5; width: 62%; text-align: center; vertical-align: middle; font-weight: bold;"><br>Keterangan</th>';
                    echo '</tr>';
                    
                    $queryCatatanEkstra = "SELECT
                        ek.nama_ek,
                        catatan
                        FROM nilai_catatan_ekstrakurikuler nce
                        LEFT JOIN ekstrakurikuler ek ON nce.id_ek = ek.id_ek
                        WHERE id_siswa = $idSiswa";
                    
                    $j = 1;
                    $catatanEkstra = mysqli_query($conn, $queryCatatanEkstra);
                    
                    while ($rowCatatanEkstra = mysqli_fetch_array($catatanEkstra)) {
                        $namaEkstra = $rowCatatanEkstra['nama_ek'];
                        $catatan = $rowCatatanEkstra['catatan'];
                    
                        echo '<tr>'; 
                        echo '<td border="1" style="line-height: 1.5; width: 5%; text-align: center; vertical-align: middle; font-weight: bold;">' . $j . '</td>';
                        echo '<td border="1" style="line-height: 1.5; width: 25%; text-align: center; vertical-align: middle; font-weight: bold;">' . $namaEkstra . '</td>';
                        echo '<td border="1" style="line-height: 1.5; width: 62%; text-align: center; vertical-align: middle; font-weight: bold;">' . $catatan . '</td>';
                        echo '</tr>'; 
                        $j++;
                    }
                    
                    echo '</table><br><br>';
                    

                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<form method="post" action="pdf_rapot_akhir.php" target="_blank">';
                    echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                    echo '<input type="hidden" name="semester" value="' . $semester . '">';
                    echo '<input type="hidden" name="kelas" value="' . $kelas . '">';
                    echo '<input type="hidden" name="idSiswa" value="' . $idSiswa . '">';
                    echo '<input type="hidden" name="nomorUrut" value="' . $nomorUrut . '">';   
                    echo '<input type="hidden" name="tanggalCetak" value="' . $tanggalCetak . '">';                   
                    echo '<button type="submit" class="btn btn-primary" name="btnCetakRapotSisipan" id="btnCetakRapotSisipan">Cetak</button>';
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

 
