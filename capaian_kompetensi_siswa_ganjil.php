<?php
require 'function.php';
require 'cek.php';
require 'config.php';
$conn = mysqli_connect("localhost:3306","root","","sdk");
$semester = 'Ganjil';

if ($kelas == 1 | $kelas == 2) {
    $fase = 'A';
} elseif ($kelas == 3 | $kelas == 4) {
    $fase = 'B';
} elseif ($kelas == 5 | $kelas == 6){
    $fase = 'C';
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
                            <li class="breadcrumb-item active">Asesmen Kualitatif / Asesmen Siswa <?=$fase;?></li>    
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
                                                // Ambil data kelas dari tabel kelas
                                                $queryMapel = mysqli_query($conn, "SELECT id_mapel, mapel FROM mapel");
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
                                        unset($_SESSION['flash_message']); // Hapus pesan flash setelah ditampilkan
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
                    // Tampilkan Daftar Nilai
                    if (isset($_POST['btnTampilLapSiswa'])) {                        
                        $idMapel = $_POST['mapel'];
                
                        $queryMapel = mysqli_query($conn, "SELECT mapel FROM mapel WHERE id_mapel='$idMapel'");
                        $rowMapel = mysqli_fetch_assoc($queryMapel);
                        $namaMapel = $rowMapel['mapel'];
                
                        ?><br>
                        <div class="row">
                            <h5>Pilih deskripsi sesuai capaian kompetensi setiap siswa :</h5>
                        </div>
                        <?php

                        capaianKompetensi($fase, $idMapel);
                    }               
                    ?>
                </div>
                <?php
                function capaianKompetensi($fase, $idMapel) {
                    $conn = mysqli_connect("localhost:3306", "root", "", "sdk");

                    $queryCk = "SELECT
                        ck.id_siswa,
                        s.nama,
                        ck.id_asesmen,
                        a.deskripsi,
                        ck.capaian
                        FROM 
                        `capaian_kompetensi` ck
                        LEFT JOIN asesmen a ON ck.id_asesmen = a.id_asesmen  
                        LEFT JOIN siswa s ON ck.id_siswa = s.id_siswa                                  
                        WHERE
                        s.id_kelas = '$kelas' AND
                        a.id_kelas = '$kelas' AND
                        a.id_mapel = '$idMapel'  
                        ORDER BY ck.id_siswa, ck.id_asesmen ASC;";

                    $capaianKompetensi = mysqli_query($conn, $queryCk);

                    $nomorSiswa = 0; // Inisialisasi nomor siswa
                    $currentSiswa = '';

                    echo '<form method="post" action="">';
                    while ($rowCapKom = mysqli_fetch_assoc($capaianKompetensi)) {
                        $idSiswa = $rowCapKom['id_siswa'];
                        $idAsesmen = $rowCapKom['id_asesmen'];
                        $siswa = $rowCapKom['nama'];
                        $deskripsi = $rowCapKom['deskripsi'];
                        $deskripsi = ucfirst($deskripsi);
                        $capaian = $rowCapKom['capaian'];

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
                        echo '<input type="hidden" name="capaian['.$idSiswa.']['.$idAsesmen.']" value="0">';

                        // Ubah checkbox agar menggunakan array pada nama                        
                        echo '<input class="form-check-input" type="checkbox" id="checkbox_'.$idAsesmen.'" name="capaian['.$idSiswa.']['.$idAsesmen.']" value="1" '.($capaian == 1 ? 'checked' : '').'>'.$deskripsi.'.';

                        echo '</div>';                        
                    }
                    echo '<br>';
                    echo '<br>';
                    echo '<div style="text-align: center;" class="sb-sidenav-footer">';
                    echo '<button type="submit" class="btn btn-primary" name="btnSimpanCapKom" id="btnSimpanCapKom">Simpan</button>';               
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
        <script>
            // Ambil referensi ke elemen-elemen dropdown
            var tahunAjarDropdown = document.getElementById('tahunAjar');
            var bulanDropdown = document.getElementById('bulan');
            var kategoriDropdown = document.getElementById('kategori');
            var tampilkanButton = document.getElementById('btnTampilLapUmum');

            // Tambahkan event listener ke setiap dropdown
            tahunAjarDropdown.addEventListener('change', checkDropdowns);
            bulanDropdown.addEventListener('change', checkDropdowns);
            kategoriDropdown.addEventListener('change', checkDropdowns);

            // Fungsi untuk memeriksa setiap dropdown
            function checkDropdowns() {
                if (tahunAjarDropdown.value !== '' && bulanDropdown.value !== '' && kategoriDropdown.value !== '') {
                    tampilkanButton.disabled = false;
                } else {
                    tampilkanButton.disabled = true;
                }
            }
        </script>
    
    </body>
</html>
