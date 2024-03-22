<?php
require 'function.php';
require 'cek.php';
require 'config.php';
date_default_timezone_set('Asia/Jakarta');

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
        <title>Halaman Project</title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
        <div id="layoutSidenav">
            <?php include 'sidebar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h2 class="mt-4">Halaman Project</h2>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">PROSES / Project / Komponen Project</li>                            
                        </ol>
                        <ul class="nav nav-tabs">
                        <li class="nav-item">                            
                            <a class="nav-link active" aria-current="page" href="#">Dimensi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="p5_elemen.php">Elemen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="p5_sub_elemen.php">Sub elemen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="p5_capaian.php">Capaian</a>
                        </li>                        
                        </ul>
                        <br><br>
                        <div class="container-fluid px-4">
                            <div class="row">                                
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-8">
                                    <?php
                                    if (isset($_SESSION['flash_message'])) {
                                        $message_class = isset($_SESSION['flash_message_class']) ? $_SESSION['flash_message_class'] : 'alert-success';
                                        echo '<div class="alert ' . $message_class . ' text-center">' . $_SESSION['flash_message'] . '</div>';
                                        unset($_SESSION['flash_message']); // Hapus pesan flash setelah ditampilkan
                                    }
                                    
                                    ?>
                                </div>
                                <div class="col-md-2">
                                <?php   
                                    // if ($role == 'admin') {
                                    //     // Tampilkan semua menu untuk 'admin'
                                    //     echo '<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalTambahDimensi">+ Dimensi Baru</button>';
                                    // } 
                                ?>
                                </div>
                            </div>
                        </div>                    
                        <br>
                        <div class="card mb-4">
                            <div class="card-header" style="font-weight: bold; ">
                                <i class="fas fa-table me-1"></i>
                                Dimensi Project
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple1" class="table table-bordered" style="width: 75%">
                                        <tr>
                                            <th style="text-align: center; width: 10%;">No.</th>
                                            <th style="text-align: center; width: 65%">Dimensi</th>
                                        </tr>
                                    <?php 
                                    $queryDimensi = mysqli_query($conn, "SELECT `id_dimensi`, `dimensi` FROM `p5_dimensi`;");

                                    $i = 1;
                                    
                                    while($data=mysqli_fetch_array($queryDimensi)){
                                        $idDimensi = $data['id_dimensi'];                                       
                                        $dimensi = $data['dimensi'];                                        
                                        ?>
                                        <tr>
                                            <td style="text-align: center; width: 10%;"><?=$i++;?>.</td>
                                            <td style="width: 65%"><?=$dimensi;?></td>  
                                        </tr>
                                        <!-- Modal edit Dimensi -->
                                        <div class="modal fade" id="modalUbahDimensi<?=$idDimensi;?>">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Ubah Dimensi</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <!-- Modal Body -->
                                                    <form method="post">
                                                        <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="dimensi">Nama Dimensi :</label>
                                                            <input type="text" name="dimensi" value="<?=$dimensi;?>" class="form-control" required>                        
                                                        </div>                                                            
                                                        <div class="text-center">
                                                            <input type="hidden" name="idDimensi" value="<?=$idDimensi;?>">
                                                            <button type="submit" class="btn btn-primary" name="ubahDimensi">Simpan</button>
                                                        </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    };
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
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
    </body>   

    <!-- Modal Tambah Dimensi -->
    <div class="modal fade" id="modalTambahDimensi" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Dimensi Proyek</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal Body -->
                <form method="post">
                    <div class="modal-body">                                
                        <div class="mb-3">
                            <label for="dimensi">Nama Dimensi :</label>
                            <input type="text" name="dimensi" class="form-control" required>                        
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="tambahDimensi">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</html>
