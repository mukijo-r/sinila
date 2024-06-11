<?php
require 'function.php';
require 'cek.php';
require 'config.php';
$conn = mysqli_connect("localhost:3306","root","","sdk");
$semester = 'Genap';

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Capaian Kompetensi</title>
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
                        <h3 class="mt-4">Capaian Pembelajaran Siswa Semester Genap</h3>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Capaian Pembelajaran / Asesmen Genap</li>    
                        </ol>  
                        <div class="container-fluid px-1">
                            <form method="post" class="form">  
                                <div class="row row-cols-auto">                                    
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="kategori">Mata Pelajaran</label>
                                            </div>
                                            <select class="form-select" name="mapel" id="mapel" aria-label="mapel" required>
                                                <option selected disabled>Pilih Mapel</option>
                                                <?php
                                                if ($kelas == 1 | $kelas == 2 | $kelas == 3) {
                                                    $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel WHERE mapel <> 'Bahasa Using';");
                                                } else {
                                                    $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel");
                                                }
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
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                    <?php
                                    if (isset($_SESSION['flash_message'])) {
                                        $message_class = isset($_SESSION['flash_message_class']) ? $_SESSION['flash_message_class'] : 'alert-success';
                                        echo '<div class="alert ' . $message_class . ' text-center">' . $_SESSION['flash_message'] . '</div>';
                                        unset($_SESSION['flash_message']); 
                                    }                                    
                                    ?>                                    
                                    </div>
                                    <div class="col-md-2"></div>  
                                </div>
                            </form> 
                        </div>
                    </div>

                    <div class="container-fluid px-4">
                        <?php
                        if (isset($_POST['btnTampilLapSiswa'])) {                        
                            $idMapel = $_POST['mapel'];
                    
                            $queryMapel = mysqli_query($conn, "SELECT mapel FROM mapel WHERE id_mapel='$idMapel'");
                            $rowMapel = mysqli_fetch_assoc($queryMapel);
                            $namaMapel = $rowMapel['mapel'];
                            
                            echo '<br>';
                            echo '<div class="row">';
                            echo '<h4>Pilih deskripsi capaian kompetensi yang akan ditampilkan ke dalam rapot :</h4>';
                            capaianKompetensi($kelas, $idMapel, $semester);
                            echo '';
                        }  
                        ?>
                    </div>
                </div>
                <?php
                function capaianKompetensi($kelas, $idMapel, $semester) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");
                
                    if ($kelas == 1 || $kelas == 2) {
                        $fase = 'A';
                    } elseif ($kelas == 3 || $kelas == 4) {
                        $fase = 'B';
                    } elseif ($kelas == 5 || $kelas == 6) {
                        $fase = 'C';
                    }
                
                    $queryCk = "SELECT 
                    id_ck, 
                    deskripsi 
                    FROM 
                    `capaian_kompetensi` 
                    WHERE 
                    id_mapel = '$idMapel' AND 
                    fase = '$fase' 
                    ORDER BY id_ck ASC;"; 

                    $capaianKompetensi = mysqli_query($conn, $queryCk);                

                    echo '<form method="post" action="">';
                    while ($rowCapKom = mysqli_fetch_assoc($capaianKompetensi)) {
                        $idCapkom = $rowCapKom['id_ck'];
                        $deskripsi = $rowCapKom['deskripsi'];
                        $deskripsi = ucfirst($deskripsi);

                        echo '<br>';                
                        echo '<div style="font-size: 1.2em;" class="form-check">';
                        echo '<input class="form-check-input" type="checkbox" name="checkbox[' . $idCapkom . '][]" value="1" id="flexCheckDefault">' . $deskripsi . '.';
                        echo '</div>';
                    }
                    echo '<br>';
                    echo '<br>';
                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<input type="hidden" name="kelas" value="' . $kelas . '">';
                    echo '<input type="hidden" name="idMapel" value="' . $idMapel . '">';
                    echo '<input type="hidden" name="semester" value="' . $semester . '">';
                    echo '<button type="submit" class="btn btn-primary" name="btnCapkomLanjut" id="btnCapkomLanjut">Lanjut</button>';
                    echo '</div>';
                    echo '</form>';
                    echo '<br>';
                    echo '<br>';   
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
    </body>
</html>
