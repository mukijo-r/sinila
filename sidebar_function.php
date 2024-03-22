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


?>