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
                            <li class="breadcrumb-item active">PROSES / Project / Daftar Project</li>                            
                        </ol><br>
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
                                </div>
                            </div>
                            <?php 
                                if ($kelas == 1 | $kelas == 2) { 
                                    $fase = 'A';
                                } elseif ($kelas == 3 | $kelas == 4) {
                                    $fase = 'B';
                                } elseif ($kelas == 5 | $kelas == 6) {
                                    $fase = 'C';
                                }
                            ?>
                        </div>                    
                        <hr>
                        <?php

                        if(isset($_POST['tambahProject'])){
                            $jumlahDimensi = $_POST['dimensi'];

                            if ($jumlahDimensi == 1) {
                                $class = 'col-md-6';
                            } elseif ($jumlahDimensi == 2) {
                                $class = 'col-md-6';
                            } elseif ($jumlahDimensi == 3) {
                                $class = 'col-md-4';
                            } elseif ($jumlahDimensi == 4) {
                                $class = 'col-md-3';
                            } elseif ($jumlahDimensi == 5) {
                                $class = 'col-md-2';
                            } elseif ($jumlahDimensi == 6) {
                                $class = 'col-md-2';
                            }

                            echo '<h4>Buat Project Baru</h4>
                                    <h5>Isi komponen yang diperlukan :</h5><br>                                    
                                    <form method="post">
                                        <div class="mb-3">
                                            <label for="namaProject">Nama Project :</label>
                                            <input type="text" name="namaProject" class="form-control col-6" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="deskripsiProject">Deskripsi Project :</label>
                                            <textarea name="deskripsiProject" class="form-control col-12" rows="5" required></textarea>
                                        </div>
                                        <h5>Pilih ' . $jumlahDimensi . ' Dimensi:</h5>
                                        <div class="row">'; 
                                            for ($d=1;$d<=$jumlahDimensi;$d++){
                                                echo '<div class="' . $class .'">
                                                        <label for="dimensi' . $d . '">Dimensi ' . $d . ':</label>
                                                        <select class="form-select" name="dimensi' . $d . '" id="dimensi' . $d . '" aria-label="dimensi' . $d . '">
                                                            <option value="">Pilih Dimensi</option>';

                                                            $queryDimensi = mysqli_query($conn, "SELECT id_dimensi, dimensi FROM p5_dimensi");
                                                            while ($rowDimensi = mysqli_fetch_assoc($queryDimensi)) {
                                                                echo '<option value="' . $rowDimensi['id_dimensi'] . '">' . $rowDimensi['dimensi'] . '</option>';
                                                            }

                                                        echo '</select><br>
                                                        <label for="elemen' . $d . '">Elemen ' . $d . ' :</label>
                                                        <select class="form-select" name="elemen' . $d . '" id="elemen' . $d . '" aria-label="elemen' . $d . '">
                                                            <option value="">Pilih Elemen</option>                                       
                                                        </select><br>
                                                        <label for="subElemen' . $d . '">Sub Elemen ' . $d . ' :</label>
                                                        <select class="form-select" name="subElemen' . $d . '" id="subElemen' . $d . '" aria-label="subElemen' . $d . '">
                                                            <option value="">Pilih Sub Elemen</option>
                                                        </select><br>
                                                        <label for="capaian' . $d . '">Capaian :</label>
                                                        <textarea name="capaian' . $d . '" id="capaian' . $d . '" rows="7" class="form-control" readonly></textarea>
                                                        <input type="hidden" name="id_capaian' . $d . '" id="id_capaian' . $d . '" value="">                               
                                                    </div>';
                                            }    

                                echo    '</div><br><br>
                                            <div style="text-align: center;" class="sb-sidenav-footer">
                                                <input type="hidden" name="jumlahDimensi" id="jumlahDimensi" value="' . $jumlahDimensi . '"> 
                                                <input type="hidden" name="namaUser" id="namaUser" value="' . $namaUser . '"> 
                                                <button type="submit" class="btn btn-primary" name="btnSimpanProject" id="btnSimpanProject">Simpan</button>
                                            </div><br><br>
                                    </form>';
                        }
                        ?>
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    var fase = '<?php echo json_encode($fase); ?>';
    var dropdowns = [];
    var capaianInputs = [];

    for (var i = 1; i <= 6; i++) {
        dropdowns[i] = {
            dimensi: document.getElementById('dimensi' + i),
            elemen: document.getElementById('elemen' + i),
            subElemen: document.getElementById('subElemen' + i)
        };

        capaianInputs[i] = document.getElementById('capaian' + i);

        dropdowns[i].dimensi.addEventListener('change', createDropdownChangeListener(i));
        dropdowns[i].elemen.addEventListener('change', createElemenDropdownChangeListener(i));
        dropdowns[i].subElemen.addEventListener('change', createSubElemenDropdownChangeListener(i));
    }

    function createDropdownChangeListener(index) {
        return function () {
            var selectedDimensi = dropdowns[index].dimensi.value;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_elements.php?dimensi=' + selectedDimensi, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var dataElemen = JSON.parse(xhr.responseText);

                    dropdowns[index].elemen.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataElemen.forEach(function (elemen) {
                        dropdowns[index].elemen.innerHTML += '<option value="' + elemen.id_elemen + '">' + elemen.elemen + '</option>';
                    });
                }
            };
            xhr.send();
        };
    }

    function createElemenDropdownChangeListener(index) {
        return function () {
            var selectedElemen = dropdowns[index].elemen.value;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_sub_elements.php?elemen=' + selectedElemen, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var dataSubElemen = JSON.parse(xhr.responseText);

                    dropdowns[index].subElemen.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataSubElemen.forEach(function (subElemen) {
                        dropdowns[index].subElemen.innerHTML += '<option value="' + subElemen.id_sub_elemen + '">' + subElemen.sub_elemen + '</option>';
                    });
                }
            };
            xhr.send();
        };
    }

    function createSubElemenDropdownChangeListener(index) {
        return function () {
            updateCapaianValue(index);
        };
    }

    function updateCapaianValue(index) {
        var selectedSubElemen = dropdowns[index].subElemen.value;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_capaian.php?subElemen=' + selectedSubElemen + '&fase=' + fase, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var capaianData = JSON.parse(xhr.responseText);

                if (capaianData.length > 0) {
                    var capaianValue = capaianData[0].capaian;
                    var idCapaianValue = capaianData[0].id_capaian;

                    capaianInputs[index].value = capaianValue;
                    document.getElementById('id_capaian' + index).value = idCapaianValue;
                } else {
                    console.error('Empty or invalid response from get_capaian.php');
                }
            }
        };
        xhr.send();
    }
});

</script>

</html>


