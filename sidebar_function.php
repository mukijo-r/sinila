<?php
include 'config.php';

$conn = mysqli_connect("localhost:3306","root","","sdk");

//Tambah Tahun Ajar
if(isset($_POST['tambahTahunAjar'])){
    $tahun_ajar = $_POST['newTahunAjar'];
    $insertTahunAjar = false;

    // Lakukan pemeriksaan panjang dan format sebelum menjalankan kueri INSERT
    if (strlen($tahun_ajar) == 9 && strpos($tahun_ajar, '/') === 4 && strpos($tahun_ajar, '/') === 4) {
        $insertTahunAjar = mysqli_query($conn, "INSERT INTO tahun_ajar (tahun_ajar) VALUES ('$tahun_ajar')");
    }

    $checkTahunAjar = "SELECT * FROM tahun_ajar WHERE tahun_ajar = '$tahun_ajar'";
    $checkTahunAjarResult = mysqli_query($conn, $checkTahunAjar);

    $sweetAlert = "";
    if ($insertTahunAjar) {
        $sweetAlert = "Swal.fire({
            title: 'Sukses!',
            text: 'Tambah Tahun Ajar baru berhasil.',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
        });";
    } else {
        $sweetAlert = "Swal.fire({
            title: 'Gagal!',
            text: 'Tambah Tahun Ajar baru gagal.',
            icon: 'error',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
        });";
    }
}

//Ubah Kelas
if(isset($_POST['ubahKelas'])){
    $newKelas = $_POST['kelas'];
    $_SESSION['kelas'] = $newKelas;
}

//Ubah Tahun Ajar
if(isset($_POST['ubahTahunAjar'])){
    $tahun_ajar = $_POST['tahunAjar'];
    $_SESSION['tahunAjar'] = $tahun_ajar;
}

//Simpan Backup
if(isset($_POST['simpanBackup'])){
    // $nama_file = $_POST['fileBackup'];

    // // Lakukan pemeriksaan panjang dan format sebelum menjalankan kueri
    // if (strlen($nama_file) >= 1) {
        $database = 'sdk';
        $user = 'root';
        $pass = '';
        $host = 'localhost';
        $charset = "utf8mb4"; # utf8mb4_unicode_ci 

        $conn = new mysqli($host, $user, $pass, $database);
        $conn->set_charset($charset);

        # get all tables
        $result = mysqli_query($conn, "SHOW TABLES");
        $tables = array();

        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }

        # Get tables data 
        $sqlScript = "";
        foreach ($tables as $table) {
            $query = "SHOW CREATE TABLE $table";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_row($result);
            
            $sqlScript .= "\n\n" . $row[1] . ";\n\n";
            
            
            $query = "SELECT * FROM $table";
            $result = mysqli_query($conn, $query);
            
            $columnCount = mysqli_num_fields($result);
            
            for ($i = 0; $i < $columnCount; $i ++) {
                while ($row = mysqli_fetch_row($result)) {
                    $sqlScript .= "INSERT INTO $table VALUES(";
                    for ($j = 0; $j < $columnCount; $j ++) {
                        $row[$j] = $row[$j];
                        
                        $sqlScript .= (isset($row[$j])) ? '"' . $row[$j] . '"' : '""';

                        if ($j < ($columnCount - 1)) {
                            $sqlScript .= ',';
                        }

                    }
                    $sqlScript .= ");\n";
                }
            }
            
            $sqlScript .= "\n"; 
        }

        //save file
        // Tentukan folder tempat penyimpanan backup (misalnya di /var/backups/)
        $folder = 'C:/var/backups/';  // Ganti dengan folder yang diinginkan

        // Periksa apakah folder ada, jika belum, buat folder tersebut
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);  // Membuat folder jika belum ada
        }

        // Generate nama file backup dengan timestamp
        $time = time();

        // Tentukan path lengkap untuk file backup
        $file_path = $folder . 'backup_' . time() . '.sql';

        // Simpan file backup ke dalam folder tersebut
        $mysql_file = fopen($file_path, 'w+');
        fwrite($mysql_file, $sqlScript);
        fclose($mysql_file);

        $sweetAlert = "";
        if ($mysql_file) {
            $sweetAlert = "Swal.fire({
                title: 'Sukses!',
                text: 'File backup disimpan di C:/var/backups/',
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });";
        } else {
            $sweetAlert = "Swal.fire({
                title: 'Gagal!',
                text: 'Simpan backup gagal.',
                icon: 'error',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });";
        }

    // }
}


?>