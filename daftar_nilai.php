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
                
                        $queryKategori = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori='$idKategoriLap'");
                        $rowKategori = mysqli_fetch_assoc($queryKategori);
                        $namaKategori = $rowKategori['nama_kategori'];
                
                        ?>
                        <div class="row" style="text-align: center; border: none">
                            <div class="col-md-3" style="text-align: right; border: none">
                            </div>
                            <div class="col-md-6">
                                <h5>Daftar Nilai Semester <?=$semester;?></h5>
                                <h5>SD Katolik Bhakti Rogojampi </h5>
                                <h5>Tahun Pelajaran <?=$tahunAjar; ?></h5>
                            </div>
                        </div><br>
                        <?php
                        // Tampilkan tabel sesuai dengan kondisi
                        if ($idKategoriLap == 4) {
                            // Tampilkan tabel Ekstrakurikuler
                            tabelEkstra($idTahunAjar, $bulanNum, $idKategoriLap);
                        } elseif ($idKategoriLap == 10) {
                            // Tampilkan tabel Ujian
                            tabelUjian($idTahunAjar, $bulanNum, $idKategoriLap);                            
                        } elseif ($idKategoriLap == 1) {
                            // Tampilkan tabel biasa
                            tabelSpp($idTahunAjar, $bulanNum, $idKategoriLap);
                        } else {
                            // Tampilkan tabel biasa
                            tabelTunggal($idTahunAjar, $bulanNum, $idKategoriLap);
                        }
                    }                

                    ?>
                </div>
                <?php                    
                function tabelEkstra($idTahunAjar, $bulanNum, $idKategoriLap) {
                        $conn = mysqli_connect("localhost:3306","root","","sdk");
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
                            echo '<th colspan="4" style="text-align: center;">Iuran Kegiatan</th>';
                            echo '<th colspan="4" style="text-align: center;">Iuran Pramuka</th>';
                            echo '<th colspan="4" style="text-align: center;">Iuran Komputer</th>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td>Penetapan</td>';
                            echo '<td>Bulan ini</td>';
                            echo '<td>Tunggakan</td>';
                            echo '<td>Jumlah</td>';
                            echo '<td>Penetapan</td>';
                            echo '<td>Bulan ini</td>';
                            echo '<td>Tunggakan</td>';
                            echo '<td>Jumlah</td>';
                            echo '<td>Penetapan</td>';
                            echo '<td>Bulan ini</td>';
                            echo '<td>Tunggakan</td>';
                            echo '<td>Jumlah</td>';
                            echo '</tr>'; 
                            echo '</thead>';
                            echo '<tbody>';

                            $queryPenerimaan = "SELECT
                            s.nama AS nama_siswa,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Kegiatan' THEN tms.penetapan ELSE 0 END) AS penetapan_kegiatan,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Pramuka' THEN tms.penetapan ELSE 0 END) AS penetapan_pramuka,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Komputer' THEN tms.penetapan ELSE 0 END) AS penetapan_komputer,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Kegiatan' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_kegiatan,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Pramuka' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_pramuka,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Komputer' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_komputer,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Kegiatan' THEN tms.tunggakan ELSE 0 END) AS tunggakan_kegiatan,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Pramuka' THEN tms.tunggakan ELSE 0 END) AS tunggakan_pramuka,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Komputer' THEN tms.tunggakan ELSE 0 END) AS tunggakan_komputer,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Kegiatan' THEN tms.jumlah ELSE 0 END) AS jumlah_kegiatan,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Pramuka' THEN tms.jumlah ELSE 0 END) AS jumlah_pramuka,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'Komputer' THEN tms.jumlah ELSE 0 END) AS jumlah_komputer

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
                            $totalPenetapanKegiatan = 0;
                            $totalBulanIniKegiatan = 0;
                            $totalTunggakanKegiatan = 0;
                            $totalJumlahKegiatan = 0;

                            $totalPenetapanPramuka = 0;
                            $totalBulanIniPramuka = 0;
                            $totalTunggakanPramuka = 0;
                            $totalJumlahPramuka = 0;

                            $totalPenetapanKomputer = 0;
                            $totalBulanIniKomputer = 0;
                            $totalTunggakanKomputer = 0;
                            $totalJumlahKomputer = 0;

                            $penerimaanEkstra = mysqli_query($conn, $queryPenerimaan);
                            while($rowPenerimaan=mysqli_fetch_array($penerimaanEkstra)){
                                $namaSiswa = $rowPenerimaan['nama_siswa'];                    
                                $penetapanKegiatan = $rowPenerimaan['penetapan_kegiatan'];
                                $bulanIniKegiatan = $rowPenerimaan['bulan_ini_kegiatan'];
                                $tunggakanKegiatan = $rowPenerimaan['tunggakan_kegiatan'];
                                $jumlahKegiatan = $rowPenerimaan['jumlah_kegiatan'];
                                $penetapanPramuka = $rowPenerimaan['penetapan_pramuka'];
                                $bulanIniPramuka = $rowPenerimaan['bulan_ini_pramuka'];
                                $tunggakanPramuka = $rowPenerimaan['tunggakan_pramuka'];
                                $jumlahPramuka = $rowPenerimaan['jumlah_pramuka'];
                                $penetapanKomputer = $rowPenerimaan['penetapan_komputer'];
                                $bulanIniKomputer = $rowPenerimaan['bulan_ini_komputer'];
                                $tunggakanKomputer = $rowPenerimaan['tunggakan_komputer'];
                                $jumlahKomputer = $rowPenerimaan['jumlah_komputer'];

                            echo '<tr>';
                            echo '<td>' . $i++ . '</td>';
                            echo '<td>' . $namaSiswa . '</td>';
                            echo '<td>Rp. ' . number_format($penetapanKegiatan, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($bulanIniKegiatan, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($tunggakanKegiatan, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($jumlahKegiatan, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($penetapanPramuka, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($bulanIniPramuka, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($tunggakanPramuka, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($jumlahPramuka, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($penetapanKomputer, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($bulanIniKomputer, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($tunggakanKomputer, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($jumlahKomputer, 0, ',', '.') . '</td>';
                            echo '</tr>';

                            // Tambahkan nilai ke total
                            $totalPenetapanKegiatan += $penetapanKegiatan;
                            $totalBulanIniKegiatan += $bulanIniKegiatan;
                            $totalTunggakanKegiatan += $tunggakanKegiatan;
                            $totalJumlahKegiatan += $jumlahKegiatan;

                            $totalPenetapanPramuka += $penetapanPramuka;
                            $totalBulanIniPramuka += $bulanIniPramuka;
                            $totalTunggakanPramuka += $tunggakanPramuka;
                            $totalJumlahPramuka += $jumlahPramuka;

                            $totalPenetapanKomputer += $penetapanKomputer;
                            $totalBulanIniKomputer += $bulanIniKomputer;
                            $totalTunggakanKomputer += $tunggakanKomputer;
                            $totalJumlahKomputer += $jumlahKomputer;
                            }
                                // Tampilkan baris total
                            echo '<tr>';
                            echo '<td colspan="2">Total</td>';
                            echo '<td><strong>Rp. ' . number_format($totalPenetapanKegiatan, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalBulanIniKegiatan, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalTunggakanKegiatan, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalJumlahKegiatan, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalPenetapanPramuka, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalBulanIniPramuka, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalTunggakanPramuka, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalJumlahPramuka, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalPenetapanKomputer, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalBulanIniKomputer, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalTunggakanKomputer, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalJumlahKomputer, 0, ',', '.') . '</strong></td>';
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
                        echo 'Rekapitulasi Penerimaan Uang Kegiatan, Pramuka, Komputer ';
                        echo '</div>';
                        echo '<div class="card-body">';
                        echo '<table id="datatablesSimple1" class="table table-bordered">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th rowspan="2"  style="vertical-align: middle;">Kelas</th>';
                        echo '<th colspan="4" style="text-align: center;">Iuran Kegiatan</th>';
                        echo '<th colspan="4" style="text-align: center;">Iuran Pramuka</th>';
                        echo '<th colspan="4" style="text-align: center;">Iuran Komputer</th>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td>Penetapan</td>';
                        echo '<td>Bulan ini</td>';
                        echo '<td>Tunggakan</td>';
                        echo '<td>Jumlah</td>';
                        echo '<td>Penetapan</td>';
                        echo '<td>Bulan ini</td>';
                        echo '<td>Tunggakan</td>';
                        echo '<td>Jumlah</td>';
                        echo '<td>Penetapan</td>';
                        echo '<td>Bulan ini</td>';
                        echo '<td>Tunggakan</td>';
                        echo '<td>Jumlah</td>';
                        echo '</tr>'; 
                        echo '</thead>';
                        echo '<tbody>';

                        // Simpan total kolom per kelas
                        $penetapanKegiatanKelas = 0;
                        $bulanIniKegiatanKelas = 0;
                        $tunggakanKegiatanKelas = 0;
                        $totalKegiatanKelas = 0;                        

                        $penetapanPramukaKelas = 0;
                        $bulanIniPramukaKelas = 0;
                        $tunggakanPramukaKelas = 0;
                        $totalPramukaKelas = 0; 

                        $penetapanKomputerKelas = 0;
                        $bulanIniKomputerKelas = 0;
                        $tunggakanKomputerKelas = 0; 
                        $totalKomputerKelas = 0;

                        $finalPenetapanKegiatan = 0;
                        $finalBulanIniKegiatan = 0;
                        $finalTunggakanKegiatan = 0;
                        $finalJumlahKegiatan = 0;

                        $finalPenetapanPramuka = 0;
                        $finalBulanIniPramuka = 0;
                        $finalTunggakanPramuka = 0;
                        $finalJumlahPramuka = 0;

                        $finalPenetapanKomputer = 0;
                        $finalBulanIniKomputer = 0;
                        $finalTunggakanKomputer = 0;
                        $finalJumlahKomputer = 0;

                        // Loop untuk menghitung total kolom per kelas
                        for ($kelas = 1; $kelas <= 6; $kelas++) {

                        $queryTotal = "SELECT
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Kegiatan' THEN tms.penetapan ELSE 0 END) AS penetapan_kegiatan_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Pramuka' THEN tms.penetapan ELSE 0 END) AS penetapan_pramuka_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Komputer' THEN tms.penetapan ELSE 0 END) AS penetapan_komputer_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Kegiatan' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_kegiatan_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Pramuka' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_pramuka_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Komputer' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_komputer_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Kegiatan' THEN tms.tunggakan ELSE 0 END) AS tunggakan_kegiatan_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Pramuka' THEN tms.tunggakan ELSE 0 END) AS tunggakan_pramuka_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Komputer' THEN tms.tunggakan ELSE 0 END) AS tunggakan_komputer_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Kegiatan' THEN tms.jumlah ELSE 0 END) AS total_kegiatan_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Pramuka' THEN tms.jumlah ELSE 0 END) AS total_pramuka_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'Komputer' THEN tms.jumlah ELSE 0 END) AS total_komputer_kelas

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
                        $penetapanKegiatanKelas =  $rowKelas['penetapan_kegiatan_kelas'];
                        $bulanIniKegiatanKelas = $rowKelas['bulan_ini_kegiatan_kelas'];
                        $tunggakanKegiatanKelas =  $rowKelas['tunggakan_kegiatan_kelas'];
                        $totalKegiatanKelas = $rowKelas['total_kegiatan_kelas'];
                        
                        $penetapanPramukaKelas = $rowKelas['penetapan_pramuka_kelas'];
                        $bulanIniPramukaKelas = $rowKelas['bulan_ini_pramuka_kelas'];
                        $tunggakanPramukaKelas = $rowKelas['tunggakan_pramuka_kelas'];
                        $totalPramukaKelas = $rowKelas['total_pramuka_kelas'];

                        $penetapanKomputerKelas = $rowKelas['penetapan_komputer_kelas'];
                        $bulanIniKomputerKelas = $rowKelas['bulan_ini_komputer_kelas'];
                        $tunggakanKomputerKelas = $rowKelas['tunggakan_komputer_kelas'];
                        $totalKomputerKelas = $rowKelas['total_komputer_kelas'];     
                        
                        echo '<tr>';
                        echo '<td>Kelas ' . $kelas . '</td>';
                        echo '<td>Rp. ' . number_format($penetapanKegiatanKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($bulanIniKegiatanKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($tunggakanKegiatanKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($totalKegiatanKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($penetapanPramukaKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($bulanIniPramukaKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($tunggakanPramukaKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($totalPramukaKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($penetapanKomputerKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($bulanIniKomputerKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($tunggakanKomputerKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($totalKomputerKelas, 0, ',', '.') . '</td>';
                        echo '</tr>';
                        
                        // Tambahkan nilai ke total
                        $finalPenetapanKegiatan += $penetapanKegiatanKelas;
                        $finalBulanIniKegiatan += $bulanIniKegiatanKelas;
                        $finalTunggakanKegiatan += $tunggakanKegiatanKelas;
                        $finalJumlahKegiatan += $totalKegiatanKelas;

                        $finalPenetapanPramuka += $penetapanPramukaKelas;
                        $finalBulanIniPramuka += $bulanIniPramukaKelas;
                        $finalTunggakanPramuka += $tunggakanPramukaKelas;
                        $finalJumlahPramuka += $totalPramukaKelas;

                        $finalPenetapanKomputer += $penetapanKomputerKelas;
                        $finalBulanIniKomputer += $bulanIniKomputerKelas;
                        $finalTunggakanKomputer += $tunggakanKomputerKelas;
                        $finalJumlahKomputer += $totalKomputerKelas;
                        
                        }

                        echo '<tr>';
                        echo '<td><strong>Total</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalPenetapanKegiatan, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalBulanIniKegiatan, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalTunggakanKegiatan, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalJumlahKegiatan, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalPenetapanPramuka, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalBulanIniPramuka, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalTunggakanPramuka, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalJumlahPramuka, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalPenetapanKomputer, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalBulanIniKomputer, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalTunggakanKomputer, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalJumlahKomputer, 0, ',', '.') . '</strong></td>';
                        echo '</tr>';
                        echo '</tbody>';
                        echo '</table>';  
                        echo '</div>';
                        echo '</div><br>';
                        echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                        echo '<form method="post" action="pdf_ekstra.php" target="_blank">';
                        echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                        echo '<input type="hidden" name="bulan" value="' . $bulanNum . '">';
                        echo '<input type="hidden" name="idKategori" value="' . $idKategoriLap . '">';
                        echo '<button type="submit" class="btn btn-primary" name="btnCetakLapEkstra" id="btnCetakLapEkstra">Cetak</button>';  
                        echo '</form>';                      
                        echo '</div><br>';  
                }

                function tabelUjian($idTahunAjar, $bulanNum, $idKategoriLap) {
                        $conn = mysqli_connect("localhost:3306","root","","sdk");
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
                            echo '<th colspan="4" style="text-align: center;">Iuran PTS</th>';
                            echo '<th colspan="4" style="text-align: center;">Iuran PAS</th>';
                            echo '<th colspan="4" style="text-align: center;">Iuran US</th>';
                            echo '</tr>';
                            echo '<tr>';
                            echo '<td>Penetapan</td>';
                            echo '<td>Bulan ini</td>';
                            echo '<td>Tunggakan</td>';
                            echo '<td>Jumlah</td>';
                            echo '<td>Penetapan</td>';
                            echo '<td>Bulan ini</td>';
                            echo '<td>Tunggakan</td>';
                            echo '<td>Jumlah</td>';
                            echo '<td>Penetapan</td>';
                            echo '<td>Bulan ini</td>';
                            echo '<td>Tunggakan</td>';
                            echo '<td>Jumlah</td>';
                            echo '</tr>'; 
                            echo '</thead>';
                            echo '<tbody>';

                            $queryPenerimaan = "SELECT
                            s.nama AS nama_siswa,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'PTS' THEN tms.penetapan ELSE 0 END) AS penetapan_pts,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'PAS' THEN tms.penetapan ELSE 0 END) AS penetapan_pas,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'US' THEN tms.penetapan ELSE 0 END) AS penetapan_us,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'PTS' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_pts,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'PAS' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_pas,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'US' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_us,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'PTS' THEN tms.tunggakan ELSE 0 END) AS tunggakan_pts,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'PAS' THEN tms.tunggakan ELSE 0 END) AS tunggakan_pas,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'US' THEN tms.tunggakan ELSE 0 END) AS tunggakan_us,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'PTS' THEN tms.jumlah ELSE 0 END) AS jumlah_pts,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'PAS' THEN tms.jumlah ELSE 0 END) AS jumlah_pas,
                            SUM(CASE WHEN subkat.nama_sub_kategori = 'US' THEN tms.jumlah ELSE 0 END) AS jumlah_us

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
                            $totalPenetapanPts = 0;
                            $totalBulanIniPts = 0;
                            $totalTunggakanPts = 0;
                            $totalJumlahPts = 0;

                            $totalPenetapanPas = 0;
                            $totalBulanIniPas = 0;
                            $totalTunggakanPas = 0;
                            $totalJumlahPas = 0;

                            $totalPenetapanUs = 0;
                            $totalBulanIniUs = 0;
                            $totalTunggakanUs = 0;
                            $totalJumlahUs = 0;

                            $penerimaanEkstra = mysqli_query($conn, $queryPenerimaan);
                            while($rowPenerimaan=mysqli_fetch_array($penerimaanEkstra)){
                                $namaSiswa = $rowPenerimaan['nama_siswa'];                    
                                $penetapanPts = $rowPenerimaan['penetapan_pts'];
                                $bulanIniPts = $rowPenerimaan['bulan_ini_pts'];
                                $tunggakanPts = $rowPenerimaan['tunggakan_pts'];
                                $jumlahPts = $rowPenerimaan['jumlah_pts'];
                                $penetapanPas = $rowPenerimaan['penetapan_pas'];
                                $bulanIniPas = $rowPenerimaan['bulan_ini_pas'];
                                $tunggakanPas = $rowPenerimaan['tunggakan_pas'];
                                $jumlahPas = $rowPenerimaan['jumlah_pas'];
                                $penetapanUs = $rowPenerimaan['penetapan_us'];
                                $bulanIniUs = $rowPenerimaan['bulan_ini_us'];
                                $tunggakanUs = $rowPenerimaan['tunggakan_us'];
                                $jumlahUs = $rowPenerimaan['jumlah_us'];

                            echo '<tr>';
                            echo '<td>' . $i++ . '</td>';
                            echo '<td>' . $namaSiswa . '</td>';
                            echo '<td>Rp. ' . number_format($penetapanPts, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($bulanIniPts, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($tunggakanPts, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($jumlahPts, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($penetapanPas, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($bulanIniPas, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($tunggakanPas, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($jumlahPas, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($penetapanUs, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($bulanIniUs, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($tunggakanUs, 0, ',', '.') . '</td>';
                            echo '<td>Rp. ' . number_format($jumlahUs, 0, ',', '.') . '</td>';
                            echo '</tr>';

                            // Tambahkan nilai ke total
                            $totalPenetapanPts += $penetapanPts;
                            $totalBulanIniPts += $bulanIniPts;
                            $totalTunggakanPts += $tunggakanPts;
                            $totalJumlahPts += $jumlahPts;

                            $totalPenetapanPas += $penetapanPas;
                            $totalBulanIniPas += $bulanIniPas;
                            $totalTunggakanPas += $tunggakanPas;
                            $totalJumlahPas += $jumlahPas;

                            $totalPenetapanUs += $penetapanUs;
                            $totalBulanIniUs += $bulanIniUs;
                            $totalTunggakanUs += $tunggakanUs;
                            $totalJumlahUs += $jumlahUs;
                        }
                                // Tampilkan baris total
                            echo '<tr>';
                            echo '<td colspan="2">Total</td>';
                            echo '<td><strong>Rp. ' . number_format($totalPenetapanPts, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalBulanIniPts, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalTunggakanPts, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalJumlahPts, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalPenetapanPas, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalBulanIniPas, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalTunggakanPas, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalJumlahPas, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalPenetapanUs, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalBulanIniUs, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalTunggakanUs, 0, ',', '.') . '</strong></td>';
                            echo '<td><strong>Rp. ' . number_format($totalJumlahUs, 0, ',', '.') . '</strong></td>';
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
                        echo 'Rekapitulasi Penerimaan Uang PTS, PAS, US ';
                        echo '</div>';
                        echo '<div class="card-body">';
                        echo '<table id="datatablesSimple1" class="table table-bordered">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th rowspan="2"  style="vertical-align: middle;">Kelas</th>';
                        echo '<th colspan="4" style="text-align: center;">Iuran PTS</th>';
                        echo '<th colspan="4" style="text-align: center;">Iuran PAS</th>';
                        echo '<th colspan="4" style="text-align: center;">Iuran US</th>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td>Penetapan</td>';
                        echo '<td>Bulan ini</td>';
                        echo '<td>Tunggakan</td>';
                        echo '<td>Jumlah</td>';
                        echo '<td>Penetapan</td>';
                        echo '<td>Bulan ini</td>';
                        echo '<td>Tunggakan</td>';
                        echo '<td>Jumlah</td>';
                        echo '<td>Penetapan</td>';
                        echo '<td>Bulan ini</td>';
                        echo '<td>Tunggakan</td>';
                        echo '<td>Jumlah</td>';
                        echo '</tr>'; 
                        echo '</thead>';
                        echo '<tbody>';

                        // Simpan total kolom per kelas
                        $penetapanPtsKelas = 0;
                        $bulanIniPtsKelas = 0;
                        $tunggakanPtsKelas = 0;
                        $totalPtsKelas = 0;                        

                        $penetapanPasKelas = 0;
                        $bulanIniPasKelas = 0;
                        $tunggakanPasKelas = 0;
                        $totalPasKelas = 0; 

                        $penetapanUsKelas = 0;
                        $bulanIniUsKelas = 0;
                        $tunggakanUsKelas = 0; 
                        $totalUsKelas = 0;

                        $finalPenetapanPts = 0;
                        $finalBulanIniPts = 0;
                        $finalTunggakanPts = 0;
                        $finalJumlahPts = 0;

                        $finalPenetapanPas = 0;
                        $finalBulanIniPas = 0;
                        $finalTunggakanPas = 0;
                        $finalJumlahPas = 0;

                        $finalPenetapanUs = 0;
                        $finalBulanIniUs = 0;
                        $finalTunggakanUs = 0;
                        $finalJumlahUs = 0;

                        // Loop untuk menghitung total kolom per kelas
                        for ($kelas = 1; $kelas <= 6; $kelas++) {

                        $queryTotal = "SELECT
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'PTS' THEN tms.penetapan ELSE 0 END) AS penetapan_pts_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'PAS' THEN tms.penetapan ELSE 0 END) AS penetapan_pas_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'US' THEN tms.penetapan ELSE 0 END) AS penetapan_us_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'PTS' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_pts_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'PAS' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_pas_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'US' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_us_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'PTS' THEN tms.tunggakan ELSE 0 END) AS tunggakan_pts_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'PAS' THEN tms.tunggakan ELSE 0 END) AS tunggakan_pas_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'US' THEN tms.tunggakan ELSE 0 END) AS tunggakan_us_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'PTS' THEN tms.jumlah ELSE 0 END) AS total_pts_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'PAS' THEN tms.jumlah ELSE 0 END) AS total_pas_kelas,
                        SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'US' THEN tms.jumlah ELSE 0 END) AS total_us_kelas

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
                        $penetapanPtsKelas =  $rowKelas['penetapan_pts_kelas'];
                        $bulanIniPtsKelas = $rowKelas['bulan_ini_pts_kelas'];
                        $tunggakanPtsKelas =  $rowKelas['tunggakan_pts_kelas'];
                        $totalPtsKelas = $rowKelas['total_pts_kelas'];
                        
                        $penetapanPasKelas = $rowKelas['penetapan_pas_kelas'];
                        $bulanIniPasKelas = $rowKelas['bulan_ini_pas_kelas'];
                        $tunggakanPasKelas = $rowKelas['tunggakan_pas_kelas'];
                        $totalPasKelas = $rowKelas['total_pas_kelas'];

                        $penetapanUsKelas = $rowKelas['penetapan_us_kelas'];
                        $bulanIniUsKelas = $rowKelas['bulan_ini_us_kelas'];
                        $tunggakanUsKelas = $rowKelas['tunggakan_us_kelas'];
                        $totalUsKelas = $rowKelas['total_us_kelas'];                                                                       
                        
                        echo '<tr>';
                        echo '<td>Kelas ' . $kelas . '</td>';
                        echo '<td>Rp. ' . number_format($penetapanPtsKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($bulanIniPtsKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($tunggakanPtsKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($totalPtsKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($penetapanPasKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($bulanIniPasKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($tunggakanPasKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($totalPasKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($penetapanUsKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($bulanIniUsKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($tunggakanUsKelas, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($totalUsKelas, 0, ',', '.') . '</td>';
                        echo '</tr>';
                        
                        // Tambahkan nilai ke total
                        $finalPenetapanPts += $penetapanPtsKelas;
                        $finalBulanIniPts += $bulanIniPtsKelas;
                        $finalTunggakanPts += $tunggakanPtsKelas;
                        $finalJumlahPts += $totalPtsKelas;

                        $finalPenetapanPas += $penetapanPasKelas;
                        $finalBulanIniPas += $bulanIniPasKelas;
                        $finalTunggakanPas += $tunggakanPasKelas;
                        $finalJumlahPas += $totalPasKelas;

                        $finalPenetapanUs += $penetapanUsKelas;
                        $finalBulanIniUs += $bulanIniUsKelas;
                        $finalTunggakanUs += $tunggakanUsKelas;
                        $finalJumlahUs += $totalUsKelas;
                        
                        }

                        echo '<tr>';
                        echo '<td><strong>Total</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalPenetapanPts, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalBulanIniPts, 0, ',', '.') . '</strong></td>';//bulan ini kegiatan
                        echo '<td><strong>Rp. ' . number_format($finalTunggakanPts, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalJumlahPts, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalPenetapanPas, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalBulanIniPas, 0, ',', '.') . '</strong></td>';//bulan ini pramuka
                        echo '<td><strong>Rp. ' . number_format($finalTunggakanPas, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalJumlahPas, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalPenetapanUs, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalBulanIniUs, 0, ',', '.') . '</strong></td>';//bulan ini komputer
                        echo '<td><strong>Rp. ' . number_format($finalTunggakanUs, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($finalJumlahUs, 0, ',', '.') . '</strong></td>';
                        echo '</tr>';
                        echo '</tbody>';
                        echo '</table>';  
                        echo '</div>';
                        echo '</div><br>';
                        echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                        echo '<form method="post" action="pdf_ujian.php" target="_blank">';
                        echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                        echo '<input type="hidden" name="bulan" value="' . $bulanNum . '">';
                        echo '<input type="hidden" name="idKategori" value="' . $idKategoriLap . '">';
                        echo '<button type="submit" class="btn btn-primary" name="btnCetakLapUjian" id="btnCetakLapUjian">Cetak</button>';  
                        echo '</form>';                      
                        echo '</div><br>';  
                }

                function tabelSpp($idTahunAjar, $bulanNum, $idKategoriLap) {
                    $conn = mysqli_connect("localhost:3306","root","","sdk");
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
                        echo '<th colspan="4" style="text-align: center;">Iuran SPP</th>';
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
                        SUM(CASE WHEN subkat.nama_sub_kategori = 'SPP' THEN tms.penetapan ELSE 0 END) AS penetapan_spp,
                        SUM(CASE WHEN subkat.nama_sub_kategori = 'SPP' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_spp,
                        SUM(CASE WHEN subkat.nama_sub_kategori = 'SPP' THEN tms.tunggakan ELSE 0 END) AS tunggakan_spp,
                        SUM(CASE WHEN subkat.nama_sub_kategori = 'SPP' THEN tms.jumlah ELSE 0 END) AS jumlah_spp
                        
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
                        $totalPenetapanSpp = 0;
                        $totalbulanIniSpp = 0;
                        $totalTunggakanSpp = 0;
                        $totalJumlahSpp = 0;

                        $penerimaanEkstra = mysqli_query($conn, $queryPenerimaan);
                        while($rowPenerimaan=mysqli_fetch_array($penerimaanEkstra)){
                            $namaSiswa = $rowPenerimaan['nama_siswa'];                    
                            $penetapanSpp = $rowPenerimaan['penetapan_spp'];
                            $bulanIniSpp = $rowPenerimaan['bulan_ini_spp'];
                            $tunggakanSpp = $rowPenerimaan['tunggakan_spp'];
                            $jumlahSpp = $rowPenerimaan['jumlah_spp'];

                        echo '<tr>';
                        echo '<td>' . $i++ . '</td>';
                        echo '<td>' . $namaSiswa . '</td>';
                        echo '<td>Rp. ' . number_format($penetapanSpp, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($bulanIniSpp, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($tunggakanSpp, 0, ',', '.') . '</td>';
                        echo '<td>Rp. ' . number_format($jumlahSpp, 0, ',', '.') . '</td>';
                        echo '</tr>';

                        // Tambahkan nilai ke total
                        $totalPenetapanSpp += $penetapanSpp;
                        $totalbulanIniSpp += $bulanIniSpp;
                        $totalTunggakanSpp += $tunggakanSpp;
                        $totalJumlahSpp += $jumlahSpp;
                        }
                            // Tampilkan baris total
                        echo '<tr>';
                        echo '<td colspan="2">Total</td>';
                        echo '<td><strong>Rp. ' . number_format($totalPenetapanSpp, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($totalbulanIniSpp, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($totalTunggakanSpp, 0, ',', '.') . '</strong></td>';
                        echo '<td><strong>Rp. ' . number_format($totalJumlahSpp, 0, ',', '.') . '</strong></td>';
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
                    echo '<th colspan="4" style="text-align: center;">Iuran SPP</th>';
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
                    $penetapanSppKelas = 0;
                    $bulanIniSppKelas = 0;
                    $tunggakanSppKelas = 0;
                    $totalSppKelas = 0;

                    $finalPenetapanSpp = 0;
                    $finalBulanIniSpp = 0;
                    $finalTunggakanSpp = 0;
                    $finalJumlahSpp = 0;

                    // Loop untuk menghitung total kolom per kelas
                    for ($kelas = 1; $kelas <= 6; $kelas++) {

                    $queryTotal = "SELECT
                    SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'SPP' THEN tms.bulan_ini ELSE 0 END) AS bulan_ini_spp_kelas,
                    SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'SPP' THEN tms.tunggakan ELSE 0 END) AS tunggakan_spp_kelas,
                    SUM(CASE WHEN s.id_kelas = $kelas AND subkat.nama_sub_kategori = 'SPP' THEN tms.jumlah ELSE 0 END) AS total_spp_kelas

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
                    
                    $bulanIniSppKelas =  $rowKelas['bulan_ini_spp_kelas'];
                    $tunggakanSppKelas =  $rowKelas['tunggakan_spp_kelas'];
                    $totalSppKelas = $rowKelas['total_spp_kelas'];

                    $queryPenetapanKelas = "SELECT SUM(p.nominal) AS total_nominal
                    FROM penetapan p
                    JOIN siswa s ON p.id_siswa = s.id_siswa
                    WHERE
                    p.id_sub_kategori = 5 AND
                    s.id_kelas = $kelas;";

                    $resultPenetapanSppKelas = mysqli_query($conn, $queryPenetapanKelas);                    
                    $rowPenetapanSppKelas = mysqli_fetch_array($resultPenetapanSppKelas);
                    $penetapanSppKelas = $rowPenetapanSppKelas['total_nominal'];

                    echo '<tr>';
                    echo '<td>Kelas ' . $kelas . '</td>';
                    echo '<td>Rp. ' . number_format($penetapanSppKelas, 0, ',', '.') . '</td>';
                    echo '<td>Rp. ' . number_format($bulanIniSppKelas, 0, ',', '.') . '</td>';
                    echo '<td>Rp. ' . number_format($tunggakanSppKelas, 0, ',', '.') . '</td>';
                    echo '<td>Rp. ' . number_format($totalSppKelas, 0, ',', '.') . '</td>';
                    echo '</tr>';
                    
                    // Tambahkan nilai ke total
                    $finalPenetapanSpp += $penetapanSppKelas;
                    $finalBulanIniSpp += $bulanIniSppKelas;
                    $finalTunggakanSpp += $tunggakanSppKelas;
                    $finalJumlahSpp += $totalSppKelas;
                    $selisihPenetapanJumlah = $finalJumlahSpp - $finalPenetapanSpp;
                    
                    }

                    echo '<tr>';
                    echo '<td><strong>Total</strong></td>';
                    echo '<td><strong>Rp. ' . number_format($finalPenetapanSpp, 0, ',', '.') . '</strong></td>';
                    echo '<td><strong>Rp. ' . number_format($finalBulanIniSpp, 0, ',', '.') . '</strong></strong></td>';
                    echo '<td><strong>Rp. ' . number_format($finalTunggakanSpp, 0, ',', '.') . '</strong></strong></td>';
                    echo '<td><strong>Rp. ' . number_format($finalJumlahSpp, 0, ',', '.') . '</strong></strong></td>';
                    echo '</tr>';

                    echo '</tbody>';
                    echo '</table>'; 
                    echo '</div>';
                    echo '</div>';

                    $queryTahunAjar = mysqli_query($conn, "SELECT tahun_ajar FROM tahun_ajar WHERE id_tahun_ajar='$idTahunAjar'");
                    $rowTahunAjar = mysqli_fetch_assoc($queryTahunAjar);
                    $tahunAjarLap = $rowTahunAjar['tahun_ajar'];

                    echo 'Selisih Penerimaan SPP bulan ini Terhadap Penetapan <br><br>';
                    echo '<table class="table table-bordered"><thead>
                        <tr>
                            <th style="width: 10%">Penetapan SPP' . $tahunAjarLap . '</th>
                            <th style="width: 20%">SPP Masuk </th>
                            <th style="width: 20%">Selisih Penerimaan</th>
                        </tr></thead><tbody>
                        <tr>
                            <td style="width: 10%"> Rp. ' . number_format($finalPenetapanSpp, 0, ',', '.') . '</td>
                            <td style="width: 20%"> Rp. ' . number_format($finalJumlahSpp, 0, ',', '.') . '</td>
                            <td style="width: 20%"> Rp. ' . number_format($selisihPenetapanJumlah, 0, ',', '.') . '</td>
                        </tr></tbody></table><br><br>';

                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<form method="post" action="pdf_spp.php" target="_blank">';
                    echo '<input type="hidden" name="idTahunAjar" value="' . $idTahunAjar . '">';
                    echo '<input type="hidden" name="bulan" value="' . $bulanNum . '">';
                    echo '<input type="hidden" name="idKategori" value="' . $idKategoriLap . '">';
                    echo '<button type="submit" class="btn btn-primary" name="btnCetakLapSpp" id="btnCetakLapSpp">Cetak</button>';  
                    echo '</form>';                      
                    echo '</div><br>';
                    
                    
                }

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
