<?php
require 'function.php';
require 'cek.php';
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
        <title>Halaman Register</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav">
            <?php include 'sidebar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                <div class="container mt-5">
                        <div class="row justify-content-center">
                            <div class="col-lg-7">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Buat Akun Baru</h3></div>
                                    <div class="card-body">
                                        <form method="post" action="register.php">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="nama">Nama Lengkap :</label>   
                                                <input type="text" name="nama" placeholder="Nama Lengkap" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="username">Username :</label>   
                                                <input type="text" name="username" placeholder="username" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="password">Password :</label>
                                                <input type="password" name="password" id="password" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="confirmPassword">Konfirmasi Password :</label>
                                                <input type="password" name="confirmPassword" id="confirmPassword" class="form-control" required>
                                                <div id="passwordError" class="text-danger"></div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="role">Role :</label>
                                                <select class="form-select" name="role" id="role" aria-label="Role">
                                                    <option value="admin">Admin</option>
                                                    <option value="guru">Guru</option>
                                                    <option value="kepsek">Kepala Sekolah</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="btn btn-success" name="tambahUser">Tambah</button> 
                                        </div>
                                        <br> 
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script src="js/scripts.js"></script>

        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        

    </body> 
</html>

<script>
    document.getElementById("confirmPassword").addEventListener("input", function() {
        const password = document.getElementById("password").value;
        const confirmPassword = this.value;
        const passwordError = document.getElementById("passwordError");

        if (password !== confirmPassword) {
            passwordError.textContent = "Password harus sama";
        } else {
            passwordError.textContent = ""; // Kosongkan pesan kesalahan jika password cocok
        }
    });   
</script>

<script>
    <?= $sweetAlert ?>
</script>

    
