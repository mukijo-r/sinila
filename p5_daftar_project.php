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
                        <div class="container-fluid px-4">
                            <div class="row">                                
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahProject">+ Project Baru</button>
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
                                </div>
                            </div>
                        </div>                    
                        <hr>
                        <?php 
                            if ($kelas == 1 | $kelas == 2) { 
                                $fase = 'A';
                            } elseif ($kelas == 3 | $kelas == 4) {
                                $fase = 'B';
                            } elseif ($kelas == 5 | $kelas == 6) {
                                $fase = 'C';
                            }

                            echo '<h4>Daftar Project Fase ' . $fase . ' :</h4>';
                        ?>
                        <div class="mb-3">
                        <?php
                        $queryProject = "SELECT 
                                            ppc.id_project,
                                            pp.nama_project,
                                            pp.tanggal,
                                            pp.pembuat,
                                            pp.deskripsi_project,
                                            ppc.pc,
                                            ppc.id_capaian,
                                            capaian,
                                            pc.id_sub_elemen,
                                            psu.sub_elemen,
                                            psu.id_elemen,
                                            pe.elemen,
                                            pe.id_dimensi,
                                            pd.dimensi
                                        FROM 
                                            `p5_project_capaian` ppc
                                        LEFT JOIN p5_project pp ON ppc.id_project = pp.id_project
                                        LEFT JOIN p5_capaian pc ON ppc.id_capaian = pc.id_capaian
                                        LEFT JOIN p5_sub_elemen psu ON pc.id_sub_elemen = psu.id_sub_elemen
                                        LEFT JOIN p5_elemen pe ON psu.id_elemen = pe.id_elemen
                                        LEFT JOIN p5_dimensi pd ON pe.id_dimensi = pd.id_dimensi
                                        WHERE pp.fase = '$fase'
                                        ORDER BY ppc.id_project DESC;";

                        $resultProject = mysqli_query($conn, $queryProject);

                        $prevProjectID = null;
                        
                        while ($row = mysqli_fetch_assoc($resultProject)) {
                            $projectID = $row['id_project'];
                            $tanggal = date('d F Y', strtotime($row['tanggal']));
                            $bulan = [
                                'January' => 'Januari',
                                'February' => 'Februari',
                                'March' => 'Maret',
                                'April' => 'April',
                                'May' => 'Mei',
                                'June' => 'Juni',
                                'July' => 'Juli',
                                'August' => 'Agustus',
                                'September' => 'September',
                                'October' => 'Oktober',
                                'November' => 'November',
                                'December' => 'Desember'
                            ];

                            foreach ($bulan as $english => $indonesian) {
                                $tanggal = str_replace($english, $indonesian, $tanggal);
                            }
                            $namaProject = $row['nama_project'];
                            $pembuat = $row['pembuat'];
                            $deskripsi = $row['deskripsi_project'];
                            $dimensi = $row['dimensi'];
                            $elemen = $row['elemen'];
                            $subElemen = $row['sub_elemen'];
                            $capaian = $row['capaian'];
                            
                            // Print project information only once for each unique project
                            if ($prevProjectID !== $projectID) {
                                $noDimensi = 1;
                                echo '<table class="table table-bordered" style="width: 85%">';
                                echo '<tr>
                                        <td style="font-weight: bold; width: 20%; background-color: #D3D3D3;"><h5>Nama Project</h5></td>
                                        <td style ="width: 80%; font-weight: bold;text-decoration: underline; background-color: #D3D3D3;"><h5>' . ucfirst($namaProject) . '<h5></td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; width: 20%;">Pembuat</td>
                                        <td style ="width: 80%;">' . $pembuat . '</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; width: 20%;">Tanggal dibuat</td>
                                        <td style ="width: 80%;">' . $tanggal . '</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; width: 20%;">Deskripsi project</td>
                                        <td style ="width: 80%;">' . $deskripsi . '</td>
                                    </tr>';

                                $prevProjectID = $projectID;
                                                                
                                 
                            }
                            
                            // Print dimension, element, sub-element, and achievement information
                            echo '<tr>
                                    <td style="font-weight: bold; width: 20%;">Dimensi ' . $noDimensi . '</td>
                                    <td style ="width: 80%; text-decoration: underline; font-weight: bold;">' . $dimensi . '</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: normal; width: 20%;">Elemen</td>
                                    <td style ="width: 80%;">' .$elemen . '.</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: normal; width: 20%;">Sub Elemen</td>
                                    <td style ="width: 80%;">' . $subElemen . '.</td>
                                </tr>
                                <tr>
                                    <td style="font-weight: normal; width: 20%;">Capaian</td>
                                    <td style ="width: 80%;">' . $capaian . '.</td>
                                </tr><br>';                                  
                                
                                
                                $noDimensi++;   
                                     
                        }
                        
                        echo '</table><hr><br>';  
                        
                        ?>

                            
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
    
    <!-- Modal Tambah Project -->
    <div class="modal fade" id="modalTambahProject">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Project Baru</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal Body -->
                <form method="post" action="p5_new_project.php">
                    <div class="modal-body">                    
                        <div class="mb-3">
                            <label for="dimensi">Jumlah Dimensi :</label>                        
                            <select class="form-select" name="dimensi" id="dimensi" aria-label="dimensi">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>                  
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" name="tambahProject">Buat</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</html>
