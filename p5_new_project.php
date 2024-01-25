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
                        <h4>Buat Project Baru</h4>
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
                            <!-- Kolom 1 -->
                            <div class="row">                                
                                <div class="col-md-3">
                                    <label for="dimensi1">Dimensi 1 :</label>
                                    <select class="form-select" name="dimensi1" id="dimensi1" aria-label="dimensi1" required>
                                        <option value="">Pilih Dimensi</option>
                                        <?php
                                        $queryDimensi = mysqli_query($conn, "SELECT id_dimensi, dimensi FROM p5_dimensi");
                                        while ($rowDimensi = mysqli_fetch_assoc($queryDimensi)) {
                                            echo '<option value="' . $rowDimensi['id_dimensi'] . '">' . $rowDimensi['dimensi'] . '</option>';
                                        }
                                        ?>
                                    </select><br>
                                    <label for="elemen1">Elemen 1 :</label>
                                    <select class="form-select" name="elemen1" id="elemen1" aria-label="elemen1" required>
                                        <option value="">Pilih Elemen</option>                                       
                                    </select><br>
                                    <label for="subElemen1">Sub Elemen 1 :</label>
                                    <select class="form-select" name="subElemen1" id="subElemen1" aria-label="subElemen1" required>
                                        <option value="">Pilih Sub Elemen</option>
                                    </select><br>
                                    <label for="capaian1">Capaian :</label>
                                    <textarea name="capaian1" id="capaian1" rows="7" class="form-control" readonly></textarea>
                                    <input type="hidden" name="id_capaian1" id="id_capaian1" value="">                               
                                </div>
                                <!-- Kolom 2 -->                                
                                <div class="col-md-3">  
                                    <label for="dimensi2">Dimensi 2 :</label>
                                    <select class="form-select" name="dimensi2" id="dimensi2" aria-label="dimensi2" required>
                                        <option value="">Pilih Dimensi</option>
                                        <?php
                                        $queryDimensi = mysqli_query($conn, "SELECT id_dimensi, dimensi FROM p5_dimensi");
                                        while ($rowDimensi = mysqli_fetch_assoc($queryDimensi)) {
                                            echo '<option value="' . $rowDimensi['id_dimensi'] . '">' . $rowDimensi['dimensi'] . '</option>';
                                        }
                                        ?>
                                    </select><br>
                                    <label for="elemen2">Elemen 2 :</label>
                                    <select class="form-select" name="elemen2" id="elemen2" aria-label="elemen2" required>
                                        <option value="">Pilih Elemen</option>                                       
                                    </select><br>
                                    <label for="subElemen2">Sub Elemen 2 :</label>
                                    <select class="form-select" name="subElemen2" id="subElemen2" aria-label="subElemen2" required>
                                        <option value="">Pilih Sub Elemen</option>
                                    </select><br>
                                    <label for="capaian2">Capaian 2:</label>
                                    <textarea name="capaian2" id="capaian2" rows="7" class="form-control" readonly></textarea>  
                                    <input type="hidden" name="id_capaian2" id="id_capaian2" value=""> 
                                </div>
                                <!-- Kolom 3 -->
                                <div class="col-md-3">  
                                    <label for="dimensi3">Dimensi 3 :</label>
                                    <select class="form-select" name="dimensi3" id="dimensi3" aria-label="dimensi3" required>
                                        <option value="">Pilih Dimensi</option>
                                        <?php
                                        $queryDimensi = mysqli_query($conn, "SELECT id_dimensi, dimensi FROM p5_dimensi");
                                        while ($rowDimensi = mysqli_fetch_assoc($queryDimensi)) {
                                            echo '<option value="' . $rowDimensi['id_dimensi'] . '">' . $rowDimensi['dimensi'] . '</option>';
                                        }
                                        ?>
                                    </select><br>
                                    <label for="elemen3">Elemen 3 :</label>
                                    <select class="form-select" name="elemen3" id="elemen3" aria-label="elemen3" required>
                                        <option value="">Pilih Elemen</option>                                       
                                    </select><br>
                                    <label for="subElemen3">Sub Elemen 3 :</label>
                                    <select class="form-select" name="subElemen3" id="subElemen3" aria-label="subElemen3" required>
                                        <option value="">Pilih Sub Elemen</option>
                                    </select><br>
                                    <label for="capaian3">Capaian 3:</label>
                                    <textarea name="capaian3" id="capaian3" rows="7" class="form-control" readonly></textarea>
                                    <input type="hidden" name="id_capaian3" id="id_capaian3" value="">   
                                </div>
                                <!-- Kolom 4 -->
                                <div class="col-md-3">  
                                    <label for="dimensi4">Dimensi 4 :</label>
                                    <select class="form-select" name="dimensi4" id="dimensi4" aria-label="dimensi4" required>
                                        <option value="">Pilih Dimensi</option>
                                        <?php
                                        $queryDimensi = mysqli_query($conn, "SELECT id_dimensi, dimensi FROM p5_dimensi");
                                        while ($rowDimensi = mysqli_fetch_assoc($queryDimensi)) {
                                            echo '<option value="' . $rowDimensi['id_dimensi'] . '">' . $rowDimensi['dimensi'] . '</option>';
                                        }
                                        ?>
                                    </select><br>
                                    <label for="elemen4">Elemen 4 :</label>
                                    <select class="form-select" name="elemen4" id="elemen4" aria-label="elemen4" required>
                                        <option value="">Pilih Elemen</option>                                       
                                    </select><br>
                                    <label for="subElemen4">Sub Elemen 4 :</label>
                                    <select class="form-select" name="subElemen4" id="subElemen4" aria-label="subElemen4" required>
                                        <option value="">Pilih Sub Elemen</option>
                                    </select><br>
                                    <label for="capaian4">Capaian 4 :</label>
                                    <textarea name="capaian4" id="capaian4" rows="7" class="form-control" readonly></textarea>  
                                    <input type="hidden" name="id_capaian4" id="id_capaian4" value=""> 
                                </div>
                            </div><br><br>
                            <div style="text-align: center;" class="sb-sidenav-footer">
                                <input type="hidden" name="namaUser" id="namaUser" value="<?=$namaUser;?>"> 
                                <button type="submit" class="btn btn-primary" name="btnSimpanProject" id="btnSimpanProject">Simpan</button>
                            </div><br><br>
                        </form>
                        
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
    document.addEventListener('DOMContentLoaded', function() {
        var dimensi1Dropdown = document.getElementById('dimensi1');
        var elemen1Dropdown = document.getElementById('elemen1');
        var subElemen1Dropdown = document.getElementById('subElemen1');
        var capaian1Input = document.getElementById('capaian1');
        var dimensi2Dropdown = document.getElementById('dimensi2');
        var elemen2Dropdown = document.getElementById('elemen2');
        var subElemen2Dropdown = document.getElementById('subElemen2');
        var capaian2Input = document.getElementById('capaian2');
        var dimensi3Dropdown = document.getElementById('dimensi3');
        var elemen3Dropdown = document.getElementById('elemen3');
        var subElemen3Dropdown = document.getElementById('subElemen3');
        var capaian3Input = document.getElementById('capaian3');
        var dimensi4Dropdown = document.getElementById('dimensi4');
        var elemen4Dropdown = document.getElementById('elemen4');
        var subElemen4Dropdown = document.getElementById('subElemen4');
        var capaian4Input = document.getElementById('capaian4');

        var fase = '<?php echo json_encode($fase); ?>';

        dimensi1Dropdown.addEventListener('change', function() {
            var selectedDimensi1 = dimensi1Dropdown.value;        

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_elements.php?dimensi=' + selectedDimensi1, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataElemen1 = JSON.parse(xhr.responseText);                    

                    elemen1Dropdown.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataElemen1.forEach(function(elemen) {
                        elemen1Dropdown.innerHTML += '<option value="' + elemen.id_elemen + '">' + elemen.elemen + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        dimensi2Dropdown.addEventListener('change', function() {
            var selectedDimensi2 = dimensi2Dropdown.value;        

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_elements.php?dimensi=' + selectedDimensi2, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataElemen2 = JSON.parse(xhr.responseText);                    

                    elemen2Dropdown.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataElemen2.forEach(function(elemen) {
                        elemen2Dropdown.innerHTML += '<option value="' + elemen.id_elemen + '">' + elemen.elemen + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        dimensi3Dropdown.addEventListener('change', function() {
            var selectedDimensi3 = dimensi3Dropdown.value;        

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_elements.php?dimensi=' + selectedDimensi3, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataElemen3 = JSON.parse(xhr.responseText);                    

                    elemen3Dropdown.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataElemen3.forEach(function(elemen) {
                        elemen3Dropdown.innerHTML += '<option value="' + elemen.id_elemen + '">' + elemen.elemen + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        dimensi4Dropdown.addEventListener('change', function() {
            var selectedDimensi4 = dimensi4Dropdown.value;        

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_elements.php?dimensi=' + selectedDimensi4, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataElemen4 = JSON.parse(xhr.responseText);                    

                    elemen4Dropdown.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataElemen4.forEach(function(elemen) {
                        elemen4Dropdown.innerHTML += '<option value="' + elemen.id_elemen + '">' + elemen.elemen + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        elemen1Dropdown.addEventListener('change', function() {
            var selectedElemen1 = elemen1Dropdown.value;        

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_sub_elements.php?elemen=' + selectedElemen1, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataSubElemen1 = JSON.parse(xhr.responseText);                    

                    subElemen1Dropdown.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataSubElemen1.forEach(function(subElemen) {
                        subElemen1Dropdown.innerHTML += '<option value="' + subElemen.id_sub_elemen + '">' + subElemen.sub_elemen + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        elemen2Dropdown.addEventListener('change', function() {
            var selectedElemen2 = elemen2Dropdown.value;        

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_sub_elements.php?elemen=' + selectedElemen2, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataSubElemen2 = JSON.parse(xhr.responseText);                    

                    subElemen2Dropdown.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataSubElemen2.forEach(function(subElemen) {
                        subElemen2Dropdown.innerHTML += '<option value="' + subElemen.id_sub_elemen + '">' + subElemen.sub_elemen + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        elemen3Dropdown.addEventListener('change', function() {
            var selectedElemen3 = elemen3Dropdown.value;        

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_sub_elements.php?elemen=' + selectedElemen3, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataSubElemen3 = JSON.parse(xhr.responseText);                    

                    subElemen3Dropdown.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataSubElemen3.forEach(function(subElemen) {
                        subElemen3Dropdown.innerHTML += '<option value="' + subElemen.id_sub_elemen + '">' + subElemen.sub_elemen + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        elemen4Dropdown.addEventListener('change', function() {
            var selectedElemen4 = elemen4Dropdown.value;        

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_sub_elements.php?elemen=' + selectedElemen4, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Parse data JSON yang diterima
                    var dataSubElemen4 = JSON.parse(xhr.responseText);                    

                    subElemen4Dropdown.innerHTML = '<option selected disabled>Pilih Elemen</option>';
                    dataSubElemen4.forEach(function(subElemen) {
                        subElemen4Dropdown.innerHTML += '<option value="' + subElemen.id_sub_elemen + '">' + subElemen.sub_elemen + '</option>';                        
                    });                               
                }
            };
            xhr.send();
        });

        subElemen1Dropdown.addEventListener('change', function() {
        updateCapaian1Value();            
        });

        subElemen2Dropdown.addEventListener('change', function() {
        updateCapaian2Value();            
        });

        subElemen3Dropdown.addEventListener('change', function() {
        updateCapaian3Value();            
        });

        subElemen4Dropdown.addEventListener('change', function() {
        updateCapaian4Value();            
        });

        // Fungsi untuk mengambil nilai capaian yang sesuai
        function updateCapaian1Value() {
            var selectedsubElemen1 = subElemen1Dropdown.value;

            // Lakukan AJAX request untuk mengambil nilai capaian
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_capaian.php?subElemen=' + selectedsubElemen1 + '&fase=' + fase, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var capaianData = JSON.parse(xhr.responseText);

                    if (capaianData.length > 0) {
                        var capaian1Value = capaianData[0].capaian;
                        var idCapaian1Value = capaianData[0].id_capaian; 

                        capaian1Input.value = capaian1Value;
                        document.getElementById('id_capaian1').value = idCapaian1Value; 
                    } else {
                        console.error('Empty or invalid response from get_capaian.php');
                    }
                }
            };
            xhr.send();
        }

        // Fungsi untuk mengambil nilai capaian yang sesuai
        function updateCapaian2Value() {
            var selectedsubElemen2 = subElemen2Dropdown.value;

            // Lakukan AJAX request untuk mengambil nilai capaian
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_capaian.php?subElemen=' + selectedsubElemen2 + '&fase=' + fase, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var capaianData = JSON.parse(xhr.responseText);

                    if (capaianData.length > 0) {
                        var capaian2Value = capaianData[0].capaian;
                        var idCapaian2Value = capaianData[0].id_capaian; 

                        capaian2Input.value = capaian2Value;
                        document.getElementById('id_capaian2').value = idCapaian2Value; 
                    } else {
                        console.error('Empty or invalid response from get_capaian.php');
                    }
                }
            };
            xhr.send();
        }

        // Fungsi untuk mengambil nilai capaian yang sesuai
        function updateCapaian3Value() {
            var selectedsubElemen3 = subElemen3Dropdown.value;

            // Lakukan AJAX request untuk mengambil nilai capaian
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_capaian.php?subElemen=' + selectedsubElemen3 + '&fase=' + fase, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var capaianData = JSON.parse(xhr.responseText);

                    if (capaianData.length > 0) {
                        var capaian3Value = capaianData[0].capaian;
                        var idCapaian3Value = capaianData[0].id_capaian; 

                        capaian3Input.value = capaian3Value;
                        document.getElementById('id_capaian3').value = idCapaian3Value; 
                    } else {
                        console.error('Empty or invalid response from get_capaian.php');
                    }
                }
            };
            xhr.send();
        }

        // Fungsi untuk mengambil nilai capaian yang sesuai
        function updateCapaian4Value() {
            var selectedsubElemen4 = subElemen4Dropdown.value;

            // Lakukan AJAX request untuk mengambil nilai capaian
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_capaian.php?subElemen=' + selectedsubElemen4 + '&fase=' + fase, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var capaianData = JSON.parse(xhr.responseText);

                    if (capaianData.length > 0) {
                        var capaian4Value = capaianData[0].capaian;
                        var idCapaian4Value = capaianData[0].id_capaian; 

                        capaian4Input.value = capaian4Value;
                        document.getElementById('id_capaian4').value = idCapaian4Value; 
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


