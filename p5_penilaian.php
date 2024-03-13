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
                            <a class="nav-link active" aria-current="page" href="#">Capaian</a>                            
                        </li>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="p5_catatan.php">Catatan</a>
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
                                                <option selected disabled>Pilih Project</option>
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
                                        <button type="submit" class="btn btn-primary" name="btnTampilPenilaian" id="btnTampilPenilaian">
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
                        if (isset($_POST['btnTampilPenilaian'])) {                        
                            $idProject = $_POST['project'];
                    
                            $queryNamaProject = mysqli_query($conn, "SELECT nama_project FROM p5_project WHERE id_project='$idProject'");
                            $rowProject = mysqli_fetch_assoc($queryNamaProject);
                            $namaProject = $rowProject['nama_project'];
                            
                            echo '<br>';
                            echo '<div class="row">';
                            echo '<h5>Penilaian siswa terhadap project yang telah dikerjakan</h5>';
                            echo '<h6>Keterangan tingkat pencapaian siswa :</h6>';
                            echo str_repeat('&nbsp;', 4) . '<table class="table-bordered" style="width: 60%; text-align: center;">
                                    <tr>
                                        <th>BB</th>
                                        <th>MB</th>
                                        <th>BSH</th>
                                        <th>SB</th>
                                    </tr>
                                    <tr>
                                        <td>Belum Berkembang</td>
                                        <td>Mulai Berkembang</td>
                                        <td>Berkembang Sesuai Harapan</td>
                                        <td>Sangat Berkembang</td>
                                    </tr>
                                    <tr>
                                        <td>Siswa masih membutuhkan bimbingan dalam mengembangkan kemampuan.</td>
                                        <td>Siswa mulai mengembangkan kemampuan namun masih belum ajek.</td>
                                        <td>Siswa telah mengembangkan kemampuan hingga berada dalam tahap ajek.</td>
                                        <td>Siswa mengembangkan kemampuannya melampaui harapan.</td>
                                    </tr>
                                  </table><br><br>';
                            penilaianProject($kelas, $idProject);
                        }  
                        ?>
                    </div>
                </div>
                <?php
                function penilaianProject($kelas, $idProject) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");
                
                    $queryProject = "SELECT 
                    ppc.id_project,
                    nama_project,
                    deskripsi_project,
                    MAX(CASE WHEN ppc.pc = 'capaian1' THEN ppc.id_capaian END) as id_capaian1,
                    MAX(CASE WHEN ppc.pc = 'capaian2' THEN ppc.id_capaian END) as id_capaian2,
                    MAX(CASE WHEN ppc.pc = 'capaian3' THEN ppc.id_capaian END) as id_capaian3,
                    MAX(CASE WHEN ppc.pc = 'capaian4' THEN ppc.id_capaian END) as id_capaian4,
                    MAX(CASE WHEN ppc.pc = 'capaian5' THEN ppc.id_capaian END) as id_capaian5,
                    MAX(CASE WHEN ppc.pc = 'capaian6' THEN ppc.id_capaian END) as id_capaian6,
                    MAX(CASE WHEN ppc.pc = 'capaian1' THEN pc.capaian END) as capaian1,
                    MAX(CASE WHEN ppc.pc = 'capaian2' THEN pc.capaian END) as capaian2,
                    MAX(CASE WHEN ppc.pc = 'capaian3' THEN pc.capaian END) as capaian3,
                    MAX(CASE WHEN ppc.pc = 'capaian4' THEN pc.capaian END) as capaian4,
                    MAX(CASE WHEN ppc.pc = 'capaian5' THEN pc.capaian END) as capaian5,
                    MAX(CASE WHEN ppc.pc = 'capaian6' THEN pc.capaian END) as capaian6,
                    COUNT(pc.id_capaian) AS jumlah_id_capaian
                    FROM 
                        `p5_project_capaian` ppc
                    LEFT JOIN 
                        p5_project pp ON ppc.id_project = pp.id_project
                    LEFT JOIN 
                        p5_capaian pc ON ppc.id_capaian = pc.id_capaian
                    WHERE
                        ppc.id_project = '$idProject'
                    GROUP BY
                        ppc.id_project, nama_project;";
                
                    $tampilProject = mysqli_query($conn, $queryProject);
                
                    echo '<form method="post" action="">';
                    while ($rowProject = mysqli_fetch_assoc($tampilProject)) {
                        $namaProject = $rowProject['nama_project'];
                        $deskripsi = $rowProject['deskripsi_project'];
                        $jumlahCapaian = $rowProject['jumlah_id_capaian'];
                        $idCapaian1 = $rowProject['id_capaian1'];
                        $idCapaian2 = $rowProject['id_capaian2'];
                        $idCapaian3 = $rowProject['id_capaian3'];
                        $idCapaian4 = $rowProject['id_capaian4'];
                        $idCapaian5 = $rowProject['id_capaian5'];
                        $idCapaian6 = $rowProject['id_capaian6'];
                        $capaian1 = $rowProject['capaian1'];
                        $capaian2 = $rowProject['capaian2'];
                        $capaian3 = $rowProject['capaian3'];
                        $capaian4 = $rowProject['capaian4'];
                        $capaian5 = $rowProject['capaian5'];
                        $capaian6 = $rowProject['capaian6'];


                        echo '<br><hr>';
                        echo '<h5 style="">Nama Project : ' . $namaProject . '</h5>';
                        echo '<table>
                                <tr>
                                    <td><h5 style="text-decoration: underline;">Deskripsi project : </h5>' . $deskripsi . '</td>
                                </tr>
                            </table><br>';                

                        $querySiswa = mysqli_query($conn, "SELECT id_siswa, nama FROM siswa WHERE id_kelas = $kelas");
                        $nomor = 1;
                
                        while ($rowSiswa = mysqli_fetch_assoc($querySiswa)) {
                            $idSiswa = $rowSiswa['id_siswa'];
                            $namaSiswa = $rowSiswa['nama'];
                
                            echo '<h5>' . $nomor . ' - ' . $namaSiswa . '</h5>';

                            for ($c=1; $c<=$jumlahCapaian; $c++) {
                                echo '<label><ul><li>Capaian ' . $c . ' : ' . ${"capaian" . $c} . ' : </li></ul></label><br>
                                    <div class="row">                                
                                        <div class="col-md-1">' . str_repeat('&nbsp;', 7) . '
                                            <input class="form-check-input" type="radio" name="penilaian[' . $idSiswa . '][' . ${"idCapaian" . $c} . ']" value="BB">  BB
                                        </div>
                                        <div class="col-md-1">
                                            <input class="form-check-input" type="radio" name="penilaian[' . $idSiswa . '][' . ${"idCapaian" . $c} . ']" value="MB"> MB
                                        </div>
                                        <div class="col-md-1">
                                            <input class="form-check-input" type="radio" name="penilaian[' . $idSiswa . '][' . ${"idCapaian" . $c} . ']" value="BSH"> BSH
                                        </div>
                                        <div class="col-md-1">
                                            <input class="form-check-input" type="radio" name="penilaian[' . $idSiswa . '][' . ${"idCapaian" . $c} . ']" value="SB"> SB
                                        </div>
                                    </div><br>';
                            }
                            $nomor++;
                            echo '<hr>';
                        }
                    }
                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<input type="hidden" name="jumlahCapaian" value="' . $jumlahCapaian . '">';
                    echo '<input type="hidden" name="idProject" value="' . $idProject . '">';
                    echo '<button type="submit" class="btn btn-primary" name="btnSubmitNilaiProject" id="btnSubmitNilaiProject">Simpan</button>';
                    echo '</div><br>';
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
