<?php
$conn = mysqli_connect("localhost:3306","root","","sdk");

$bulanIni = date('F');
if ($bulanIni == 'January') {
    $bulanIni = 'Januari';
} elseif ($bulanIni == 'February') {
    $bulanIni = 'Februari';
} elseif ($bulanIni == 'March') {
    $bulanLalu = 'Maret';
} elseif ($bulanIni == 'April') {
    $bulanIni = 'April';
} elseif ($bulanIni == 'May') {
    $bulanIni = 'Mei';
} elseif ($bulanIni == 'June') {
    $bulanIni = 'Juni';
} elseif ($bulanIni == 'July') {
    $bulanIni = 'Juli';
} elseif ($bulanIni == 'August') {
    $bulanIni = 'Agustus';
} elseif ($bulanIni == 'September') {
    $bulanIni = 'September';
} elseif ($bulanIni == 'October') {
    $bulanIni = 'Oktober';
} elseif ($bulanIni == 'November') {
    $bulanIni = 'November';
} elseif ($bulanIni == 'December') {
    $bulanIni = 'Desember';
} else {
    $bulanIni = 'Bulan Tidak valid';
}

?>