<?php
require 'function.php';
require 'cek.php';
require 'config.php';
// require_once('capkom_siswa_ganjil.php');
$conn = mysqli_connect("localhost:3306","root","","sdk");
$semester = 'Ganjil';
$idMapel = isset($_GET['idMapel']) ? $_GET['idMapel'] : '';
$semester = isset($_GET['semester']) ? $_GET['semester'] : '';

$queryMapel = mysqli_query($conn, "SELECT `mapel` FROM `mapel` WHERE id_mapel = '$idMapel'");
$rowMapel = mysqli_fetch_array($queryMapel);
$Mapel = $rowMapel['mapel'];


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
                        <h3 class="mt-4">Capaian Kompetensi Siswa Semester Ganjil</h3>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Capaian Pembelajaran / Asesmen Siswa</li>    
                        </ol>  
                        <div class="container-fluid px-1">
                            <form method="post" class="form">  
                                <div class="row row-cols-auto">                                    
                                    <div class="col">                                        
                                    </div>
                                    <div class="col">
                                    </div>                                              
                                </div>
                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                    <?php
                                    if (isset($_SESSION['flash_message'])) {
                                        $message_class = isset($_SESSION['flash_message_class']) ? $_SESSION['flash_message_class'] : 'alert-success';
                                        echo '<div class="alert ' . $message_class . ' text-center">' . $_SESSION['flash_message'] . '</div>';
                                        unset($_SESSION['flash_message']); // Hapus pesan flash setelah ditampilkan
                                    }                                    
                                    ?>                                    
                                    </div>
                                    <div class="col-md-2"></div>  
                                </div>
                            </form> 
                        </div>
                    </div>
                    <hr>
                    <div class="container-fluid px-4">
                        <h4>Penilaian Capaian Kompetensi Mata Pelajaran <?=$Mapel;?>
                        <h4>Aktifkan deskripsi sesuai capaian kompotensi setiap siswa :</h4>
                        <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                        <label class="form-check-label" for="flexSwitchCheckDefault"> : Membutuhkan bimbingan/bantuan</label>
                        </div>
                        <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
                        <label class="form-check-label" for="flexSwitchCheckChecked"> : Mampu/memiliki pemahaman</label>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-auto"></div>
                        <div class="col-md-11">
                            
                        <?php
                        $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahunAjar'");
                        $rowTahunAjar = mysqli_fetch_array($queryTahunAjar);
                        $idTahunAjar = $rowTahunAjar['id_tahun_ajar'];

                        $queryAsesmenCk = "SELECT 
                        `id_ac`, 
                        ac.id_siswa,
                        s.nama,
                        ck.id_mapel, 
                        ac.id_ck,
                        ck.id_mapel,
                        mapel,
                        ck.deskripsi,
                        ac.capaian 
                        FROM 
                        `asesmen_capkom` ac
                        LEFT JOIN siswa s ON ac.id_siswa = s.id_siswa
                        LEFT JOIN capaian_kompetensi ck ON ac.id_ck = ck.id_ck  
                        LEFT JOIN mapel m ON ck.id_mapel = m.id_mapel
                        WHERE
                        `id_tahun_ajar` = '$idTahunAjar' AND
                        `semester` = '$semester' AND
                        `kelas` = '$kelas' AND
                        ck.id_mapel = '$idMapel' AND
                        `tampil`= 1
                        ORDER BY ac.id_siswa, ck.id_ck                        
                        ;";                        

                        $asesmenCk = mysqli_query($conn, $queryAsesmenCk);

                        $nomorSiswa = 0; // Inisialisasi nomor siswa
                        $currentSiswa = '';                        

                        echo '<form method="post" action="">';
                        while ($rowAsesmenCk = mysqli_fetch_assoc($asesmenCk)) {
                            $idSiswa = $rowAsesmenCk['id_siswa'];
                            $idCk = $rowAsesmenCk['id_ck'];
                            $siswa = $rowAsesmenCk['nama'];
                            $deskripsi = $rowAsesmenCk['deskripsi'];
                            $deskripsi = ucfirst($deskripsi);
                            $capaian = $rowAsesmenCk['capaian'];

                            echo '<input type="hidden" name="id_siswa[]" value="'.$idSiswa.'">';
                            // Jika siswa berubah, update nomor siswa dan tampilkan header siswa
                            if ($siswa != $currentSiswa) {
                                $currentSiswa = $siswa;
                                $nomorSiswa++;

                                // Tampilkan header siswa
                                echo '<br>';
                                echo '<h5>'.$nomorSiswa.'. '.$siswa.' :</h5>';
                            }

                            // Menentukan status checklist berdasarkan nilai capaian
                            $checked = ($capaian == 1) ? 'checked' : '';

                            // Menampilkan deskripsi dan checklist
                            echo '<div style="font-size: 1.1em;" class="form-check form-switch">';
                            echo '<input type="hidden" name="capaian['.$idSiswa.']['.$idCk.']" value="0">';

                            // Ubah checkbox agar menggunakan array pada nama                        
                            echo '<input class="form-check-input" type="checkbox" id="checkbox_'.$idCk.'" name="capaian['.$idSiswa.']['.$idCk.']" value="1" '.($capaian == 1 ? 'checked' : '').'>'.$deskripsi.'.';

                            echo '</div>';                        
                        }
                        echo '<br>';
                        echo '<br>';
                        echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                        echo '<input type="hidden" name="idMapel" value="' . $idMapel . '">';
                        echo '<input type="hidden" name="semester" value="' . $semester . '">';
                        echo '<button type="submit" class="btn btn-primary" name="btnSimpanAsesmen" id="btnSimpanAsesmen">Simpan</button>';               
                        echo '</div>';
                        echo '</form>'; 
                        echo '<br>';
                        echo '<br>';

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
