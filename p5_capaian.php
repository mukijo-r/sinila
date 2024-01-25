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
                            <li class="breadcrumb-item active">PROSES / Project / Elemen Project</li>                            
                        </ol>
                        <ul class="nav nav-tabs">
                        <li class="nav-item">                            
                            <a class="nav-link" href="p5_dimensi.php">Dimensi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="p5_elemen.php">Elemen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="p5_sub_elemen.php">Sub elemen</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Capaian</a>
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
                                    //     echo '<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalTambahElemen">+ Elemen Baru</button>';
                                    // } 
                                ?>
                                </div>
                            </div>
                        </div>                    
                        <br>
                        <div class="card mb-4">
                            <div class="card-header" style="font-weight: bold;">
                                <i class="fas fa-table me-1"></i>
                                Elemen, Sub Elemen, dan Capaian untuk Fase 
                                <?php 
                                if ($kelas == 1 | $kelas == 2) { 
                                    $fase = 'A';
                                } elseif ($kelas == 3 | $kelas == 4) {
                                    $fase = 'B';
                                } elseif ($kelas == 5 | $kelas == 6) {
                                    $fase = 'C';
                                }
                                echo $fase;
                                ?>
                            </div>
                            <div class="card-body">                            
                                <table class="table table-bordered" style="width: 100%">
                                    <?php 
                                    $queryCapaian = mysqli_query($conn, "SELECT 
                                    pe.elemen, pse.sub_elemen, `capaian`
                                    FROM `p5_capaian` pca
                                    LEFT JOIN p5_sub_elemen pse ON pca.id_sub_elemen = pse.id_sub_elemen
                                    LEFT JOIN p5_elemen pe ON pse.id_elemen = pe.id_elemen
                                    WHERE
                                    pca.fase = '$fase'
                                    ORDER BY `pe`.`elemen` ASC;");
                                
                                $groupedCapaian = [];
                                
                                while ($row = mysqli_fetch_assoc($queryCapaian)) {
                                    $elemen = $row['elemen'];
                                    $subElemen = $row['sub_elemen'];
                                    $capaian = $row['capaian'];
                                
                                    // Group capaian by element and sub-element
                                    if (!isset($groupedCapaian[$elemen])) {
                                        $groupedCapaian[$elemen] = ['subElements' => []];
                                    }
                                
                                    if (!isset($groupedCapaian[$elemen]['subElements'][$subElemen])) {
                                        $groupedCapaian[$elemen]['subElements'][$subElemen] = [];
                                    }
                                
                                    // Add capaian to the group
                                    $groupedCapaian[$elemen]['subElements'][$subElemen][] = $capaian;
                                }
                                
                                // Display the grouped capaian in a table
                                $h = 1;
                                foreach ($groupedCapaian as $elemen => $elementData) {
                                    echo '<h5>' . $h . '. Elemen ' . ucwords($elemen) . ' :</h5>';
                                    echo '<table class="table table-bordered" style="width: 100%">';
                                    echo '<tr>
                                        <th style="width: 5%">No.</th>
                                        <th style="width: 40%">Sub Elemen</th>
                                        <th style="width: 55%">Capaian</th>
                                    </tr>';
                                    $i = 1;
                                    foreach ($elementData['subElements'] as $subElemen => $capaians) {
                                        echo '<tr>';
                                        echo '<td style="width: 5%">' . $i . '.</td>';
                                        echo '<td style="width: 40%">' . ucfirst($subElemen) . '.</td>';
                                        echo '<td style="width: 55%">' . implode(', ', $capaians) . '.</td>';
                                        echo '</tr>';
                                        $i++;
                                    }
                                
                                    echo '</table>';
                                    echo '<br>';
                                    echo '<hr>';
                                    echo '<br>';
                                    $h++;
                                    }
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
    <div class="modal fade bd-example-modal-lg" id="modalTambahElemen" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                            <label for="dimensi1">Dimensi :</label>
                            <select class="form-select" name="dimensi1" id="dimensi1" aria-label="dimensi1" required>
                                <option value="">Pilih Dimensi</option>
                                <?php
                                $queryDimensi = mysqli_query($conn, "SELECT id_dimensi, dimensi FROM p5_dimensi");
                                while ($rowDimensi = mysqli_fetch_assoc($queryDimensi)) {
                                    echo '<option value="' . $rowDimensi['id_dimensi'] . '">' . $rowDimensi['dimensi'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="elemen">Nama Elemen :</label>
                            <input type="text" name="elemen" class="form-control" required>                        
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary" name="tambahElemen">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</html>
