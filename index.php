<?php
require 'function.php';
require 'cek.php';
require 'config.php';

//session_start();

// if (isset($_SESSION['user'])) {
//     $username = $_SESSION['user'];
// } else {
//     // Pengguna tidak masuk. Lakukan sesuatu, seperti mengarahkan mereka kembali ke halaman login.
//     header('location: login.php');
// }

// if (isset($_SESSION['tahunAjar'])) {
//     $tahunAjar = $_SESSION['tahunAjar'];
// }

// if (isset($_SESSION['kelas'])) {
//     $kelas = $_SESSION['kelas'];
// }

// $queryUser = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE username = '$username'");
// $rowUser = mysqli_fetch_array($queryUser);
// $namaUser = $rowUser['nama_lengkap'];



?>

<!DOCTYPE html>
<html lang="en">
    <head><?=$kelas;?>
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


        </style>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav">
            <?php include 'sidebar.php'; ?><?=$kelas;?>
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
                            <div class="row">                                
                                <?=$kelas;?>
                            </div>
                        </figure>
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

    </body>
</html>

