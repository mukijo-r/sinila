<?php
include 'config.php';

$conn = mysqli_connect("localhost:3306","root","","sdk");


//Tambah Tahun Ajar
if(isset($_POST['tambahTahunAjar'])){
    $tahun_ajar = $_POST['newTahunAjar'];
    $insertTahunAjar = mysqli_query($conn, "INSERT INTO tahun_ajar (tahun_ajar) VALUES ('$tahun_ajar')");
}

//Ubah Tahun Ajar
if(isset($_POST['ubahTahunAjar'])){
    $tahun_ajar = $_POST['tahunAjar'];

    $queryTahunAjar = mysqli_query($conn, "SELECT id_tahun_ajar FROM tahun_ajar WHERE tahun_ajar = '$tahun_ajar'");
    $dataTahunAjar = mysqli_fetch_assoc($queryTahunAjar);
    $idTahunAjar = $dataTahunAjar['id_tahun_ajar'];
    $ubahTahunAjar = mysqli_query($conn, "UPDATE tahun_ajar_default set id_tahun_ajar='$idTahunAjar'");
}


?>