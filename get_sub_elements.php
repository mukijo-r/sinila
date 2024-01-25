<?php
// Include your database connection file
include 'config.php';

$idElemen = $_GET['elemen'];

$query = "SELECT id_sub_elemen, sub_elemen FROM p5_sub_elemen WHERE id_elemen = $idElemen";
$result = mysqli_query($conn, $query);

$dataSubElemen = array();
while ($row = mysqli_fetch_assoc($result)) {
    $dataSubElemen[] = $row;
}

// Mengembalikan data dalam format JSON
echo json_encode($dataSubElemen);
?>
