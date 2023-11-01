<?php
include 'function.php';
require 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login - SDK Bhakti</title>
        <style>
            .with-background {
                background-image: url('assets/img/login.jpg'); /* Ganti 'url-gambar-anda.jpg' dengan URL gambar yang ingin Anda gunakan */
                background-size: cover; /* Untuk mengatur gambar agar menutupi seluruh div */
                background-repeat: no-repeat; /* Agar gambar tidak diulang */
                background-position: center center; /* Agar gambar terpusat dalam div */
                /* opacity: 0.6; */
            }
        </style>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication" class="with-background">
            <div id="layoutAuthentication_content">
                <main>
                    <br>
                    <div class="row" style="text-align: center;" >
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            <figure class="bg-light p-4" style="opacity: 0.55;">
                                <blockquote class="blockquote pb-2">
                                    <h2>
                                        SISTEM PENGELOLAAN NILAI
                                    </h2>
                                    <h2>
                                        SD KATOLIK BHAKTI ROGOJAMPI
                                    </h2>
                                </blockquote>
                            </figure>
                        </div>
                        <div class="col-md-3">
                        </div>
                    </div>

                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="username" id="inputUsername" type="text" placeholder="Username" />
                                                <label for="inputUsername">Username</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="password" id="inputPassword" type="password" placeholder="Password" />
                                                <label for="inputPassword">Password</label>
                                            </div>
                                            <div class="form-floating mb-3">
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
                                            </div>
                                            <div class="form-floating mb-3">
                                                <select class="form-select" name="kelas" id="kelas" aria-label="Kelas">
                                                    <option selected>Pilih Kelas</option>
                                                    <?php
                                                    // Ambil data kelas dari tabel kelas
                                                    $queryKelas = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas");
                                                    while ($kelas = mysqli_fetch_assoc($queryKelas)) {
                                                        echo '<option value="' . $kelas['id_kelas'] . '">' . $kelas['nama_kelas'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            </div>     
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                                                <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                                            </div>
                                            <div class="d-flex justify-content-center mt-4 mb-0">
                                                <a class="small" href="#.html"></a>
                                                <button class="btn btn-primary mx-auto" name="login">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="#"></a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
            <div id="layoutAuthentication_footer">
                
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>
