<?php
include 'config.php';
require 'sidebar_function.php';
//session_start();
if (isset($_SESSION['tahunAjar'])) {
    $tahunAjar = $_SESSION['tahunAjar'];
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
        <title>Dashboard - SIM Keuangan</title>
        <style>
            @media print {
                .sb-sidenav, .accordion, .sb-sidenav-dark, .sb-sidenav-menu, .nav, .nav-link, .sb-nav-link-icon, .fas, .fa-tachometer-alt, .sb-sidenav-menu-heading, .nav-link, .collapsed, .sb-nav-link-icon, .sb-sidenav-collapse-arrow, .collapse, .sb-sidenav-menu-nested, .nav, .nav-link, .sb-sidenav-footer, .small, .modal {
                    display: none !important;
                }
            }

            /* Style untuk kontainer gambar dengan border melingkar */
            .logo {
                border-radius: 100%; /* Membuat border melingkar dengan radius 50% dari lebar atau tinggi kontainer */
                overflow: hidden; /* Menghilangkan bagian gambar yang mungkin melampaui border */
            }

            /* Style untuk gambar di dalam kontainer */
            .logo img {
                width: 40%;
                height: 40%;
                object-fit: cover; /* Mengatur bagaimana gambar mengisi kontainer */
            }

        </style>

        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
    </head>
    <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="logo" style="text-align: center; margin-right: 10px;">
                                <img src="assets/img/karmel-logo.png" width="50px" height="50px">
                                <br><br>
                                <div style="text-align: center;">
                                    <h6>Tahun Ajar</h6>
                                    <a href=# data-bs-toggle="modal" data-bs-target="#modalTahunAjar">
                                    <h5><?=$tahunAjar;?><h5>
                                    </a>
                                </div>
                            </div>
                            <div class="sb-sidenav-menu-heading">Proses</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                                Input Data
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="penetapan.php">Mata Pelajaran</a>
                                    <a class="nav-link" href="transaksi_masuk_siswa.php">Nilai</a>
                                    <a class="nav-link" href="transaksi_keluar_siswa.php">Absensi</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts1" aria-expanded="false" aria-controls="collapseLayouts1">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-piggy-bank"></i></div>
                                Laporan
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts1" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="tabung.php">Daftar Nilai</a>
                                    <a class="nav-link" href="tabung_ambil.php">Rapot Sisipan</a>
                                    <a class="nav-link" href="tabung_list.php">Rapot Akhir Semester</a>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Master</div>
                            <a class="nav-link" href="guru.php" >
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                                Guru
                            </a>
                            <a class="nav-link" href="siswa.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-people-group"></i></div>
                                Siswa
                            </a>
                            <a class="nav-link" href="kelas.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-people-group"></i></div>
                                Kelas
                            </a>

                            <div class="sb-sidenav-menu-heading">Info</div>
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalAbout">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-circle-info"></i></div>
                                About
                            </a>
                        </div>                            
                    </div>
                    <div style="text-align: center;" class="sb-sidenav-footer">
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalTahunAjar">Ganti Tahun Ajar</button>
                        
                    </div>
                </nav>
            </div>

            <!-- Modal Ganti Tahun ajar-->
    <div class="modal fade" id="modalTahunAjar">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Ganti Tahun Ajar</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->      
                <form method="post">
                <div class="modal-body">
                    <br>
                    <select class="form-select" name="tahunAjar" aria-label="Pilih TA">
                        <option selected>Pilih Tahun Ajar</option>
                        <?php
                            // Ambil data kelas dari tabel kelas
                            $queryTA = mysqli_query($conn, "SELECT id_tahun_ajar, tahun_ajar FROM tahun_ajar");
                            while ($ta = mysqli_fetch_assoc($queryTA)) {
                                echo '<option value="' . $ta['tahun_ajar'] . '">' . $ta['tahun_ajar'] . '</option>';
                            }
                            ?>
                    </select>
                    <br>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" name="ubahTahunAjar">Ubah</button> 
                </div>
                <br> 
            </form>   
            </div>
        </div>
    </div>



    <!-- Modal About-->
    <div class="modal fade" id="modalAbout">
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
                    <h5>Tentang Aplikasi</h5>
                    <p>Sistem Pengelolaan Nilai SDK Bhakti Rogojampi.</p>
                    <h5>Fitur</h5>
                    <ul>
                        <li>Mengelola data nilai</li>
                        <li>Mencetak rapor.</li>
                    </ul>
                    <h5>Versi</h5>
                    <p>1.0.0 </p>
                    <h5>Tanggal Rilis</h5>
                    <p>15 November 2023 </p>
                    <h5>Pengembangan</h5>
                    <ul>
                        <li>Pengembang : Mukijo</li>
                        <li>Email : mkjjaya@gmail.com</li>
                        <li>No.tlp : 0856-4334-6785</li>
                        <li>Afiliasi : Universitas Siber Asia</li>
                    </ul>
                    <h5>Dukungan</h5>
                    <p>Aplikasi didukung oleh XAMPP versi 8.0.19 ke atas.</p>
                    <h5>Bantuan</h5>
                    <p>Jika Anda memiliki pertanyaan atau masalah, silakan hubungi melalui email atau WA.</p>
                    <p>Terima kasih telah menggunakan aplikasi ini!</p>                    
                </div>
                <br> 
            </form>   
            </div>
        </div>
    </div> 


