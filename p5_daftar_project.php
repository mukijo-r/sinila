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
                                    <a href="p5_new_project.php"><button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalTambahProject">+ Project Baru</button></a>
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

                            echo '<h4>Daftar Project Fase ' . $fase . ' :</h4><br>';
                        ?>                       

                        <div class="mb-3">                           

                                <?php
                                $queryProject = "SELECT `id_project`, 
                                `tanggal`,
                                `pembuat`, 
                                `nama_project`,  
                                `deskripsi_project`, 
                                `id_capaian1`, 
                                `id_capaian2`, 
                                `id_capaian3`, 
                                `id_capaian4` 
                                FROM `p5_project` prj
                                WHERE prj.fase = '$fase'
                                ORDER BY prj.id_project DESC;";

                                $resultProject = mysqli_query($conn, $queryProject);
                                $totalEntries = mysqli_num_rows($resultProject);                    
                                $p = $totalEntries;

                                while ($row = mysqli_fetch_assoc($resultProject)) {
                                    $tanggal = $row['tanggal'];
                                    $tanggalDMY = date('d F Y', strtotime($tanggal));

                                    // Array untuk mengganti nama bulan
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

                                    // Ganti nama bulan dalam bahasa Indonesia
                                    $tanggalIndonesia = str_replace(array_keys($bulan), array_values($bulan), $tanggalDMY);

                                    $pembuat = $row['pembuat'];
                                    $nama_project = $row['nama_project'];
                                    $deskripsi_project = $row['deskripsi_project'];
                                    //Capaian 1
                                    $id_capaian1 = $row['id_capaian1'];
                                    $queryCapaian1 = mysqli_query($conn, "SELECT
                                    pca.capaian,
                                    pse.sub_elemen,
                                    pe.elemen,
                                    pd.dimensi
                                    FROM
                                    p5_capaian pca
                                    LEFT JOIN p5_sub_elemen pse ON pca.id_sub_elemen = pse.id_sub_elemen
                                    LEFT JOIN p5_elemen pe ON pse.id_elemen = pe.id_elemen
                                    LEFT JOIN p5_dimensi pd ON pe.id_dimensi = pd.id_dimensi
                                    WHERE
                                    pca.id_capaian = '$id_capaian1';");

                                    while ($rowCap1 = mysqli_fetch_assoc($queryCapaian1)) {                                    
                                    $capaian1 = $rowCap1['capaian'];
                                    $subelemen1 = $rowCap1['sub_elemen'];
                                    $elemen1 = $rowCap1['elemen'];
                                    $dimensi1 = $rowCap1['dimensi'];
                                    }

                                    //Capaian 2
                                    $id_capaian2 = $row['id_capaian2'];

                                    $queryCapaian2 = mysqli_query($conn, "SELECT
                                    pca.capaian,
                                    pse.sub_elemen,
                                    pe.elemen,
                                    pd.dimensi
                                    FROM
                                    p5_capaian pca
                                    LEFT JOIN p5_sub_elemen pse ON pca.id_sub_elemen = pse.id_sub_elemen
                                    LEFT JOIN p5_elemen pe ON pse.id_elemen = pe.id_elemen
                                    LEFT JOIN p5_dimensi pd ON pe.id_dimensi = pd.id_dimensi
                                    WHERE
                                    pca.id_capaian = '$id_capaian2';");

                                    while ($rowCap2 = mysqli_fetch_assoc($queryCapaian2)) {                                    
                                    $capaian2 = $rowCap2['capaian'];
                                    $subelemen2 = $rowCap2['sub_elemen'];
                                    $elemen2 = $rowCap2['elemen'];
                                    $dimensi2 = $rowCap2['dimensi'];
                                    }

                                    //Capaian 3
                                    $id_capaian3 = $row['id_capaian3'];
                                    $queryCapaian3 = mysqli_query($conn, "SELECT
                                    pca.capaian,
                                    pse.sub_elemen,
                                    pe.elemen,
                                    pd.dimensi
                                    FROM
                                    p5_capaian pca
                                    LEFT JOIN p5_sub_elemen pse ON pca.id_sub_elemen = pse.id_sub_elemen
                                    LEFT JOIN p5_elemen pe ON pse.id_elemen = pe.id_elemen
                                    LEFT JOIN p5_dimensi pd ON pe.id_dimensi = pd.id_dimensi
                                    WHERE
                                    pca.id_capaian = '$id_capaian3';");

                                    while ($rowCap3 = mysqli_fetch_assoc($queryCapaian3)) {                                    
                                    $capaian3 = $rowCap3['capaian'];
                                    $subelemen3 = $rowCap3['sub_elemen'];
                                    $elemen3 = $rowCap3['elemen'];
                                    $dimensi3 = $rowCap3['dimensi'];
                                    }

                                    //Capaian 4
                                    $id_capaian4 = $row['id_capaian4'];
                                    $queryCapaian4 = mysqli_query($conn, "SELECT
                                    pca.capaian,
                                    pse.sub_elemen,
                                    pe.elemen,
                                    pd.dimensi
                                    FROM
                                    p5_capaian pca
                                    LEFT JOIN p5_sub_elemen pse ON pca.id_sub_elemen = pse.id_sub_elemen
                                    LEFT JOIN p5_elemen pe ON pse.id_elemen = pe.id_elemen
                                    LEFT JOIN p5_dimensi pd ON pe.id_dimensi = pd.id_dimensi
                                    WHERE
                                    pca.id_capaian = '$id_capaian4';");

                                    while ($rowCap4 = mysqli_fetch_assoc($queryCapaian4)) {                                    
                                    $capaian4 = $rowCap4['capaian'];
                                    $subelemen4 = $rowCap4['sub_elemen'];
                                    $elemen4 = $rowCap4['elemen'];
                                    $dimensi4 = $rowCap4['dimensi'];
                                    }

                                    echo '<h5>' . $p . '. ' . ucfirst($nama_project) . '</h5>';

                                    echo '<table class="table table-bordered" style="width: 85%">';
                                    echo '<tr>
                                            <td style="font-weight: bold; width: 20%;">Nama Project</td>
                                            <td style ="width: 80%; font-weight: bold;text-decoration: underline;">' . $nama_project . '</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">Pembuat</td>
                                            <td style ="width: 80%;">' . $pembuat . '</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">Tanggal dibuat</td>
                                            <td style ="width: 80%;">' . $tanggalIndonesia . '</td>
                                          </tr>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">Dimensi 1</td>
                                            <td style ="width: 80%; text-decoration: underline; font-weight: bold;">' . $dimensi1 . '</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Elemen</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . ucfirst($elemen1) . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Sub Elemen</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . ucfirst($subelemen1) . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Capaian</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . $capaian1 . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">Dimensi 2</td>
                                            <td style ="width: 80%; text-decoration: underline;font-weight: bold;">' . $dimensi2 . '</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Elemen</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . ucfirst($elemen2) . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Sub Elemen</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . ucfirst($subelemen2) . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Capaian</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . $capaian2 . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">Dimensi 3</td>
                                            <td style ="width: 80%; text-decoration: underline;font-weight: bold;">' . $dimensi3 . '</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Elemen</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . ucfirst($elemen3) . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Sub Elemen</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . ucfirst($subelemen3) . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Capaian</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . $capaian3 . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">Dimensi 4</td>
                                            <td style ="width: 80%; text-decoration: underline;font-weight: bold;">' . $dimensi4 . '</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Elemen</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . ucfirst($elemen4) . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Sub Elemen</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . ucfirst($subelemen4) . '.</td>
                                          </tr>
                                          <tr>
                                            <td style="font-weight: bold; width: 20%;">
                                                <ul>
                                                    <li>Capaian</li>
                                                </ul>
                                            </td>
                                            <td style ="width: 80%;">' . $capaian4 . '.</td>
                                          </tr>';
                                    echo '</table><hr><br>';

                                    $p--;
                                }

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
</html>
