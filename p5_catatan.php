<?php
require 'function.php';
require 'cek.php';
require 'config.php';
$conn = mysqli_connect("localhost:3306","root","","sdk");
$semester = 'Ganjil';

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Project</title>
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
                        <h3 class="mt-4">Halaman Project</h3>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">PROSES / Project / Penilaian Project</li>    
                        </ol>
                        <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link" href="p5_penilaian.php">Capaian</a>                          
                        </li>
                        </li>
                        <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Catatan</a>
                        </li>
                        </ul>
                        <br>  
                        <div class="container-fluid px-1">
                            <form method="post" class="form">  
                                <div class="row row-cols-auto">                                    
                                    <div class="col">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <label class="input-group-text" for="project">Project</label>
                                            </div>
                                            <select class="form-select" name="project" id="project" aria-label="project" required>
                                                <option value="">Pilih Project</option>
                                                <?php
                                                $queryProject = mysqli_query($conn, "SELECT id_project, nama_project FROM p5_project");
                                                while ($rowProject = mysqli_fetch_assoc($queryProject)) {
                                                    echo '<option value="' . $rowProject['id_project'] . '">' . $rowProject['nama_project'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary" name="btnTampilPenilaianCatatan" id="btnTampilPenilaianCatatan">
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
                        if (isset($_POST['btnTampilPenilaianCatatan'])) {                        
                            $idProject = $_POST['project'];
                    
                            $queryNamaProject = mysqli_query($conn, "SELECT nama_project FROM p5_project WHERE id_project='$idProject'");
                            $rowProject = mysqli_fetch_assoc($queryNamaProject);
                            $namaProject = $rowProject['nama_project'];
                            
                            echo '<br>';
                            echo '<div class="row">';
                            echo '<h5>Penilaian siswa terhadap project yang telah dikerjakan</h5>';
                            echo '<h6>Catatan hasil project setiap siswa :</h6>';
                            
                            penilaianProject($kelas, $idProject);
                        }  
                        ?>
                    </div>
                </div>
                <?php
                function penilaianProject($kelas, $idProject) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");
                
                    echo '<form method="post">';
                    echo '<div class="modal-body">';     
                    echo '<div class="mb-3">';
                        $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas");
                        
                        if (mysqli_num_rows($querySiswa) > 0) {
                            $counter = 1; // Initialize a counter variable

                            while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                                // Display each student's name and sequential number
                                echo '<div class="row mb-3">';
                                echo '<div class="col-md-6">' . $counter . '.  ' . $rowSiswa['nama'] . ' :</div><br>';
                                
                                echo '<div class="col-md-12">';
                                echo '<textarea name="nilai_' . $rowSiswa['id_siswa'] . '" rows="4" class="form-control" required></textarea>';
                                echo '</div>';
                                
                                echo '</div>';

                                $counter++; 
                            }
                        } else {
                            echo '<p>No students found.</p>';
                        }

                    echo '</div>';
                    echo '<div class="text-center">';
                    echo '<input type="hidden" name="idProject" value="' . $idProject . '">';
                    echo '<button type="submit" class="btn btn-primary" name="tambahCatatanProject">Simpan</button>';
                    echo '</div>';
                    echo '</div>';
                    echo '</form>';
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
