<?php
include 'config.php';

$idSubElemen = $_GET['subElemen'];
$fase = $_GET['fase'];

// Debugging statements
error_log('Received subElemen: ' . $idSubElemen . ', fase: ' . $fase);

$query = "SELECT id_capaian, capaian FROM p5_capaian WHERE id_sub_elemen = $idSubElemen AND fase = $fase";
$result = mysqli_query($conn, $query);

$dataCapaian = array();
while ($row = mysqli_fetch_assoc($result)) {
    $dataCapaian[] = $row;
}

// Debugging statements
error_log('Data Capaian: ' . json_encode($dataCapaian));
file_put_contents('debug_log.txt', 'Data Capaian: ' . print_r($dataCapaian, true));

echo json_encode($dataCapaian);
?>
