<?php
// Include your database connection file
include 'config.php';

// Ambil nilai dimensi yang dikirimkan melalui parameter GET
$idDimensi = $_GET['dimensi'];

$query = "SELECT id_elemen, elemen FROM p5_elemen WHERE id_dimensi = $idDimensi";
$result = mysqli_query($conn, $query);

$dataElemen = array();
while ($row = mysqli_fetch_assoc($result)) {
    $dataElemen[] = $row;
}

// Mengembalikan data dalam format JSON
echo json_encode($dataElemen);
?>
