<?php

require 'cek.php';

$queryUser = mysqli_query($conn, "SELECT `role` FROM users WHERE username = '$username'");
while ($dataRole = mysqli_fetch_assoc($queryUser)) {
    $role = $dataRole['role'];
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
        <title></title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js" integrity="sha384-..." crossorigin="anonymous"></script>
    </head>
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-4" href="index.php">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SIRKUM</a>            
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php   
                    if ($role == 'admin') {
                        // Tampilkan semua menu untuk 'admin'
                        echo '<li><a class="dropdown-item" href="register.php">Register</a></li>';
                        echo '<li><a class="dropdown-item" href="ganti_password.php">Ganti password</a></li>';
                        echo '<li><a class="dropdown-item" href="logout.php">Logout</a></li>';
                    } else {
                        // Tampilkan menu untuk 'guru'
                        echo '<li><a class="dropdown-item" href="ganti_password.php">Ganti password</a></li>';
                        echo '<li><a class="dropdown-item" href="logout.php">Logout</a></li>';
                    } 
                    ?>
                    </ul>
                </li>
            </ul>
    </nav>

   


    