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
                                                <label class="input-group-text" for="kategori">Mata Pelajaran</label>
                                            </div>
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
                                    echo 'III';
                                } elseif ($kelas == 3) {
                                    echo 'IV';
                                } elseif ($kelas == 4) {
                                    echo 'V';
                                } elseif ($kelas == 5) {
                                    echo 'VI';
                                } elseif ($kelas == 6) {
                                    echo 'VI';
                                } else {
                                    echo '';
                                }
                                ?>
                            </h6>
                            <h6>MATA PELAJARAN : <?=$namaMapel;?></H6>
                        </div>
                        <?php
                    }               
                    ?>
                </div>
                <?php                    

                function tabelTunggal($idTahunAjar, $bulanNum, $idKategoriLap) {
                    $conn = mysqli_connect("localhost:3306","root","","sdk");
                    // query kategori
                    $queryKategori = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori='$idKategoriLap'");
                    $rowKategori = mysqli_fetch_assoc($queryKategori);
                    $namaKategori = $rowKategori['nama_kategori'];
                    for ($kelas = 1; $kelas <= 6; $kelas++) {
                        echo '<div class="card mb-4">';
                        echo '<div class="card-header">';
                        echo '<i class="fas fa-table me-1"></i>';
                        echo 'Daftar Penerimaan kelas ' . $kelas;
                        echo '</div>';
                        echo '<div class="card-body">';
                        echo '<table id="datatablesSimple1" class="table table-bordered">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th rowspan="2" style="vertical-align: middle;">No.</th>';
                        echo '<th rowspan="2"  style="vertical-align: middle;">Nama Siswa</th>';
                        echo '<th colspan="4" style="text-align: center;">Iuran ' . $namaKategori . '</th>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td>Penetapan</td>';
                        echo '<td>Bulan ini</td>';
                        echo '<td>Tunggakan</td>';
                        echo '<td>Jumlah</td>';
                        echo '</tr>'; 
                        echo '</thead>';
                        echo '<tbody>';

                        $queryPenerimaan = "SELECT
                        s.nama AS nama_siswa,
                        SUM(CASE WHEN subkat.nama_sub_kategori = '$namaKategori' THEN tms.penetapan ELSE 0 END) AS penetapan_subkat,
                        SUM(CASE WHEN subkat.nama_sub_kategori = '$namaKategori' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_subkat,
                        SUM(CASE WHEN subkat.nama_sub_kategori = '$namaKategori' THEN tms.tunggakan ELSE 0 END) AS tunggakan_subkat,
                        SUM(CASE WHEN subkat.nama_sub_kategori = '$namaKategori' THEN tms.jumlah ELSE 0 END) AS jumlah_subkat
                        
                        FROM
                            transaksi_masuk_siswa tms
                        LEFT JOIN
                            siswa s ON tms.id_siswa = s.id_siswa
                        LEFT JOIN
                            sub_kategori_siswa subkat ON tms.id_sub_kategori = subkat.id_sub_kategori
                        WHERE
                            tms.id_tahun_ajar = $idTahunAjar AND 
                            MONTH(tms.tanggal) = $bulanNum AND
                            s.id_kelas = $kelas
                        GROUP BY
                            s.nama;
                        ";
                        $i = 1;
                        $totalPenetapanSubkat = 0;
                        $totalBulanIniSubkat = 0;
                        $totalTunggakanSubkat = 0;
                        $totalJumlahSubkat = 0;

                        $penerimaanSubkat = mysqli_query($conn, $queryPenerimaan);
                        while($rowPenerimaan=mysqli_fetch_array($penerimaanSubkat)){
                            $namaSiswa = $rowPenerimaan['nama_siswa'];                    
                            $penetapanSubkat = $rowPenerimaan['penetapan_subkat'];
                            $bulanIniSubkat = $rowPenerimaan['bulan_ini_subkat'];
                            $tunggakanSubkat = $rowPenerimaan['tunggakan_subkat'];
                            $jumlahSubkat = $rowPenerimaan['jumlah_subkat'];

                        echo '<tr>';
                        echo '<td>' . $i++ . '</td>';
                        echo '<td>' . $namaSiswa . '</td>';
                        echo '<td>Rp. ' . number_format($penetapanSubkat, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($bulanIniSubkat, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($tunggakanSubkat, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($jumlahSubkat, 0, ',', '.') . '</td>';
                        echo '</tr>';

                        // Tambahkan nilai ke total
                        $totalPenetapanSubkat += $penetapanSubkat;
                        $totalBulanIniSubkat += $bulanIniSubkat;
                        $totalTunggakanSubkat += $tunggakanSubkat;
                        $totalJumlahSubkat += $jumlahSubkat;
                    }
                            // Tampilkan baris total
                        echo '<tr>';
                        echo '<td colspan="2">Total</td>';
                        echo '<td><strong>Rp. ' . number_format($totalPenetapanSubkat, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($totalBulanIniSubkat, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($totalTunggakanSubkat, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($totalJumlahSubkat, 0, ',', '.') . '</strong></td>';
                        echo '</tr>';                            
                        echo '</tbody>';
                        echo '</table>'; 
                        echo '</div>';
                        echo '</div>';
                        echo '<br>';
                    }                       
                    
                    // Tampilkan tabel untuk total kolom per kelas
                    echo '<br>';
                    echo '<div class="card mb-4">';
                    echo '<div class="card-header">';
                    echo '<i class="fas fa-table me-1"></i>';
                    echo 'Rekapitulasi Penerimaan Uang SPP ';
                    echo '</div>';
                    echo '<div class="card-body">';
                    echo '<table id="datatablesSimple1" class="table table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th rowspan="2"  style="vertical-align: middle;">Kelas</th>';
                    echo '<th colspan="4" style="text-align: center;">Iuran ' . $namaKategori . '</th>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td>Penetapan</td>';
                    echo '<td>Bulan ini</td>';
                    echo '<td>Tunggakan</td>';
                    echo '<td>Jumlah</td>';
                    echo '</tr>'; 
                    echo '</thead>';
                    echo '<tbody>';

                    // Simpan total kolom per kelas
                    $penetapanSubkatKelas = 0;
                    $bulanIniSubkatKelas = 0;
                    $tunggakanSubkatKelas = 0;
                    $totalSubkatKelas = 0;

                    $finalPenetapanSubkat = 0;
                    $finalBulanIniSubkat = 0;
                    $finalTunggakanSubkat = 0;
                    $finalJumlahSubkat = 0;

                    // Loop untuk menghitung total kolom per kelas
                    for ($kelas = 1; $kelas <= 6; $kelas++) {

                    $queryTotal = "SELECT
                    SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = '$namaKategori' THEN tms.penetapan ELSE 0 END) AS penetapan_subkat_kelas,
                    SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = '$namaKategori' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_subkat_kelas,
                    SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = '$namaKategori' THEN tms.tunggakan ELSE 0 END) AS tunggakan_subkat_kelas,
                    SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = '$namaKategori' THEN tms.jumlah ELSE 0 END) AS total_subkat_kelas,

                    FROM
                        transaksi_masuk_siswa tms
                    LEFT JOIN
                        siswa s ON tms.id_siswa = s.id_siswa
                    LEFT JOIN
                        sub_kategori_siswa subkat ON tms.id_sub_kategori = subkat.id_sub_kategori
                    WHERE
                        tms.id_tahun_ajar = $idTahunAjar AND 
                        MONTH(tms.tanggal) = $bulanNum";
                        
                    $resultTotal = mysqli_query($conn, $queryTotal);

                    $rowKelas=mysqli_fetch_array($resultTotal);
                    $penetapanSubkatKelas =  $rowKelas['penetapan_subkat_kelas'];
                    $bulanIniSubkatKelas = $rowKelas['bulan_ini_subkat_kelas'];
                    $tunggakanSubkatKelas = $rowKelas['tunggakan_subkat_kelas'];
                    $totalSubkatKelas = $rowKelas['total_subkat_kelas'];
                    
                    echo '<tr>';
                    echo '<td>Kelas ' . $kelas . '</td>';
                    echo '<td>Rp. ' . number_format($penetapanSubkatKelas, 0, ',', '.') . '</td>';
                    echo '<td>Rp. ' . number_format($bulanIniSubkatKelas, 0, ',', '.') . '</td>';
                    echo '<td>Rp. ' . number_format($tunggakanSubkatKelas, 0, ',', '.') . '</td>';
                    echo '<td>Rp. ' . number_format($totalSubkatKelas, 0, ',', '.') . '</td>';
                    echo '</tr>';
                    
                    // Tambahkan nilai ke total
                    $finalPenetapanSubkat += $penetapanSubkatKelas;
                    $finalBulanIniSubkat += $bulanIniSubkatKelas;
                    $finalTunggakanSubkat += $tunggakanSubkatKelas;
                    $finalJumlahSubkat += $totalSubkatKelas;
                    
                    }

                    echo '<tr>';
                    echo '<td><strong>Total</strong></td>';
                    echo '<td><strong>Rp. ' . number_format($finalPenetapanSubkat, 0, ',', '.') . '</strong></td>';
                    echo '<td><strong>Rp. ' . number_format($finalBulanIniSubkat, 0, ',', '.') . '</strong></strong></td>';
                    echo '<td><strong>Rp. ' . number_format($finalTunggakanSubkat, 0, ',', '.') . '</strong></strong></td>';
                    echo '<td><strong>Rp. ' . number_format($finalJumlahSubkat, 0, ',', '.') . '</strong></strong></td>';
                    echo '</tr>';

                    echo '</tbody>';
                    echo '</table>'; 
                    echo '</div>';
                    echo '</div>';
                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<form method="post" action="pdf_lap_siswa_lain.php" target="_blank">';
                    echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                    echo '<input type="hidden" name="bulan" value="' . $bulanNum . '>';
                    echo '<input type="hidden" name="idKategori" value="' . $idKategoriLap . '">';
                    echo '<button type="submit" class="btn btn-primary" name="btnCetakLapTunggal" id="btnCetakLapTunggal">Cetak</button>';  
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
