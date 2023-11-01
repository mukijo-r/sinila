<?php
include 'config.php';

$conn = mysqli_connect("localhost:3306","root","","sdk");

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